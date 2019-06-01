<?php

/**
 * Created by IntelliJ IDEA.
 * User: gpapkala
 * Date: 28.11.2017
 * Time: 15:21
 */
class Meveto_OAuth_Public
{
    /**
     * The ID of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $plugin_name The ID of this plugin.
     */
    private $plugin_name;
    /**
     * The version of this plugin.
     *
     * @since    1.0.0
     * @access   private
     * @var      string $version The current version of this plugin.
     */
    private $version;
    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */
    private $allowed_actions = ['login', 'callback', '/callback', 'kill', '/meveto/login', '/meveto/callback', '/meveto/kill', '/meveto/login/', '/meveto/callback/', '/meveto/kill/'];

    /**
     * Meveto_OAuth_Public constructor.
     * @param $plugin_name
     * @param $version
     */
    public function __construct($plugin_name, $version)
    {
        $this->plugin_name = $plugin_name;
        $this->version = $version;
    }

    public function enqueue_styles()
    {
    }

    public function enqueue_scripts()
    {

    }

    public function add_endpoints()
    {
        add_rewrite_endpoint('meveto', EP_ROOT);
    }

    public function process_meveto_login()
    {
        global $wp_query;
        if ($wp_query->is_main_query()) {
            $action = $wp_query->get('meveto');
            if ('' == $action) {
                $action = $_SERVER['REQUEST_URI'];
            }
            if (in_array($action, $this->allowed_actions)) {
                switch ($action) {
                    case 'login':
                    case '/meveto/login':
                    case '/meveto/login/':
                        $this->action_login();
                        break;
                    case 'kill':
                    case '/meveto/kill':
                    case '/meveto/kill/':
                        $this->action_kill();
                        break;
                    case 'callback':
                    case '/callback':
                    case '/meveto/callback':
                    case '/meveto/callback/':
                        $this->action_callback();
                        break;
                }
            }
        }
    }

    private function action_login()
    {
        $redirect_url = sprintf("%s%s/%s",
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 || $_SERVER['X-Forwarded-Proto'] === 'https') ? "https://" : "http://",
            $_SERVER['HTTP_HOST'],
            'meveto/callback'
        );

        $authorize_query = http_build_query([
            'client_id' => get_option('meveto_oauth_client_id'),
            'scope' => get_option('meveto_oauth_scope'),
            'response_type' => 'code',
            'redirect_uri' => $redirect_url //home_url()."/meveto/callback"
        ]);

        $authorize_url = get_option('meveto_oauth_authorize_url') . '?' . $authorize_query;
        echo '<script type="text/javascript">console.log("Redirecting to authorization server")</script>';
        wp_redirect($authorize_url);
        exit;
    }

    private function action_callback()
    {
        echo '<script type="text/javascript">console.log("Call back hooked.")</script>';
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-meveto-oauth-handler.php';
        $redirect_url = sprintf("%s%s/%s",
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 || $_SERVER['X-Forwarded-Proto'] === 'https') ? "https://" : "http://",
            $_SERVER['HTTP_HOST'],
            'meveto/callback'
        );

        $handler = new Meveto_OAuth_Handler();
        $accessToken = $handler->get_access_token(get_option('meveto_oauth_token_url'), 'authorization_code',
            get_option('meveto_oauth_client_id'), get_option('meveto_oauth_client_secret'), $_GET['code'], $redirect_url);
        $email = $handler->get_resource_owner($accessToken,"https://auth.meveto.com/meveto-auth/user/briefinfo");
        $this->login_user($email);
    }

    private function login_user($email)
    {
        echo '<script type="text/javascript">console.log("Attempting logging the user into wordpress dashboard.")</script>';
        $user = get_user_by('login', $email);
        if (!$user)
            $user = get_user_by('email', $email);

        if ($user) {
            $user_id = $user->ID;
        } else {
            $random_password = wp_generate_password(10, false);
            $user_id = wp_create_user($email, $random_password, $email);
            $user = get_user_by('email', $email);
        }

        if ($user_id) {
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login);
            wp_redirect(home_url());
            echo '<script type="text/javascript">console.log("User logged into wordpress dashboard.")</script>';
            exit;
        }
    }

    private function action_kill()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'];
        $secret = $data['secret'];
        if ($secret === get_option('meveto_oauth_client_secret')) {
            $user = get_user_by('login', $email);
            if (!$user)
                $user = get_user_by('email', $email);

            if ($user) {
                $user_id = $user->ID;
                $sessions = WP_Session_Tokens::get_instance($user_id);
                $sessions->destroy_all();
                
                wp_send_json(['success' => true]);
                exit;
            }
        }
        wp_send_json(['success' => false]);
    }

}

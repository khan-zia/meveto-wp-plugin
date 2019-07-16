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
    private $allowed_actions = [
        'login',
        'callback',
        '/callback',
        'kill',
        '/meveto/login',
        '/meveto/callback',
        '/meveto/kill',
        '/meveto/login/',
        '/meveto/callback/',
        '/meveto/kill/',
        '/meveto/no-user',
        'no-user',
        '/no-user',
        '/meveto/connect',
        '/connect',
        'connect',
    ];

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
        wp_enqueue_style('meveto-no-user', plugin_dir_url(__FILE__) . '/css/no_user.css', []);
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
                $actionKey = substr($_SERVER['REQUEST_URI'], 8, 1);
                if ($actionKey == 'c') {
                    $action = 'callback';
                } else if ($actionKey == 'l') {
                    $action = 'login';
                } else if ($actionKey == 'k') {
                    $action = 'kill';
                } else {
                    $action = $_SERVER['REQUEST_URI'];
                }
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
                    case 'no-user':
                    case '/no-user':
                    case '/meveto/no-user':
                        $this->action_no_user();
                        break;
                    case 'connect':
                    case '/connect':
                    case '/meveto/connect':
                        $this->action_connect_to_meveto();
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
            'redirect_uri' =>  $redirect_url //"http://localhost/wordpress/meveto/callback"
        ]);

        $authorize_url = get_option('meveto_oauth_authorize_url') . '?' . $authorize_query;
        wp_redirect($authorize_url);
        exit;
    }

    private function action_callback()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-meveto-oauth-handler.php';
        $redirect_url = sprintf("%s%s/%s",
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 || $_SERVER['X-Forwarded-Proto'] === 'https') ? "https://" : "http://",
            $_SERVER['HTTP_HOST'],
            'meveto/callback'
        );
        // check whether authorization code was returned by the backend or not.
        if($_GET['code']) {
            $handler = new Meveto_OAuth_Handler();
            $accessToken = $handler->get_access_token(get_option('meveto_oauth_token_url'), 'authorization_code',
                get_option('meveto_oauth_client_id'), get_option('meveto_oauth_client_secret'), $_GET['code'], $redirect_url);
            $login_name = $handler->get_resource_owner($accessToken,"https://auth.meveto.com/meveto-auth/user/briefinfo");
            $this->login_user($login_name, $accessToken);
        } else {
            error_log("\n Authorization code not received",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
            // Authorization code was not returned.
            echo "We are sorry! Meveto could not authenticate your credentials. Meveto server responded with the following error/errors.";
            echo "<br/>";
            echo ($_GET['error']) ? $_GET['error'] : '';
            echo "<br/>";
            echo ($_GET['error_description']) ? $_GET['error_description'] : '';
        }
    }

    // $redirect_url
    // http://localhost/wordpress/meveto/callback
    // https://auth.meveto.com/meveto-auth/user/briefinfo
    // http://laravel.local/api/user

    private function login_user($email,$token = '')
    {
        $user = get_user_by('login', $email);
        if (!$user)
            $user = get_user_by('email', $email);

        if ($user) {
            $user_id = $user->ID;
        } else {
            error_log("\n login_user:".$email." user was not found on the WP. Connect to Meveto initiated.",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
            error_log("\n login_user:".$token."\n The above is authentication token.",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
            $redirect_to = home_url()."/meveto/no-user?token=".$token;
            error_log("\n login_user: redirecting: ".$redirect_to,3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
            wp_redirect($redirect_to); // redirect user to a synchronization page.
            exit();

            // $random_password = wp_generate_password(10, false);
            // $user_id = wp_create_user($email, $random_password, $email);
            // $user = get_user_by('email', $email);
        }
        if ($user_id) {
            wp_set_current_user($user_id);
            wp_set_auth_cookie($user_id);
            do_action('wp_login', $user->user_login);
            wp_redirect(home_url());
            exit();
        }
    }

    private function action_no_user() {
        include plugin_dir_path(dirname(__FILE__)) . 'public/partials/no_user.php';
        exit();
    }

    private function action_connect_to_meveto() {

        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-meveto-oauth-handler.php';
        $login_name = $_POST['login_name'];
        $login_password = $_POST['login_password'];
        $client_id = get_option('meveto_oauth_client_id');
        $access_token = $_GET['token'];

        $user = wp_authenticate($login_name, $login_password);



        if (is_wp_error($user) || $user == null || $user == false) {
            session_start();
            $_SESSION['meveto_error'] = "You have entered incorrect login credentials.";
            $redirect_to = home_url()."/meveto/no-user?token=".$access_token;
            wp_redirect($redirect_to);

        } else {
            // send connect action to Meveto's back-end.
            $handler = new Meveto_OAuth_Handler();
            $connect = $handler->connect_to_meveto($client_id, $login_name, $access_token);
            if($connect) {
                $this->login_user($login_name);
            } else {
                echo "Sorry! We could not connect your account to Meveto at the moment. Please try again later. If the issue persists, contact your website's owner or administrator";
                exit();
            }
        }
        exit();
    }

    private function action_kill()
    {
        $data = json_decode(file_get_contents("php://input"), true);
        $email = $data['email'];
        $secret = $data['secret'];
        //$secret === get_option('meveto_oauth_client_secret')
        if (true) {
            $user = wp_get_current_user(); //get_user_by('login', $email);
            //if (!$user)
                //$user = get_user_by('email', $email);

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

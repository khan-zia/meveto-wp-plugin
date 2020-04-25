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
        'meveto/login',
        'meveto/redirect',
        'meveto/webhook',
        'meveto/no-user',
        'meveto/connect',
        'meveto/pusherauth',

        '/meveto/login',
        '/meveto/redirect',
        '/meveto/webhook',
        '/meveto/no-user',
        '/meveto/connect',
        '/meveto/pusherauth',
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
        wp_register_style('meveto-main', plugin_dir_url(__DIR__) . 'assets/css/main.css', []);
        wp_enqueue_style('meveto-main');
        wp_register_style('meveto-button', plugin_dir_url(__DIR__) . 'assets/css/widget.css', []);
        wp_enqueue_style('meveto-button');
        wp_register_style( 'meveto-toaster', plugin_dir_url(__DIR__) . 'assets/css/toaster.css', []);
        wp_enqueue_style('meveto-toaster');

        wp_register_style('meveto-no-user', plugin_dir_url(__DIR__) . 'assets/css/no_user.css', []);
        wp_enqueue_style('meveto-no-user');
    }

    public function enqueue_scripts()
    {
        wp_register_script('meveto-pusher-service', plugin_dir_url(__DIR__) . 'assets/js/pusher.js', []);
        wp_enqueue_script('meveto-pusher-service');
        wp_register_script('meveto-toaster', plugin_dir_url(__DIR__) . 'assets/js/toaster.js', []);
        wp_enqueue_script('meveto-toaster');
        wp_register_script('meveto-pusher', plugin_dir_url(__DIR__) . 'assets/js/meveto.pusher.js', []);
        wp_localize_script('meveto-pusher', 'data', [
            'userId' => get_current_user_id() ? get_current_user_id() : null,
            'key' => get_option('meveto_pusher_key') ? get_option('meveto_pusher_key') : null,
            'cluster' => get_option('meveto_pusher_cluster') ? get_option('meveto_pusher_cluster') : null,
            'authEndpoint' => home_url('meveto/pusherauth'),
            'homeUrl' => get_home_url(),
        ]);
        wp_enqueue_script('meveto-pusher');
    }

    public function add_endpoints()
    {
        add_rewrite_endpoint('meveto', EP_ROOT);
    }

    public function process_meveto_auth($user_login = null, $user = null)
    {
        if($user && $user_login)
        {
            $freshLoginAttempt = true;
        } else {
            $freshLoginAttempt = false;
        }
        // Check if an authenticated user exist
        if($user == null)
        {
            $user = wp_get_current_user();
        }

        if($user != null)
        {
            // Check if the current user has started using Meveto. If so, then make sure the user is logged in using Meveto.
            // If the admin has chosen to allow passwords, then skip. Check the option for 'meveto_allow_passwords'
            global $wpdb;
            $table = $wpdb->prefix.'meveto_users';
            $query = "SELECT last_logged_in, last_logged_out FROM `{$wpdb->dbname}`.`{$table}` WHERE `id` = '{$user->ID}'";
            $meveto_user = $wpdb->get_results($query, ARRAY_A);
            
            if($meveto_user != null)
            {
                if($freshLoginAttempt) {
                    // The user is not logged in via Meveto. If passwords are not allowed, Log the user out.
                    if(get_option('meveto_allow_passwords') == 'on')
                    {
                        // Since passwords are allowed, then update the last_logged_in time for the current user
                        $timestamp = time();
                        $query = "UPDATE `{$wpdb->dbname}`.`{$table}` SET `last_logged_in` = '{$timestamp}' WHERE `{$table}`.`id` = '{$user->ID}'";
                        $wpdb->query($query);

                    } else {
                        // Otherwise, do not let the user login using a password.
                        wp_logout();
                        echo "Your account is protected by Meveto. You can not login to your account using your password. Please use Meveto to login to your account.";
                        echo "<br/> <a href='".home_url().'/meveto/login'."'>Login using Meveto</a>";
                        exit;
                    }
                } else {
                    if($meveto_user[0]['last_logged_out'] != null && ($meveto_user[0]['last_logged_out'] > $meveto_user[0]['last_logged_in']))
                    {
                        // If it's not a fresh login attempt, then the currently logged in user has requested a logout from their Meveto dashboard.
                        // Make sure to log the user out.
                        wp_logout();
                        wp_redirect(home_url());
                    }
                }
            }
        }
    }

    public function process_meveto_login()
    {
        global $wp;
        $action = $wp->request;
        
        if($action == '' OR $action == null)
        {
            global $wp_query;
            $action = $wp_query->query['pagename'];
            
            if($action == '' OR $action == null)
            {
                $action = $wp_query->query_vars['meveto'];
            }
        }
        if (in_array($action, $this->allowed_actions)) {
            switch ($action) {
                case 'meveto/login':
                case '/meveto/login':
                    $this->action_login();
                    break;
                case 'meveto/webhook':
                case '/meveto/webhook':
                    $this->action_process_webhook();
                    break;
                case 'meveto/redirect':
                case '/meveto/redirect':
                    $this->action_callback();
                    break;
                case 'meveto/no-user':
                case '/meveto/no-user':
                    $this->action_no_user();
                    break;
                case 'meveto/connect':
                case '/meveto/connect':
                    $this->action_connect_to_meveto();
                    break;
                case 'meveto/pusherauth':
                case '/meveto/pusherauth':
                    $this->action_auth_pusher();
                    break;
            }
        }
    }

    private function action_login()
    {
        $redirect_url = sprintf("%s%s/%s",
            (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443 || $_SERVER['X-Forwarded-Proto'] === 'https') ? "https://" : "http://",
            $_SERVER['HTTP_HOST'],
            'meveto/redirect'
        );
        $query = [
            'client_id' => get_option('meveto_oauth_client_id'),
            'scope' => get_option('meveto_oauth_scope'),
            'response_type' => 'code',
            'redirect_uri' =>  $redirect_url //$redirect_url //"http://localhost/wordpress/meveto/redirect"
        ];
        if(isset($_GET['client_token']))
        {
            $query['client_token'] = stripslashes(sanitize_text_field($_GET['client_token']));
        }
        if(isset($_GET['sharing_token']))
        {
            $query['sharing_token'] = stripslashes(sanitize_text_field($_GET['sharing_token']));
        }
        $authorize_query = http_build_query($query);

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
            'meveto/redirect'
        );
        // check whether authorization code was returned by the Meveto OAuth server or not.
        // This data can be trusted
        if($_GET['code']) {
            $client_id = get_option('meveto_oauth_client_id');
            $handler = new Meveto_OAuth_Handler();
            $accessToken = $handler->get_access_token(get_option('meveto_oauth_token_url'), 'authorization_code', $client_id, get_option('meveto_oauth_client_secret'), $_GET['code'], $redirect_url);
            $mevetoUserId = $handler->get_resource_owner($accessToken,"https://prod.meveto.com/api/client/user?client_id=".$client_id);

            $this->login_user($mevetoUserId);
        } else {
            // Authorization code was not returned.
            echo "We are sorry! Meveto could not authenticate your credentials. Meveto server responded with the following error/errors.";
            echo "<br/>";
            echo ($_GET['error']) ? $_GET['error'] : '';
            echo "<br/>";
            echo ($_GET['error_description']) ? $_GET['error_description'] : '';
        }
    }

    private function login_user($mevetoUserId)
    {
        // First grab user from the database by meveto_id
        global $wpdb;
        $table = $wpdb->prefix.'users';
        $query = "SELECT * FROM `{$wpdb->dbname}`.`{$table}` WHERE `meveto_id` = '{$mevetoUserId}'";
        $user = $wpdb->get_results($query, ARRAY_A)[0];
        if($user !== null)
        {
            // Set Meveto users record. First check if the Meveto users record exist for this user already or not.
            $table = $wpdb->prefix.'meveto_users';
            $query = "SELECT * FROM `{$wpdb->dbname}`.`{$table}` WHERE `id` = '{$user['ID']}'";
            $meveto_user = $wpdb->get_results($query, ARRAY_A)[0];
            $timestamp = time();
            if($meveto_user == null)
            {
                // Meveto user was not found. This is probably user's very first time using Meveto with this website.
                $query = "INSERT INTO `{$wpdb->dbname}`.`{$table}` (`id`, `last_logged_in`) VALUES ('{$user['ID']}', '{$timestamp}')";
                $wpdb->query($query);
            } else {
                // Update the record for this Meveto user
                $query = "UPDATE `{$wpdb->dbname}`.`{$table}` SET `last_logged_in` = '{$timestamp}' WHERE `{$table}`.`id` = '{$user['ID']}'";
                $wpdb->query($query);
            }
            wp_set_current_user($user['ID'], $user->user_login);
            wp_set_auth_cookie($user['ID']);
            do_action('wp_login', $user['user_login'], 10, 2);
            wp_redirect(home_url());
            exit();
        } else {
            $redirect_to = home_url()."/meveto/no-user?meveto_id=".$mevetoUserId;
            wp_redirect($redirect_to); // redirect user to a connect to Meveto page.
            exit();
        }
    }

    private function action_no_user() {
        // Load the template for the NO USER virtual page.
        include plugin_dir_path(dirname(__FILE__)) . 'public/partials/no_user.php';
        exit();
    }

    private function action_connect_to_meveto()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-meveto-oauth-handler.php';
        $login_name = stripslashes(sanitize_text_field($_POST['login_name']));
        $login_password = stripslashes(sanitize_text_field($_POST['login_password']));
        $mevetoId = stripslashes(sanitize_text_field($_GET['meveto_id']));

        $user = wp_authenticate($login_name, $login_password);

        if (is_wp_error($user) || $user == null || $user == false) {
            session_start();
            $_SESSION['meveto_error'] = "You have entered incorrect login credentials.";
            $redirect_to = home_url()."/meveto/no-user?meveto_id=".$mevetoId;
            wp_redirect($redirect_to);

        } else {
            // Set meveto_id on the user
            global $wpdb;
            $table = $wpdb->prefix.'users';
            $query = "UPDATE `{$wpdb->dbname}`.`{$table}` SET `meveto_id` = '{$mevetoId}' WHERE `{$table}`.`id` = '{$user->ID}'";
            $wpdb->query($query);
            $this->login_user($mevetoId);
        }
        exit();
    }

    private function action_auth_pusher()
    {
        if(is_user_logged_in())
        {
            $channel = stripslashes(sanitize_text_field($_POST['channel_name']));
            $socket = stripslashes(sanitize_text_field($_POST['socket_id']));

            // Make sure the logged in user and the owns the private channel. Extract user ID from the channel name
            $array = explode('.', $channel);
            $userID = array_values(array_slice($array, -1))[0];
            if($userID == get_current_user_id())
            {
                $pusher = $this->instantiatePusher();
                status_header(200);
                echo $pusher->socket_auth($channel, $socket);
            }
        }
        else {
            status_header(403);
            echo "Forbidden";
        }
        exit();
    }

    private function action_process_webhook()
    {
        if($json = json_decode(file_get_contents("php://input"), true)) {
            $data = $json;
        } else {
            $data = $_REQUEST;
        }
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-meveto-oauth-handler.php';

        // Switch over type of the event
        switch($data['type'])
        {
            case 'User_Logged_Out':
                // Exchange the token for user information
                $handler = new Meveto_OAuth_Handler();
                $user = $handler->getTokenUser($data['user_token'], 'https://prod.meveto.com/api/client/user-for-token');
                if($user)
                {
                    // Find the corresponding local user for the Meveto ID (user)
                    global $wpdb;
                    $table = $wpdb->prefix.'users';
                    $query = "SELECT * FROM `{$wpdb->dbname}`.`{$table}` WHERE `meveto_id` = '{$user}'";
                    $user = $wpdb->get_results($query, ARRAY_A)[0];
                    if($user !== null)
                    {
                        $table = $wpdb->prefix.'meveto_users';
                        $timestamp = time();
                        // Update the last_logged_out record for this Meveto user
                        $query = "UPDATE `{$wpdb->dbname}`.`{$table}` SET `last_logged_out` = '{$timestamp}' WHERE `{$table}`.`id` = '{$user['ID']}'";
                        $wpdb->query($query);

                        // Trigger pusher event
                        $pusher = $this->instantiatePusher();
                        $data['message'] = '';
                        $pusher->trigger('private-Meveto-Kill.'.$user['ID'], 'logout', $data);
                    }
                    status_header(200);
                    echo "";
                    exit();
                } else {
                    //error_log("\n\n Could not exchange logout token for a user. 403 response sent to Meveto webhook call",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
                    status_header(403);
                    exit();
                }
            break;
            case 'Meveto_Protection_Removed':
                // Exchange the token for user information
                $handler = new Meveto_OAuth_Handler();
                $user = $handler->getTokenUser($data['user_token'], 'https://prod.meveto.com/api/client/user-for-token');
                if($user)
                {
                    // Set meveto_id to NULL for the user
                    global $wpdb;
                    $table = $wpdb->prefix.'users';
                    $query = "SELECT * FROM `{$wpdb->dbname}`.`{$table}` WHERE `{$table}`.`meveto_id` = '{$user}'";
                    $user = $wpdb->get_results($query, ARRAY_A)[0];
                    if($user !== null)
                    {
                        $query = "UPDATE `{$wpdb->dbname}`.`{$table}` SET `meveto_id` = NULL WHERE `{$table}`.`ID` = '{$user['ID']}'";
                        $wpdb->query($query);
                        // Next, remove the Meveto Users record for the user
                        $table = $wpdb->prefix.'meveto_users';
                        $query = "DELETE FROM `{$wpdb->dbname}`.`{$table}` WHERE `{$table}`.`id` = '{$user['ID']}'";
                        $wpdb->query($query);
                    }

                    // Event if the user could not be found here locally, send a 200 response to Meveto regardless. This is because the User could possibly have not mapped their Meveto
                    // account to a local user account
                    status_header(200);
                    echo "";
                    exit();
                } else {
                    status_header(403);
                    exit();
                }
            break;
        }
    }

    private function instantiatePusher() {
        // Determine whether to use TLS or not
        $tls = false;
        if (isset($_SERVER['HTTPS']) &&
            ($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
            isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
            $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https') {
            $tls = true;
        }
        $options = array(
            'cluster' => get_option('meveto_pusher_cluster'),
            'useTLS' => $tls
        );
        $pusher = new Pusher\Pusher(
            get_option('meveto_pusher_key'),
            get_option('meveto_pusher_secret'),
            get_option('meveto_pusher_app_id'),
        $options
        );

        return $pusher;
    }

}

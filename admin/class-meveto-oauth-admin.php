<?php

/**
 * Created by VSCODE
 * User: Zia Khan
 * Date: April 02, 2020
 * Time: 18:30
 */
class Meveto_OAuth_Admin
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
     * The options name to be used in this plugin
     *
     * @since    1.0.0
     * @access    private
     * @var    string $option_name Option name of this plugin
     */
    private $option_name = 'meveto';

    /**
     * Initialize the class and set its properties.
     *
     * @since    1.0.0
     * @param      string $plugin_name The name of this plugin.
     * @param      string $version The version of this plugin.
     */

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

    public function extend_menu()
    {
        add_menu_page('Meveto OAuth Settings', 'Meveto', 'administrator', 'meveto_oauth_settings', [$this, 'meveto_oauth_settings_page']);
    }

    public function meveto_oauth_settings_page()
    {
        include_once 'partials/settings.php';
    }

    public function manage_settings()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'meveto_manage_settings') {
            $this->save_settings();
        }
    }

    private function save_settings()
    {
        $client_id = stripslashes(sanitize_text_field($_POST['meveto_oauth_client_id']));
        $client_secret = stripslashes(sanitize_text_field($_POST['meveto_oauth_client_secret']));
        $scope = stripslashes(sanitize_text_field($_POST['meveto_oauth_scope']));
        $authorize_url = esc_url_raw($_POST['meveto_oauth_authorize_url']);
        $token_url = esc_url_raw($_POST['meveto_oauth_token_url']);
        $allow_passwords = stripslashes(sanitize_text_field($_POST['meveto_allow_passwords']));

        $pusher_app = stripslashes(sanitize_text_field($_POST['meveto_pusher_app_id']));
        $pusher_key = stripslashes(sanitize_text_field($_POST['meveto_pusher_key']));
        $pusher_secret = stripslashes(sanitize_text_field($_POST['meveto_pusher_secret']));
        $pusher_cluster = stripslashes(sanitize_text_field($_POST['meveto_pusher_cluster']));

        update_option($this->option_name . '_oauth_client_id', $client_id);
        update_option($this->option_name . '_oauth_client_secret', $client_secret);
        update_option($this->option_name . '_oauth_scope', $scope);
        update_option($this->option_name . '_oauth_authorize_url', $authorize_url);
        update_option($this->option_name . '_oauth_token_url', $token_url);
        update_option($this->option_name . '_allow_passwords', $allow_passwords);

        update_option($this->option_name . '_pusher_app_id', $pusher_app);
        update_option($this->option_name . '_pusher_key', $pusher_key);
        update_option($this->option_name . '_pusher_secret', $pusher_secret);
        update_option($this->option_name . '_pusher_cluster', $pusher_cluster);
    }

    public function enqueue_styles()
    {
        admin_register_style('meveto-main', plugin_dir_url(__DIR__) . 'assets/css/main.css', []);
        admin_enqueue_style('meveto-main');
        admin_register_style('meveto-admin', plugin_dir_url(__FILE__) . '/css/admin.css', []);
        admin_enqueue_style('meveto-admin');
        admin_register_style( 'meveto-toaster', plugin_dir_url(__DIR__) . 'assets/css/toaster.css', []);
        admin_enqueue_style('meveto-toaster');
    }

    public function enqueue_scripts()
    {
        wp_register_script( 'pusher', plugin_dir_url(__DIR__) . 'assets/js/pusher.js', []);
        wp_enqueue_script('pusher');
        wp_register_script( 'toaster', plugin_dir_url(__DIR__) . 'assets/js/toaster.js', []);
        wp_enqueue_script('toaster');
        wp_register_script( 'meveto-pusher', plugin_dir_url(__DIR__) . 'assets/js/meveto.pusher.js', []);
        wp_localize_script( 'meveto-pusher', 'data', [
            'userId' => get_current_user_id() ? get_current_user_id() : null,
            'key' => get_option('meveto_pusher_key') ? get_option('meveto_pusher_key') : null,
            'cluster' => get_option('meveto_pusher_cluster') ? get_option('meveto_pusher_cluster') : null,
            'authEndpoint' => home_url('meveto/pusherauth'),
            'homeUrl' => get_home_url(),
        ]);
        wp_enqueue_script('meveto-pusher');
    }
}

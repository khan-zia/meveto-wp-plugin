<?php

/**
 * Created by IntelliJ IDEA.
 * User: gpapkala
 * Date: 28.11.2017
 * Time: 15:20
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
    private $option_name = 'meveto_oauth';

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

        $pusher_app = stripslashes(sanitize_text_field($_POST['meveto_pusher_app_id']));
        $pusher_key = stripslashes(sanitize_text_field($_POST['meveto_pusher_key']));
        $pusher_secret = stripslashes(sanitize_text_field($_POST['meveto_pusher_secret']));
        $pusher_cluster = stripslashes(sanitize_text_field($_POST['meveto_pusher_cluster']));

        update_option($this->option_name . '_client_id', $client_id);
        update_option($this->option_name . '_client_secret', $client_secret);
        update_option($this->option_name . '_scope', $scope);
        update_option($this->option_name . '_authorize_url', $authorize_url);
        update_option($this->option_name . '_token_url', $token_url);

        update_option('meveto_pusher_app_id', $pusher_app);
        update_option('meveto_pusher_key', $pusher_key);
        update_option('meveto_pusher_secret', $pusher_secret);
        update_option('meveto_pusher_cluster', $pusher_cluster);
    }

    public function enqueue_styles()
    {
        wp_enqueue_style('meveto-admin', plugin_dir_url(__FILE__) . '/css/admin.css', []);
        wp_register_style( 'toaster', 'https://cdn.jsdelivr.net/npm/toastify-js/src/toastify.min.css', []);
        wp_enqueue_style('toaster');
        wp_enqueue_style('meveto-main', plugin_dir_url(__DIR__) . 'assets/css/main.css', []);
    }

    public function enqueue_scripts()
    {
        wp_register_script( 'pusher', 'https://js.pusher.com/5.0/pusher.min.js', []);
        wp_enqueue_script('pusher');
        wp_register_script( 'toaster', 'https://cdn.jsdelivr.net/npm/toastify-js', []);
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

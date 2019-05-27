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

        update_option($this->option_name . '_client_id', $client_id);
        update_option($this->option_name . '_client_secret', $client_secret);
        update_option($this->option_name . '_scope', $scope);
        update_option($this->option_name . '_authorize_url', $authorize_url);
        update_option($this->option_name . '_token_url', $token_url);
    }

    public function enqueue_styles()
    {
        wp_enqueue_style('meveto-admin', plugin_dir_url(__FILE__) . '/css/admin.css', []);
    }

    public function enqueue_scripts()
    {

    }
}

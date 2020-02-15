<?php

/**
 * Created by IntelliJ IDEA.
 * User: gpapkala
 * Date: 28.11.2017
 * Time: 14:01
 */
class Meveto_OAuth
{

    /**
     * The loader that's responsible for maintaining and registering all hooks that power
     * the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      Meveto_OAuth_Loader $loader Maintains and registers all hooks for the plugin.
     */
    protected $loader;

    /**
     * The unique identifier of this plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $plugin_name The string used to uniquely identify this plugin.
     */
    protected $plugin_name = 'meveto-oauth';

    /**
     * The current version of the plugin.
     *
     * @since    1.0.0
     * @access   protected
     * @var      string $version The current version of the plugin.
     */
    protected $version;

    /**
     * Define the core functionality of the plugin.
     *
     * Set the plugin name and the plugin version that can be used throughout the plugin.
     * Load the dependencies, define the locale, and set the hooks for the admin area and
     * the public-facing side of the site.
     *
     * @since    1.0.0
     */
    public function __construct()
    {
        if (defined('MEVETO_OAUTH_VERSION')) {
            $this->version = MEVETO_OAUTH_VERSION;
        } else {
            $this->version = '1.0.0';
        }

        $this->load_dependencies();
        $this->define_admin_hooks();
        $this->define_widget_hooks();
        $this->define_public_hooks();

    }

    /**
     * Load the required dependencies for this plugin.
     *
     * Include the following files that make up the plugin:
     *
     * - Meveto_OAuth_Loader. Orchestrates the hooks of the plugin.
     * - Meveto_OAuth_i18n. Defines internationalization functionality.
     * - Meveto_OAuth_Admin. Defines all hooks for the admin area.
     * - Meveto_OAuth_Public. Defines all hooks for the public side of the site.
     *
     * Create an instance of the loader which will be used to register the hooks
     * with WordPress.
     *
     * @since    1.0.0
     * @access   private
     */
    private function load_dependencies()
    {
        /**
         * The class responsible for orchestrating the actions and filters of the
         * core plugin.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'includes/class-meveto-oauth-loader.php';

        /**
         * The class responsible for defining all actions that occur in the admin area.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'admin/class-meveto-oauth-admin.php';

        /**
         * The class responsible for defining all actions that occur in the public-facing
         * side of the site.
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-meveto-oauth-public.php';

        /**
         * The class responsible for defining all actions in widgets
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/class-meveto-oauth-widget.php';

        $this->loader = new Meveto_OAuth_Loader();

    }

    /**
     * Register all of the hooks related to the admin area functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_admin_hooks()
    {
        $plugin_admin = new Meveto_OAuth_Admin($this->get_plugin_name(), $this->get_version());
        $this->loader->add_action('admin_init', $plugin_admin, 'enqueue_styles');
        $this->loader->add_action('admin_init', $plugin_admin, 'enqueue_scripts');
        $this->loader->add_action('admin_menu', $plugin_admin, 'extend_menu');
        $this->loader->add_action('admin_init', $plugin_admin, 'manage_settings');
    }

    /**
     * Register all of the hooks related to the public-facing functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_public_hooks()
    {
        $plugin_public = new Meveto_OAuth_Public($this->get_plugin_name(), $this->get_version());

        $this->loader->add_action('init', $plugin_public, 'add_endpoints');
        $this->loader->add_action('wp', $plugin_public, 'process_meveto_auth');
        $this->loader->add_action('wp_login', $plugin_public, 'process_meveto_auth', 10, 2);
        $this->loader->add_action('wp', $plugin_public, 'process_meveto_login');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_styles');
        $this->loader->add_action('wp_enqueue_scripts', $plugin_public, 'enqueue_scripts');
    }

    /**
     * Register all of the hooks related to the widgets functionality
     * of the plugin.
     *
     * @since    1.0.0
     * @access   private
     */
    private function define_widget_hooks()
    {
        $plugin_widget = new Meveto_OAuth_Widget();

        $this->loader->add_action('widgets_init', $plugin_widget, 'register_meveto_widget');
        $this->loader->add_action('get_header', $plugin_widget, 'enqueue_styles');
        $this->loader->add_action('get_header', $plugin_widget, 'enqueue_scripts');
        $this->loader->add_action('login_form', $plugin_widget, 'show_login_button');
    }

    /**
     * Run the loader to execute all of the hooks with WordPress.
     *
     * @since    1.0.0
     */
    public function run()
    {
        $this->loader->run();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @since     1.0.0
     * @return    string    The name of the plugin.
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @since     1.0.0
     * @return    Meveto_OAuth_Loader    Orchestrates the hooks of the plugin.
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @since     1.0.0
     * @return    string    The version number of the plugin.
     */
    public function get_version()
    {
        return $this->version;
    }

}

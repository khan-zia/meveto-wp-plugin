<?php

/**
 * Created by IntelliJ IDEA.
 * User: gpapkala
 * Date: 28.11.2017
 * Time: 15:20
 */
class Meveto_OAuth_Widget extends WP_Widget
{

    public function __construct()
    {
        parent::__construct(false, 'Meveto OAuth Login Widget', ['description' => __('Login to Apps with Meveto', 'flw')]);
    }

    public function widget($args, $instance)
    {
//        include_once 'partials/login_widget.php';
        /**
         * The class responsible for displaying 'Login with Meveto' link
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/login_widget.php';
        Meveto_Login_Button::login_button();
    }

    public function update($new_instance, $old_instance)
    {
        $instance = [];
        $instance['wid_title'] = strip_tags($new_instance['wid_title']);

        return $instance;
    }

    public function register_meveto_widget()
    {
        register_widget(get_class($this));
    }

    public function enqueue_styles()
    {
        wp_enqueue_style('meveto-widget', plugin_dir_url(__FILE__) . '/css/widget.css', []);
    }

    public function enqueue_scripts()
    {

    }

    public function show_login_button()
    {
        /**
         * The class responsible for displaying 'Login with Meveto' link
         */
        require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/login_widget.php';
        Meveto_Login_Button::login_button();
    }

}

?>

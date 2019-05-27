<?php
/**
 * Created by Zia Khan
 * User: Zia Khan
 * Date: 14.05.2019
 * Time: 0613 PKT
 */
class Meveto_OAuth_Public_Button
{

    function GenerateShortCode()
    {
    	require_once plugin_dir_path(dirname(__FILE__)) . 'public/partials/login_widget.php';
        return Meveto_Login_Button::login_button();
    }
    public function AddShortCode()
    {
    	add_shortcode('public_oauth_button', array($this, 'GenerateShortCode'));
    }

}
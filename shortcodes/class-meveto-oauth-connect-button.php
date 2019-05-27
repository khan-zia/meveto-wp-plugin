<?php
/**
 * Created by Zia Khan
 * User: Zia Khan
 * Date: 14.05.2019
 * Time: 0613 PKT
 */
class Meveto_OAuth_Connect_Button
{

    function GenerateShortCode()
    {
    	require_once plugin_dir_path(dirname(__FILE__)) . 'includes/partials/class-connect-button.php';
        return Connect_To_Meveto_Button::connect_button();
    }
    public function AddShortCode()
    {
    	add_shortcode('connect_to_meveto_button', array($this, 'GenerateShortCode'));
    }

}
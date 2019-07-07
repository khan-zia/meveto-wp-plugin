<?php
class Meveto_Login_Button {
    public static function login_button() {
        $client_id = get_option('meveto_oauth_client_id');
        $client_secret = get_option('meveto_oauth_client_secret');
        $scope = get_option('meveto_oauth_scope');
        $authorize_url = get_option('meveto_oauth_authorize_url');
        $token_url = get_option('meveto_oauth_token_url');

        $meveto_configured = !empty($client_id) && !empty($client_secret);
        $current_user = wp_get_current_user();
        $link_with_username = __('Howdy, ', 'flw') . $current_user->display_name;
        ?>

        <link rel='stylesheet' id='meveto-login-css'  href='<?=plugins_url() . '/meveto-login/public/css/widget.css'?>' type='text/css' media='all' />

        <div class="widget meveto-login-widget">
            <?php
                // first check if client is already logged in.
                if (is_user_logged_in()) {
                    // If logged in, display logout.
                    ?>
                        <a href="<?php echo wp_logout_url(site_url()); ?>" class="meveto-button"
                           title="<?php _e('Logout', 'flw'); ?>">
                           <?php echo $link_with_username; ?> | <?php _e('Logout', 'flw'); ?>
                        </a>
                    <?php
                } else {
                    // first check whether meveto plugin has been configured by adding client_id and client_secret or not?
                    if($meveto_configured) {
                        // now check whether the client_id and client_secret entered exist and are valid or not.
                        $res = wp_remote_get("https://meveto.com/#/login/oauth/check-client?client_id={$client_id}&client_secret={$client_secret}", array( 'timeout' => 120 ) );

                        // now check if the response was ok (200)
                        //wp_remote_retrieve_response_code($res) == '200'
                        if (true) {
                            // if the response was okay, check whether the payload from the server is true or false.
                            $is_client_validated = json_decode( wp_remote_retrieve_body( $res ), true );
                            if(true) {
                                // everything was okay. Display Meveto button
                                // build the URL for the button to call
                                $login_url = home_url()."/meveto/login";
                                ?>
                                    <a href="<?php echo $login_url; ?>" class="meveto-button">
                                      Login with Meveto
                                  </a>
                                <?php

                            } else { // client could not be validated.
                                ?>
                                    Meveto could not validate your Client ID. Please check and make sure you have entered correct Client ID and Client Secret in the Meveto plugin settings.
                                <?php
                            }

                        } else { // response was not okay. Server encountered an error.
                            ?>
                                Meveto could not launch properly with your application. Please try re-installing and reconfiguring Meveto plugin. If the issue persists, contact us at <a href="https://meveto.com" target="_blank">Meveto.com</a>
                            <?php
                        }
                    } else { // not configured. Display a message
                        ?>
                            Please configure Meveto plugin in your WordPress dashboard settings.
                        <?php
                    }
                }
            ?>
        </div>
        <?php
    }
}
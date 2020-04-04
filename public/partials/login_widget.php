<?php
class Meveto_Login_Button {
    public static function login_button() {
        $client_id = get_option('meveto_oauth_client_id');
        $client_secret = get_option('meveto_oauth_client_secret');

        $meveto_configured = !empty($client_id) && !empty($client_secret);
        $current_user = wp_get_current_user();
        $link_with_username = __('Howdy, ', 'flw') . $current_user->display_name;
        ?>

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
                        // build the URL for the button to call
                        $login_url = home_url()."/meveto/login";
                        ?>
                            <a href="<?php echo $login_url; ?>" class="meveto-button">
                              Login with Meveto
                          </a>
                        <?php
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
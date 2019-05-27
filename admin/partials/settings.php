<div class="wrap meveto-settings">
    <h1>Configure Meveto OAuth Integration</h1>

    <form method="post" action="">
        <input type="hidden" name="action" value="meveto_manage_settings">
        <table class="form-table">
            <tr>
                <th><span class="meveto-required">*</span>Client ID:</th>
                <td><input class="regular-text" required="" type="text" name="meveto_oauth_client_id"
                           value="<?php echo get_option('meveto_oauth_client_id'); ?>"></td>
            </tr>
            <tr>
                <th><span class="meveto-required">*</span>Client Secret:</th>
                <td><input class="regular-text" required="" type="password" name="meveto_oauth_client_secret"
                           value="<?php echo get_option('meveto_oauth_client_secret'); ?>"></td>
            </tr>
            <tr>
                <th>Scope:</th>
                <td><input class="regular-text" type="text" name="meveto_oauth_scope"
                           value="<?php echo get_option('meveto_oauth_scope'); ?>"">
                </td>
            </tr>
            <tr>
                <th><span class="meveto-required">*</span>Authorize Endpoint:</th>
                <td><input class="regular-text" type="text" name="meveto_oauth_authorize_url"
                           value="<?php echo get_option('meveto_oauth_authorize_url'); ?>"">
                </td>
            </tr>
            <tr>
                <th><span class="meveto-required">*</span>Access Token Endpoint:</th>
                <td><input class="regular-text" type="text" name="meveto_oauth_token_url"
                           value="<?php echo get_option('meveto_oauth_token_url'); ?>"">
                </td>
            </tr>
        </table>
        <?php submit_button("Connect with Meveto"); ?>
    </form>
</div>

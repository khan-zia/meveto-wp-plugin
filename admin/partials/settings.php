<div class="wrap meveto-settings">
    <h1>Configure Meveto OAuth Integration</h1>

    <?php
        if(!empty(get_option('meveto_oauth_scope'))) {
            $scope = get_option('meveto_oauth_scope');
        } else {
            $scope = "";
        }
        if(!empty(get_option('meveto_oauth_authorize_url'))) {
            $auth_url = get_option('meveto_oauth_authorize_url');
        } else {
            $auth_url = "https://auth.meveto.com/meveto-auth/oauth/authorize";
        }
        if(!empty(get_option('meveto_oauth_token_url'))) {
            $token_url = get_option('meveto_oauth_token_url');
        } else {
            $token_url = "https://auth.meveto.com/meveto-auth/oauth/mevtoken";
        }
    ?>

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
                           value="<?php echo $scope; ?>"">
                </td>
            </tr>
            <tr>
                <th><span class="meveto-required">*</span>Authorize Endpoint:</th>
                <td><input class="regular-text" type="text" name="meveto_oauth_authorize_url"
                           value="<?php echo $auth_url; ?>"">
                </td>
            </tr>
            <tr>
                <th><span class="meveto-required">*</span>Access Token Endpoint:</th>
                <td><input class="regular-text" type="text" name="meveto_oauth_token_url"
                           value="<?php echo $token_url; ?>"">
                </td>
            </tr>
        </table>
        <?php submit_button("Save settings"); ?>
    </form>
</div>

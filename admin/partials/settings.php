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
            $auth_url = "https://prod.meveto.com/oauth/authorize";
        }
        if(!empty(get_option('meveto_oauth_token_url'))) {
            $token_url = get_option('meveto_oauth_token_url');
        } else {
            $token_url = "https://prod.meveto.com/oauth/token";
        }

        if(!empty(get_option('meveto_pusher_app_id'))) {
            $pusher_app = get_option('meveto_pusher_app_id');
        } else {
            $pusher_app = "";
        }
        if(!empty(get_option('meveto_pusher_key'))) {
            $pusher_key = get_option('meveto_pusher_key');
        } else {
            $pusher_key = "";
        }
        if(!empty(get_option('meveto_pusher_secret'))) {
            $pusher_secret = get_option('meveto_pusher_secret');
        } else {
            $pusher_secret = "";
        }
        if(!empty(get_option('meveto_pusher_cluster'))) {
            $pusher_cluster = get_option('meveto_pusher_cluster');
        } else {
            $pusher_cluster = "mt1";
        }
    ?>

    <form method="post" action="">
        <input type="hidden" name="action" value="meveto_manage_settings">
        <h3>Meveto server configuration</h3><hr />
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
        <h3>Pusher configuration</h3><hr />
        <p>We highly recommend you use <a href="https://pusher.com">pusher</a> with Meveto so that your website can perform seamless actions when your Meveto users take an action from their Meveto dashboard. For example, when a user logs out from your website using their Meveto dashboard, your website will be able to refresh automatically.</p>
        <table class="form-table">
            <tr>
                <th>Your pusher app ID:</th>
                <td><input class="regular-text" type="text" name="meveto_pusher_app_id"
                           value="<?php echo $pusher_app; ?>"">
                </td>
            </tr>
            <tr>
                <th>Your pusher app key:</th>
                <td><input class="regular-text" type="text" name="meveto_pusher_key"
                           value="<?php echo $pusher_key; ?>"">
                </td>
            </tr>
            <tr>
                <th>Your pusher app secret:</th>
                <td><input class="regular-text" type="text" name="meveto_pusher_secret"
                           value="<?php echo $pusher_secret; ?>"">
                </td>
            </tr>
            <tr>
                <th>Your pusher app cluster:</th>
                <td><input class="regular-text" type="text" name="meveto_pusher_cluster"
                           value="<?php echo $pusher_cluster; ?>"">
                </td>
            </tr>
        </table>
        <?php submit_button("Save settings"); ?>
    </form>
</div>

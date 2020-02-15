<?php

class Meveto_OAuth_Handler
{
    public $retry = 3;

    /**
     * @param $token_endpoint
     * @param $grant_type
     * @param $client_id
     * @param $client_secret
     * @param $code
     * @param $redirect_url
     * @return mixed
     */
    public function get_access_token($token_endpoint, $grant_type, $client_id, $client_secret, $code, $redirect_url)
    {
        $ch = curl_init($token_endpoint);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_USERPWD, $client_id . ":" . $client_secret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, 'redirect_uri=' . urlencode($redirect_url) . '&grant_type=' . $grant_type . '&client_id=' . $client_id . '&client_secret=' . $client_secret . '&code=' . $code);
        $content = $this->call_curl($ch, $this->retry);
        if (curl_error($ch)) {
            exit(curl_error($ch));
        }
        $content = json_decode($content, true);
        if (!is_array($content)) {
            echo "<br>Invalid response received while expecting access token.";
            exit();
        }
        if (isset($content["error_description"])) {
            exit($content["error_description"]);
        } else if (isset($content["error"])) {
            exit($content["error"]);
        } else if (isset($content["access_token"])) {
            $access_token = $content["access_token"];
        } else {
            exit('Invalid response received from OAuth Provider. Contact your administrator for more details.');
        }
        return $access_token;
    }

    /**
     * @param $access_token
     * @return mixed
     */
    public function get_resource_owner($access_token,$resource_endpoint)
    {
		//error_log("\n get_resource_owner() CURL JWT token: $access_token",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
        $ch = curl_init($resource_endpoint);
        $authorization = "Authorization: Bearer ".$access_token;
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json' , $authorization ));
        $content = $this->call_curl($ch, $this->retry);
        if (curl_error($ch)) {
            exit(curl_error($ch));
        }
        if (!is_array(json_decode($content, true))) {
            echo "<br>Invalid response received while expecting resource owner information.";
            exit();
        }
        $content = json_decode($content, true);
        if (isset($content["error_description"])) {
            exit($content["error_description"]);
        } else if (isset($content["error"])) {
            exit($content["error"]);
        } else if (isset($content["message"])) {
            exit($content["message"]);
        } else if (isset($content["payload"])) {
            $user = $content["payload"]['user'];
        } else {
            exit('Invalid response received from Meveto while trying to retrieve the logged in user. Contact your administrator for more details.');
        }
        return $user;
    }

    /**
     * Get the user that is associated with the current webhook event
     * 
     * @param $user_token
     * @param $resource_endpoint
     * @return mixed
     */
    public function getTokenUser($user_token, $resource_endpoint)
    {
        //error_log("\n\n logout user call initiated for token = \"{$user_token}\"",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
        $ch = curl_init($resource_endpoint.'?token='.$user_token);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
        $content = $this->call_curl($ch, $this->retry);
        if (curl_error($ch)) {
            exit(curl_error($ch));
        }
        $content = json_decode($content, true);
        if ($content["status"] == 'Token_User_Retrieved') {
            // error_log("\n\n Status success received from the server",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
            $user = $content['payload']['user'];
            // error_log("\n\n The logout user is = \"{$user}\"",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
        } else {
            // There was an error
            $user = null;
            //error_log("\n\n Logout user from the server could not be retrieved.",3,plugin_dir_path(dirname(__FILE__)).'logs/error_log.txt');
        }
        
        return $user;
    }

    private function call_curl($ch, $retry)
    {
        $tried = 0;
        $success = false;
        $content = null;
        while (($retry > $tried) && !$success) {
            sleep($tried*0.5);
            $tried += 1;
            $content = curl_exec($ch);
            if (curl_error($ch)) {
                continue;
            }

            if (!is_array(json_decode($content, true))) {
                continue;
            }

            $json_content = json_decode($content, true);
            if (isset($json_content["error_description"])) {
                continue;
            } else if (isset($json_content["error"])) {
                continue;
            }
            $success = true;
        }
        return $content;
    }

}

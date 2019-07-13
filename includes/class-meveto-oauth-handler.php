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
        //echo '<script type="text/javascript">console.log("Access token received and returned.")</script>';
        return $access_token;
    }

    /**
     * @param $access_token
     * @return mixed
     */
    function get_resource_owner($access_token,$resource_endpoint)
    {
        $ch = curl_init($resource_endpoint);
        $authorization = "Authorization: Bearer ".$access_token;
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));

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
        } else if (isset($content["email"])) {
            $email = $content["email"];
        } else {
            exit('Invalid response received from OAuth Provider. Contact your administrator for more details.');
        }
        return ['token' => $access_token, 'email' => $email];
    }

    public function connect_to_meveto($client_id,$login_name,$access_token)
    {
        $ch = curl_init("https://auth.meveto.com/meveto-auth/connect_account");
        $authorization = "Authorization: Bearer ".$access_token;
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_ENCODING, "");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_AUTOREFERER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , $authorization ));

        curl_setopt($ch, CURLOPT_POSTFIELDS, 'client_id=' . $client_id . '&login_name=' . $login_name);

        $content = $this->call_curl($ch, $this->retry);

        if (curl_error($ch)) {
            exit(curl_error($ch));
        }

        $content = json_decode($content, true);

        if (!is_array($content)) {
            echo "<br>Invalid response received from OAuth Provider. Contact your administrator for more details.";
            exit();
        }
        if (isset($content["error_description"])) {
            exit($content["error_description"]);
        } else if (isset($content["error"])) {
            exit($content["error"]);
        } else if (isset($content["success"])) {
            $success = $content[0]["success"];
        } else {
            exit('Invalid response received from OAuth Provider. Contact your administrator for more details.');
        }
        return $success;
    }

    private function call_curl($ch, $retry)
    {
        $tried = 0;
        $success = false;
        $content = null;
        while (($retry > $tried) && !$success) {
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

<?php

use Jose\Component\Checker\HeaderCheckerManager;
use Jose\Component\Checker\AlgorithmChecker;
use Jose\Component\Core\AlgorithmManager;
use Jose\Component\Core\Converter\StandardConverter;
use Jose\Component\KeyManagement\JWKFactory;
use Jose\Component\Signature\Algorithm\RS256;
use Jose\Component\Signature\JWSTokenSupport;
use Jose\Component\Signature\JWSVerifier;
use Jose\Component\Signature\Serializer\CompactSerializer;
use Jose\Component\Signature\Serializer\JWSSerializerManager;

class Meveto_OAuth_Handler
{
    public $retry = 3;

    protected $headerCheckerManager;

    protected $algorithmManager;

    protected $jwsVerifier;

    protected $jsonConverter;

    const PUBLIC_KEY = <<<EOD
-----BEGIN PUBLIC KEY-----
MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnGp/Q5lh0P8nPL21oMMrt2RrkT9AW5jgYwLfSUnJVc9G6uR3cX
RRDCjHqWU5WYwivcF180A6CWp/ireQFFBNowgc5XaA0kPpzEtgsA5YsNX7iSnUibB004iBTfU9hZ2Rbsc8cWqynT0RyN4T
P1RYVSeVKvMQk4GT1r7JCEC+TNu1ELmbNwMQyzKjsfBXyIOCFU/E94ktvsTZUHF4Oq44DBylCDsS1k7/sfZC2G5EU7Oz0m
hG8+Uz6MSEQHtoIi6mc8u64Rwi3Z3tscuWG2ShtsUFuNSAFNkY7LkLn+/hxLCu2bNISMaESa8dG22CIMuIeRLVcAmEWEWH
5EEforTg+QIDAQAB
-----END PUBLIC KEY-----
EOD;

    /**
     * Meveto_OAuth_Handler constructor.
     */
    public function __construct() {
        // $this->headerCheckerManager = HeaderCheckerManager::create(
        //     [
        //         new AlgorithmChecker(['RS256']), // We check the header "alg" (algorithm)
        //     ],
        //     [
        //         new JWSTokenSupport(), // Adds JWS token type support
        //     ]
        // );

        // // The algorithm manager with the RS256 algorithm.
        // $this->algorithmManager = AlgorithmManager::create([
        //     new RS256(),
        // ]);

        // // We instantiate our JWS Verifier.
        // $this->jwsVerifier = new JWSVerifier(
        //     $this->algorithmManager
        // );

        // $this->jsonConverter = new StandardConverter();

    }

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
        echo '<script type="text/javascript">console.log("Initiating access token request")</script>';
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

        if (!is_array(json_decode($content, true))) {
            var_dump($content);
            echo "<br>Invalid response received while expecting access token.";
            exit();
        }
        $content = json_decode($content, true);
        if (isset($content["error_description"])) {
            exit($content["error_description"]);
        } else if (isset($content["error"])) {
            exit($content["error"]);
        } else if (isset($content["access_token"])) {
            $access_token = $content["access_token"];
        } else {
            exit('Invalid response received from OAuth Provider. Contact your administrator for more details.');
        }
        echo '<script type="text/javascript">console.log("Access token received and returned.")</script>';
        return $access_token;
    }

    /**
     * @param $access_token
     * @return mixed
     */
    function get_resource_owner($access_token,$resource_endpoint)
    {
        echo '<script type="text/javascript">console.log("Initiating resource owner information request.")</script>';
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
            var_dump($content);
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
        echo '<script type="text/javascript">console.log("Resource owner email received and returned.")</script>';
        return $email;
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

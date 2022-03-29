<?php

class Curl{
    // Method for getting with curl
    static function get($url, $headers, $body){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        if (curl_errno($curl)){
            echo 'Error : '.curl_error($curl);
        }
        curl_close($curl);

        return $response;
    }

    // Method for posting with curl
    static function post($url, $headers, $body = "", $graph = 0){
        $curl = curl_init();

        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        if ($graph){
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        } else {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($body));
        }
        
        curl_setopt($curl, CURLOPT_TIMEOUT, 0);

        $response = curl_exec($curl);
        if (curl_errno($curl)){
            echo 'Error : '.curl_error($curl);
        }
        curl_close($curl);

        return json_decode($response);
    }

    // Method for setting cors
    static function setCors(){
        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }
    
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
                header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
    
            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
                header("Access-Control-Allow-Headers:        {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
    
            exit(0);
        }
    }

    // Method for setting session
    static function setSession(){
        session_start();
        
        // If no access token or expired token then create one token
        if (!isset($_SESSION['access_token']) || time() > $_SESSION['access_token_expiration']) {
            $response = Authentication::getAuthToken();
            $_SESSION['access_token'] = $response['accessToken'];
            $_SESSION['access_token_expiration'] = $response['expiration'];
        };


        // If no session token then create one token
        if (!isset($_SESSION['session_token'])) {
            $_SESSION['session_token'] = Authentication::getSessionToken($_SESSION['access_token']);
        }

        // If no-results don't exist then initialize
        if (!isset($_SESSION['no-results'])) {
            $_SESSION['no-results'] = 0;
        }
    }
}
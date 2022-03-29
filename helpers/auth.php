<?php
require_once __DIR__."/curl.php";

class Authentication {

    const APIKEY = "nyUl7wzXoKtgoHnd2fB0uRrAv0dDyLC+b4Y6xngpJDY=";
    const SECRET = "eyJhbGciOiJSUzI1NiIsInR5cCI6IkpXVCJ9.eyJwcm9qZWN0IjoieW9kYV9jaGF0Ym90X2VuIn0.anf_eerFhoNq6J8b36_qbD4VqngX79-yyBKWih_eA1-HyaMe2skiJXkRNpyWxpjmpySYWzPGncwvlwz5ZRE7eg";
    const AUTHENTICATION_URL = "https://api.inbenta.io/v1/auth";
    const CONVERSATION_URL = "https://api-gce3.inbenta.io/prod/chatbot/v1/conversation";

    // Method for getting the Authentification Token
    public static function getAuthToken(){
        $headers = [
            "x-inbenta-key: ".SELF::APIKEY,
            "Content-Type: application/json"
        ];

        $body = [
            "secret" => SELF::SECRET
        ];

        $response = Curl::post(SELF::AUTHENTICATION_URL, $headers, $body);

        return [
            'accessToken' => $response->accessToken,
            'expiration' => $response->expiration
        ];
    }

    // Method for getting the Session Token
    public static function getSessionToken($accessToken){
        $headers = [
            "x-inbenta-key: ".SELF::APIKEY,
            "Authorization: Bearer ".$accessToken
        ];

        $response = Curl::post(self::CONVERSATION_URL, $headers);

        return $response->sessionToken;
    }

    // Method for getting a response from chatbot with a message, an accessToken and a sessionToken
    public static function getMessage($message, $accessToken, $sessionToken){
        $headers = [
            'x-inbenta-key: '.self::APIKEY,
            'x-inbenta-session: Bearer '.$sessionToken,
            'Authorization: Bearer '.$accessToken,
            'Content-Type: application/json'
        ];

        $body = [
            'message' => $message
        ];

        $response = Curl::post(self::CONVERSATION_URL.'/message', $headers, $body);

        if (!isset($response->errors)){
            return [ 
                'message' => $response->answers[0]->messageList[0],
                'no-results' => in_array('no-results', $response->answers[0]->flags, true) 
            ];
        } else {
            return [ 
                'message' => "Session expired."
            ];
        }


        
    }

    // Method for setting cors
    public static function setCors(){
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
    public static function setSession(){
        // For a reason i don't understand, the sessions variables works in local but not in heroku and i don't suceed to set up them
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
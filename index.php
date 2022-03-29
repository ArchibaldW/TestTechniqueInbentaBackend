<?php
ini_set('display_errors', 1);
require_once __DIR__."./helpers/auth.php";
require_once __DIR__."./helpers/chatbot.php";

Curl::setCors();
Curl::setSession();

// If we have the session token, then try to get a response
if (isset($_SESSION['session_token'])){
    $message = json_decode(file_get_contents("php://input"),true)["message"];

    // If "force" is in the string, then response with films
    if (ChatBot::force($message) == 2){
        $response = ChatBot::getFilms();
    } else {
        $answer = Authentication::getMessage($message,$_SESSION['access_token'],$_SESSION['session_token']);
        
        $response = ChatBot::getResponse($answer);
    }

    echo $response;
}


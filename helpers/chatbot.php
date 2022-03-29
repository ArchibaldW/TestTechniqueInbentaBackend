<?php
require_once __DIR__."./curl.php";

class ChatBot {
    const GRAPH_URL = 'https://inbenta-graphql-swapi-prod.herokuapp.com/api';

    // Method for knowing if the word "force" is in the string
    public static function force($message){
        if (stripos($message, "force")!==false){
            return 2;
        }
        return $message;
    }

    // Method for getting films when talking about force
    public static function getFilms(){
        $headers = ['Content-Type: application/json'];
        $body = '{"query":"{allFilms{films{title}}}","variables":{}}';

        $response = Curl::post(self::GRAPH_URL, $headers, $body, 1);

        $films = $response->data->allFilms->films;

        $film1 = $films[0]->title;
        $film2 = $films[1]->title;

        return "Seeking for force? Well, find it you can in ".$film1." or maybe ".$film2;
    }

    // Method for getting people when two consecutive no-result
    public static function getPeople(){
        $headers = ['Content-Type: application/json'];
        $body = '{"query":"{allPeople{people{name}}}","variables":{}}';

        $response = Curl::post(self::GRAPH_URL, $headers, $body, 1);

        $people = $response->data->allPeople->people;

        $people1 = $people[0]->name;
        $people2 = $people[1]->name;

        return "I haven't found any results, but certainly you can ask to ".$people1." or ".$people2.", they can help you great";
    }

    public static function getResponse($answer){
        // If session expired, then get another Session Token and response with session expired message
        if ($answer["message"] == "Session expired."){
            $_SESSION['session_token'] = Authentication::getSessionToken($_SESSION['access_token']);
            $response = "Your session expired, please reload";

        // If no result then increment no-result session variable,
        // Then, 
        //  - if no-result session variable = 2, reponse with people message and set no-result session variable to 0
        //  - else response with answer normal message
        } else if($answer["no-results"] == 1){
            $_SESSION["no-results"] += 1;
            if ($_SESSION["no-results"] == 2){
                $response = ChatBot::getPeople();
                $_SESSION["no-results"] = 0;
            } else {
                $response = $answer["message"].$_SESSION["no-results"];
            }
        
        // If all good, then set no-result session variable to 0 and response with answer normal message
        } else {
            $_SESSION['no-results'] = 0;
            $response = $answer['message'];
        }

        return $response;
    }
}
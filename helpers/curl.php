<?php

class Curl{
    // Method for getting with curl
    public static function get($url, $headers, $body){
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
    public static function post($url, $headers, $body = "", $graph = 0){
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
}
<?php

namespace App\Models;

use CodeIgniter\Model;



class NectecSimilarityModel extends Model
{
    private $apiKey = "7hBE5pkqBlocEv6J5X9ppcp4wYkmkMPe";

    private $hostURL = "https://api.aiforthai.in.th/";

    private $reqeustType = "GET";

    private function cURLrequest($url, $type, $apiKey){
      $curl = curl_init();
      curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $type,
        CURLOPT_HTTPHEADER => array(
          "Apikey: ".$apiKey,
          "Accept: application/json"
        )
      ));
      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);

      return json_decode($response);
    }

    private function genRequestURL($search_word, $method, $numword, $model_sim){
      $callUrL = $this->hostURL.$method;
      $searchData = "?word=".$search_word."&numword=".$numword."&model=".$model_sim;
      $requestURL = $callUrL.$searchData;

      return $requestURL;

    }

    public function getNectecmethod($search_word="", $method="thaiwordsim", $model_sim = "thwiki", $numword=5){

      $requestURL = $this->genRequestURL($search_word, $method, $numword, $model_sim);
      $response = $this->cURLrequest($requestURL, $this->reqeustType, $this->apiKey);

      return $response;
    }//getNectecmethod

}

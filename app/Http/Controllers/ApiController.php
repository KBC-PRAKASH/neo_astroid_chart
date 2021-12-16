<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

class ApiController extends Controller
{
    public function getDataFromApi(Request $req){
        $data = $req->all();
        $API_KEY = "Gfi0AEDpaIMRHcAeYpSwSOA0wLEdgcDvd9K6Q3VW";
        $start_date = date_format(new DateTime($data['from_date']), 'Y-m-d');
        $end_date = date_format(new DateTime($data['to_date']), 'Y-m-d');
        // print_r($data);die;

        $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=$start_date&end_date=$end_date&api_key=$API_KEY";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        return $result;
        
    }
}

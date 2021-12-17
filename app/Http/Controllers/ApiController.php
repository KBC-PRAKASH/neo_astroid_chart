<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DateTime;

class ApiController extends Controller
{
    public function getDataFromApi(Request $req){
        $data = $req->all();
        $res = $this->getDataFromNeoApi($data);
        if($res['http_status'] == 200){
            return response()->json(['status' => 200, 'data' => $res]);
        }else{
            return response()->json(['status' => 400, 'data' => $res]);
        }
        
    }


    protected function getDataFromNeoApi($data){
        $API_KEY = "Gfi0AEDpaIMRHcAeYpSwSOA0wLEdgcDvd9K6Q3VW";
        $start_date = date_format(new DateTime($data['from_date']), 'Y-m-d');
        $end_date = date_format(new DateTime($data['to_date']), 'Y-m-d');

        $url = "https://api.nasa.gov/neo/rest/v1/feed?start_date=$start_date&end_date=$end_date&api_key=$API_KEY";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if($httpcode == 200){
            $api_result = json_decode($result, true);
            $final_result['http_status'] = 200;
            $final_result['result'] = $api_result;
            $final_result['get_fastest_astroid_speed'] = $this->calculate_fastest_astroid_speed($api_result['near_earth_objects']);
            $final_result['get_closest_astroid_distance'] = $this->calculate_closest_astroid_distance($api_result['near_earth_objects']);
            $final_result['average_size_of_astroid_km'] = $this->calculate_average_size_of_astroid_km($api_result['near_earth_objects']);
            // print_r($final_result);die;
            return $final_result;
        }else{
            $api_result = json_decode($result, true);
            $explodeStr = explode('-',$api_result['error_message']);
            $final_result['http_status'] = 400;
            $final_result['error_message'] = $explodeStr[4];
            return $final_result;
        }
        
        
    }

    protected function calculate_fastest_astroid_speed($sdata){
        $fastest_asteroid_km = [];
        foreach($sdata as $sKey => $sVal){
            foreach($sVal as $rvKey => $rvVal){
                $fastest_asteroid_km[] = $rvVal['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'];
            }
        }

        $max_fastest_asteroid_km = max($fastest_asteroid_km);
        return $get_astroid_details = $this->_get_astroid_details($sdata, $max_fastest_asteroid_km);
    }
    protected function calculate_closest_astroid_distance($sdata){
        $closest_asteroid_dist = [];
        foreach($sdata as $sKey => $sVal){
            foreach($sVal as $rvKey => $rvVal){
                $closest_asteroid_dist[] = $rvVal['close_approach_data'][0]['miss_distance']['kilometers'];
            }
        }

        $min_closest_asteroid_dist = min($closest_asteroid_dist);
        return $get_closest_astroid_details = $this->_get_closest_astroid_details($sdata, $min_closest_asteroid_dist);
    }

    protected function _get_astroid_details($sdata, $max_fastest_asteroid_km){
        $fastest_astroid_details = [];
        foreach($sdata as $sKey => $sVal){
            foreach($sVal as $rvKey => $rvVal){
                if($rvVal['close_approach_data'][0]['relative_velocity']['kilometers_per_hour'] == $max_fastest_asteroid_km){
                    $fastest_astroid_details['speed_km'] = $max_fastest_asteroid_km;
                    $fastest_astroid_details['astroid_id'] = $rvVal['id'];
                }
            }
        }
        return $fastest_astroid_details;
    }
    protected function _get_closest_astroid_details($sdata, $min_closest_asteroid_dist){
        $closest_astroid_details = [];
        foreach($sdata as $sKey => $sVal){
            foreach($sVal as $rvKey => $rvVal){
                if($rvVal['close_approach_data'][0]['miss_distance']['kilometers'] == $min_closest_asteroid_dist){
                    $closest_astroid_details['distance_km'] = $min_closest_asteroid_dist;
                    $closest_astroid_details['astroid_id'] = $rvVal['id'];
                }
            }
        }
        return $closest_astroid_details;
    }

    protected function calculate_average_size_of_astroid_km($stroidData){
        $total_size_of_astroid = 0;
        $total_astroid_count = 0;   
        foreach($stroidData as $sKey => $sVal){
            foreach($sVal as $rvKey => $rvVal){
                $total_size_of_astroid += $rvVal['estimated_diameter']['kilometers']['estimated_diameter_max'];
                $total_astroid_count++;
            }
        }
        return $total_size_of_astroid / $total_astroid_count;
    }


}

<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Exchange_Data') ){
    class TSSL_Exchange_Data{
        public function __construct() {

        }

        /**
         * Exchange Token For Data Method.
         *
         * @since  1.0.0
         *
         * @param  $url     The api url.
         * @return array    The access token data.
         */
        function TSSL_GET( $url ){

            $access_token_api = $url;
            
            $response_json = wp_remote_get( $access_token_api );
            
            $obj_json = wp_remote_retrieve_body( $response_json );
                    
            $data_array = json_decode($obj_json, true);

            return $data_array;
        }

        function TSSL_POST( $url ){

            $access_token_api = $url;
            
            $response_json = wp_remote_post( $access_token_api );
            
            $obj_json = wp_remote_retrieve_body( $response_json );
                    
            $data_array = json_decode($obj_json, true);

            return $data_array;
        }

        function TSSL_REQUEST( $url, $array ){

            $access_token_api = $url;
            
            $response_json = wp_remote_request($access_token_api, array(
                                'headers'     => array( 
                                                    'Content-Type'  => 'application/json; charset=utf-8',
                                                    'Accept'        => 'application/json'
                                                ),
                                'body'        => json_encode($array),
                                'method'      => 'POST',
                                'data_format' => 'body',
                            ));
            
            $obj_json = wp_remote_retrieve_body( $response_json );
                    
            $data_array = json_decode($obj_json, true);

            return $data_array;
        }

        function TSSL_REQUEST_Basic64( $url, $array, $client_id, $secret ){

            $access_token_api = $url;
            
            $response_json = wp_remote_request($access_token_api, array(
                                'headers'     => array(
                                                    'Authorization' => 'Basic ' . base64_encode( $client_id . ':' . $secret ),
                                                ),
                                'body'        => json_encode($array),
                                'method'      => 'POST',
                                'data_format' => 'body',
                            ));
            
            $obj_json = wp_remote_retrieve_body( $response_json );
                    
            $data_array = json_decode($obj_json, true);

            return $data_array;
        }

        function TSSL_REQUEST_Bearer_GET( $url, $access_token ){

            $access_token_api = $url;
            
            $response_json = wp_remote_request($access_token_api, array(
                                'headers'     => array(
                                                    'Content-Type'  => 'application/json',
                                                    'Authorization' => 'Bearer ' . $access_token,
                                                ),
                                'method'      => 'GET',
                                'data_format' => 'body',
                            ));
            $obj_json = wp_remote_retrieve_body( $response_json );
                    
            $data_array = json_decode($obj_json, true);

            return $data_array;
        }
    }
}
<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Data_Process') ){
    class TSSL_Data_Process{
        public function __construct() {

        }
        
        /**
         *  Filter Data from Json
         *
         * @param $social_type  The social type
         * 
         * @return Object       Return an object.
         */

        function Data_Process( $social_type ){

            $json_data = new TSSL_Json_Data();

            $json = $json_data->TSSL_Json();
                    
            $data = json_decode($json);

            if( !empty( $social_type ) ){
                foreach( $data as $key => $obj ){
                    if( $obj->type == $social_type ){
                        $object = $data[$key];
                        return $object;
                        break;
                    }
                }
            }
        }

        function Data_Process_Array(){

            $json_data = new TSSL_Json_Data();

            $json = $json_data->TSSL_Json();
                    
            $data = json_decode($json);

            return $data;
        }

    }
}
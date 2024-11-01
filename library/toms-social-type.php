<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Social_Type') ){
    class TSSL_Social_Type{
        public function __construct() {

        }

        /**
         *  TomS Social Login Type Check
         *
         * @param $social_type    The social type.
         * 
         * @return yes|no       If the type is match return yes else return no.
         */
        function TSSL_Type_Check( $social_type ){

            $data   = new TSSL_Data_Process;
            $obj    = $data->Data_Process( $social_type );

            if( !empty($social_type) && !empty( $obj )  ){
                if( $social_type === $obj->type ){
                    $type_check = 'yes';
                }else{
                    $type_check = 'no';
                }
                return $type_check;
            }
        }
    }
}
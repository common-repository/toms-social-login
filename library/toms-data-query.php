<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Data_Query') ){
    class TSSL_Data_Query{
        public function __construct() {

        }
        
        /**
         *  Query TomS Social Login User Info
         *
         * @param $QueryItem    Support this items: id, userid, socialtype, unionid, useremail, openid, avatar, logintimes, nickname
         * @param $QueryValue   The value of the query item.
         * @param $charset      The charset type: %s, %d
         * 
         * @return Object       Return an object of an array[0] of the query item.
         */
        function TSSL_Query($QueryItem1, $QueryValue1, $charset1, $QueryItem2, $QueryValue2, $charset2){

            global $wpdb;
            $QueryName1      = $QueryItem1;
            $QueryName2      = $QueryItem2;
            $tomsTable      = $wpdb->prefix . 'toms_social_login';
            $tomsQuery      = $wpdb->prepare("SELECT * FROM $tomsTable WHERE $QueryName1 = $charset1 AND $QueryName2 = $charset2", array( $QueryValue1, $QueryValue2 ));
            $tomsUserData   = $wpdb->get_results($tomsQuery);

            return $tomsUserData;

        }

        function TSSL_Query_Single($QueryItem1, $QueryValue1, $charset1){

            global $wpdb;
            $QueryName1      = $QueryItem1;
            $tomsTable      = $wpdb->prefix . 'toms_social_login';
            $tomsQuery      = $wpdb->prepare("SELECT * FROM $tomsTable WHERE $QueryName1 = $charset1", array( $QueryValue1 ));
            $tomsUserData   = $wpdb->get_results($tomsQuery);

            return $tomsUserData;

        }
    }
}
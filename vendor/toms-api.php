<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( !class_exists('TomSAPI') ){
    class TomSAPI{
        public function __construct() {
            add_action( 'rest_api_init', array($this, 'TomS_API'));
        }

        function TomS_API(){
            register_rest_route( 'toms/v1', 'toms-social-login', array(
                'methods'   => WP_REST_SERVER::READABLE,
                'callback'  => array($this, 'TomS_Social_Login')
            ) );
        }

        function TomS_Social_Login(){
            return array(
                'title' => 'TomS API',
                'content' => 'TomS Social Login'
            );
        }
    }

    $TomSAPI = new TomSAPI();
}
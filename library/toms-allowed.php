<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TomS_Allowed_HTML') ){
    class TomS_Allowed_HTML{
        public function __construct() {

        }

        /**
         *  TomS Allowed HTML
         * @return array
        */
        function Allowed_Html(){
            return $allowed_html =[
                                    'style' => [
                                        'id'        => [],
                                        'class'     => [],
                                        'name'      => [],
                                        '@media'    => [],
                                        'max-width' => [],
                                        'min-width' => [],
                                    ],
                                    'div' => [
                                        'class' => [],
                                        'id'    => [],
                                        'name'  => []
                                    ],
                                    'span' => [
                                        'class' => [],
                                        'id'    => [],
                                        'name'  => [],
                                        'title' => [],
                                        'style' => [],
                                        'order_key' => [],
                                        'data-type'  => [],
                                        'toms-label-tooltip' => []
                                    ],
                                    'img'   => [
                                        'title' => [],
                                        'src' => [],
                                        'alt' => []
                                    ],
                                    'input' => [
                                        'id'    => [],
                                        'class' => [],
                                        'type'  => [],
                                        'name'  => [],
                                        'value' => [],
                                        'data-key'  => [],
                                        'onfocus'   => [],
                                        'onblur'    => [],
                                        'checked'   => []
                                    ],
                                    'label' => [
                                        'id'    => [],
                                        'class' => []
                                    ],
                                    'i' => [
                                        'id'    => [],
                                        'class' => []
                                    ],
                                    'a' => [
                                        'href'      => [],
                                        'class'     => [],
                                        'id'        => [],
                                        'onclick'   => []
                                    ],
                                    'button' => [
                                        'class' => [],
                                        'id'    => [],
                                        'disabled'  => []
                                    ],
                                    'strong' => [],
                                    'script' => [
                                        'type' => []
                                    ]
                                ];
        }

        /**
         *  TomS Allowed Protocols
         * @return array
        */
        function Allowed_Protocols(){
            return $protocols = array( 'data', 'http', 'https' );
        }
    }
}
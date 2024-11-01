<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TomS_Default_Supported_Plugins') ){
    class TomS_Default_Supported_Plugins{
        public function __construct() {
            add_action( 'plugins_loaded', array($this, 'TomS_Supported_plugins') );
        }
        function TomS_Supported_plugins(){
            //Login Button
            function TSSL_Login(){
                if( !is_user_logged_in() ){
                    $allowed            = new TomS_Allowed_HTML();
                    $allowed_html       = $allowed->Allowed_Html();
                    $allowed_protocols  = $allowed->Allowed_Protocols();
                    $html = '';
                    $html .=  do_shortcode( '[TSSL_Login_Button]' );
                    echo wp_kses( $html, $allowed_html, $allowed_protocols );
                }
            }
            
            //Binding Button
            function TSSL_Bingding(){
                $allowed            = new TomS_Allowed_HTML();
                $allowed_html       = $allowed->Allowed_Html();
                $allowed_protocols  = $allowed->Allowed_Protocols();
                if( is_user_logged_in() ){
                    $html = '';
                    $html .=  do_shortcode( '[TSSL_Binding_Button]');
                    echo wp_kses( $html, $allowed_html, $allowed_protocols );
                }
            }

            //Woocommerce
            if( class_exists( 'Woocommerce' ) ){
                //Woocommerce Login/Register forms
                add_action( 'woocommerce_login_form', 'TSSL_Login' );
                add_action( 'woocommerce_register_form', 'TSSL_Login' );
                
                //Woocommerce My Account Page Dashboard/Edit Account Fields
                //add_action( 'woocommerce_account_dashboard', 'TSSL_Bingding' );
                add_action( 'woocommerce_edit_account_form', 'TSSL_Bingding' );
            }

            //Ultimate Member
            if( class_exists( 'UM' ) ){
                //Ultimate Member Login/Register forms
                add_action( 'um_after_form_fields', 'TSSL_Login' );

                //Ultimate Member Account Tab
                add_action('um_after_account_general', 'TSSL_Bingding' );
            }

            //User Registration
            if ( class_exists( 'UserRegistration' ) ){
                function TomS_User_Registration(){
                    $allowed            = new TomS_Allowed_HTML();
                    $allowed_html       = $allowed->Allowed_Html();
                    $allowed_protocols  = $allowed->Allowed_Protocols();
                    $html = '<style>
                                .user-registration .register .toms-social-login{
                                    padding: 10px 10px 0 10px;
                                }
                            </style>';
                    $html .= TSSL_Login();
                    echo wp_kses( $html, $allowed_html, $allowed_protocols );
                }
                //User Registration Login/Register forms
                add_action('user_registration_after_field_row', 'TomS_User_Registration' );
                add_action('user_registration_login_form', 'TomS_User_Registration' );
                
                //User Registration Account dashboard
                add_action('user_registration_account_dashboard', 'TSSL_Bingding' );

                //Update TSSL user info for User Registration
                function TomS_UR_update_user_meta( $meta_array, $user_id){
                    if( !empty( $user_id ) ){
                        $toms_ur_form_id = get_user_meta( $user_id, 'ur_form_id' );

                        if( empty( $toms_ur_form_id ) ){
                            $default_form_page_id = get_option( 'user_registration_default_form_page_id' );

                            $meta_key = array(
                                'ur_form_id' => $default_form_page_id
                            );
                            $meta_array = array_merge( $meta_array, $meta_key );

                            return $meta_array;
                        }
                    }
                    return $meta_array;
                }
                add_filter( 'TomSUserMeta', 'TomS_UR_update_user_meta', 10, 2 );

                //After user login redirect to user registration my account page
                function redirect_to_ur_myaccount_page( $redirect_url ){
                    $ur_myaccount_id = get_option( 'user_registration_myaccount_page_id' );
                    if( !empty( $ur_myaccount_id ) ){
                        $redirect_url = home_url() . '/?page_id=' . $ur_myaccount_id;
                    }

                    return $redirect_url;
                }
                add_filter( 'TomSRedirectURL', 'redirect_to_ur_myaccount_page' );
            }
        }
    }

    new TomS_Default_Supported_Plugins();
}


<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TomSPaypalLogin') ){
    class TomSPaypalLogin{

        public function __construct() {
            add_action( 'init', array($this, 'TomS_Login_Process') );
        }

        function TomS_Login_Process(){

            $state = isset($_GET['state']) ? sanitize_textarea_field( $_GET['state'] ) : '';
            //Get Social Type
            preg_match("/^paypal/", $state, $matche_type);
            $type = isset($matche_type[0]) ? $matche_type[0] : '';

            //Get Nonce
            $string = preg_replace('/^paypal/', '', $state);
            $nonce = preg_replace('/toms_state.*/', '', $string);
            $nonce_name = '';

            //Get Request Url
            preg_match("/toms_state.*/", $string, $matche_url);
            $request_url = isset($matche_url[0]) ? $matche_url[0] : '';
            $request_url = preg_replace('/^toms_state/', '', $request_url);
            $request_url = home_url( $request_url );

            $TSSL_data  = new TSSL_Data_Process();
            $obj        = $TSSL_data->Data_Process( $type );

            if( !empty( $obj ) ){
                $nonce_name         = $obj->nonce_name;
                $client_id          = $obj->client_id;
                $secret             = $obj->secret;
                $access_token_URL   = $obj->access_token_URL->url;
                $grant_type         = $obj->access_token_URL->grant_type;
                $fetch_url          = $obj->fetch_user_info->url;
                $redirect_uri       = $obj->params->redirect_uri;
            }


            if( !empty( $type ) && $type === 'paypal' && isset($_GET['code']) && wp_verify_nonce($nonce, $nonce_name) ){
                
                //Exchange  access_token,id_token
                $code = sanitize_textarea_field( $_GET['code'] );

                $ac_params = [
                    'grant_type'    => $grant_type,
                    'code'          => $code
                ];

                $access_token_uri = $access_token_URL . http_build_query( $ac_params );

                $TSSL_Exchange_Data = new TSSL_Exchange_Data();
                
                $access_token_data = $TSSL_Exchange_Data->TSSL_REQUEST_Basic64( $access_token_uri, '', $client_id, $secret);

                if( isset($access_token_data['error_description']) ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $access_token_data['error_description'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }

                $token  = isset($access_token_data['access_token']) && !empty($access_token_data['access_token']) ? sanitize_textarea_field( $access_token_data['access_token'] ) : '';

                //Exchange user info
                $fetch_url_params = [
                    'schema'    => 'paypalv1.1'
                ];

                $fetch_user_data_url = $fetch_url . http_build_query( $fetch_url_params );
                
                $user_array = $TSSL_Exchange_Data->TSSL_REQUEST_Bearer_GET( $fetch_user_data_url, $token );

                //Save info to Session
                if (!session_id()) {
                    session_start();
                }

                $_SESSION['toms_paypal']=$user_array;

                if( isset($_SESSION['toms_paypal']['name']) && $_SESSION['toms_paypal']['name'] == 'AUTHENTICATION_FAILURE' ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $_SESSION['toms_paypal']['message'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }
                //Get Session info
                $user_id            = isset($_SESSION['toms_paypal']['user_id']) && !empty($_SESSION['toms_paypal']['user_id']) ? sanitize_textarea_field( $_SESSION['toms_paypal']['user_id'] ) : '';

                //Filter user_id only
                preg_match("/user\/.*/", $user_id, $matche_username);
                $user_id = isset($matche_username[0]) ? $matche_username[0] : '';
                $user_id = preg_replace('/^user\//', '', $user_id);

                $unionid            = $user_id;
                $openid             = isset($_SESSION['toms_paypal']['payer_id']) && !empty($_SESSION['toms_paypal']['payer_id']) ? sanitize_textarea_field( $_SESSION['toms_paypal']['payer_id'] ) : '';
                $avatar             = isset($_SESSION['toms_paypal']['picture']) && !empty($_SESSION['toms_paypal']['picture']) ? sanitize_textarea_field( $_SESSION['toms_paypal']['picture'] ) : '';
                $nickname           = isset($_SESSION['toms_paypal']['name']) && !empty($_SESSION['toms_paypal']['name']) ? sanitize_textarea_field( $_SESSION['toms_paypal']['name'] ) : $unionid;
                $email              = isset($_SESSION['toms_paypal']['emails'][0]['value']) && !empty($_SESSION['toms_paypal']['emails'][0]['value']) ? sanitize_textarea_field( $_SESSION['toms_paypal']['emails'][0]['value'] ) : '';

                // Associate wordpress user
                $TomSUserRequest = new TomSUserRequest();
                $user = $TomSUserRequest->TomS_User_Request($unionid, $type, $avatar, $email, $openid, $nickname, $request_url);

            }
        }
    }
    new TomSPaypalLogin();
}
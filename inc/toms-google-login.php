<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TomSGoogleLogin') ){
    class TomSGoogleLogin{

        public function __construct() {
            add_action( 'init', array($this, 'TomS_Login_Process') );
        }

        function TomS_Login_Process(){

            $state = isset($_GET['state']) ? sanitize_textarea_field( $_GET['state'] ) : '';
            //Get Social Type
            preg_match("/^google/", $state, $matche_type);
            $type = isset($matche_type[0]) ? $matche_type[0] : '';

            //Get Nonce
            $string = preg_replace('/^google/', '', $state);
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

            if( !empty( $type ) && $type === 'google' && isset($_GET['code']) && wp_verify_nonce($nonce, $nonce_name) ){
                
                //Exchange  access_token,id_token
                $code = sanitize_textarea_field( $_GET['code'] );

                $ac_params = [
                    'client_id'     => $client_id,
                    'client_secret' => $secret,
                    'grant_type'    => $grant_type,
                    'redirect_uri'  => $redirect_uri,
                    'code'          => $code
                ];

                $access_token_uri = $access_token_URL . http_build_query( $ac_params );

                $TSSL_Exchange_Data = new TSSL_Exchange_Data();
                
                $access_token_data = $TSSL_Exchange_Data->TSSL_POST( $access_token_uri );

                if( isset($access_token_data['error_description']) ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $access_token_data['error_description'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }

                $token      = isset($access_token_data['access_token']) && !empty($access_token_data['access_token']) ? sanitize_textarea_field( $access_token_data['access_token'] ) : '';
                $id_token   = isset($access_token_data['id_token']) && !empty($access_token_data['id_token']) ? sanitize_textarea_field( $access_token_data['id_token'] ) : '';

                //Exchange user info
                $fetch_url_params = [
                    'access_token'  => $token
                ];

                $fetch_user_data_url = $fetch_url . http_build_query( $fetch_url_params );
                
                $user_array = $TSSL_Exchange_Data->TSSL_GET( $fetch_user_data_url );

                //Save info to Session
                if (!session_id()) {
                    session_start();
                }

                $_SESSION['toms_google']=$user_array;

                if( isset($_SESSION['toms_google']['error_description']) ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $_SESSION['toms_google']['error_description'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }
                //Get Session info
                $openid             = isset($_SESSION['toms_google']['openid']) && !empty($_SESSION['toms_google']['openid']) ? sanitize_textarea_field( $_SESSION['toms_google']['openid'] ) : '';
                $unionid            = isset($_SESSION['toms_google']['sub']) && !empty($_SESSION['toms_google']['sub']) ? sanitize_textarea_field( $_SESSION['toms_google']['sub'] ) : '';
                $avatar             = isset($_SESSION['toms_google']['picture']) && !empty($_SESSION['toms_google']['picture']) ? sanitize_textarea_field( $_SESSION['toms_google']['picture'] ) : '';
                $nickname           = isset($_SESSION['toms_google']['name']) && !empty($_SESSION['toms_google']['name']) ? sanitize_textarea_field( $_SESSION['toms_google']['name'] ) : $unionid;
                $email              = isset($_SESSION['toms_google']['email']) && !empty($_SESSION['toms_google']['email']) ? sanitize_textarea_field( $_SESSION['toms_google']['email'] ) : '';
                $locale             = isset($_SESSION['toms_google']['locale']) && !empty($_SESSION['toms_google']['locale']) ? sanitize_textarea_field( $_SESSION['toms_google']['locale'] ) : '';

                //Associate wordpress user
                $TomSUserRequest = new TomSUserRequest();
                $user = $TomSUserRequest->TomS_User_Request($unionid, $type, $avatar, $email, $openid, $nickname, $request_url);
            }
        }
    }
    new TomSGoogleLogin;
}
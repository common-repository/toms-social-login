<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TomSFacebookLogin') ){
    class TomSFacebookLogin{

        public function __construct() {
            add_action( 'init', array($this, 'TomS_Login_Process') );
        }

        function TomS_Login_Process(){

            $state = isset($_GET['state']) ? sanitize_textarea_field( $_GET['state'] ) : '';
            //Get Social Type
            preg_match("/^facebook/", $state, $matche_type);
            $type = isset($matche_type[0]) ? $matche_type[0] : '';

            //Get Nonce
            $string = preg_replace('/^facebook/', '', $state);
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
                $fetch_info_url     = $obj->fetch_user_info->user_info_url;
                $redirect_uri       = $obj->params->redirect_uri;
            }

            if( !empty( $type ) && $type === 'facebook' && isset($_GET['code']) && wp_verify_nonce($nonce, $nonce_name) ){

                //Exchange token
                $code = sanitize_textarea_field( $_GET['code'] );

                $ac_params = [
                    'client_id'     => $client_id,
                    'client_secret' => $secret,
                    'redirect_uri'  => $redirect_uri,
                    'code'          => $code
                ];

                $access_token_uri = $access_token_URL . http_build_query( $ac_params );

                $TSSL_Exchange_Data = new TSSL_Exchange_Data();
                
                $access_token_data = $TSSL_Exchange_Data->TSSL_GET( $access_token_uri );

                if( isset($access_token_data['error']['message']) ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $access_token_data['error']['message'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }

                $token      = isset($access_token_data['access_token']) && !empty($access_token_data['access_token']) ? sanitize_textarea_field( $access_token_data['access_token'] ) : '';

                //Exchange User id
                $fetch_url_params = [
                    'input_token'   => $token,
                    'access_token'  => $token
                ];

                $fetch_user_data_url = $fetch_url . http_build_query( $fetch_url_params );
                
                $user_array = $TSSL_Exchange_Data->TSSL_GET( $fetch_user_data_url );

                if( isset($user_array['error']['message']) ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $user_array['error']['message'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }

                $user_id = isset( $user_array['data']['user_id'] ) && !empty( $user_array['data']['user_id'] ) ? sanitize_textarea_field( $user_array['data']['user_id'] ) : '';

                //Exchange user info
                $fetch_info_url_params = [
                    'fields'   => 'id,name,email,picture',
                    'access_token'  => $token
                ];

                $fetch_user_info_url = $fetch_info_url . $user_id. '?' . http_build_query( $fetch_info_url_params );

                $userinfo_array = $TSSL_Exchange_Data->TSSL_GET($fetch_user_info_url);

                //Save info to Session
                if (!session_id()) {
                    session_start();
                }

                $_SESSION['toms_facebook']=$userinfo_array;

                if( isset($session_data['error']['message']) ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $session_data['error']['message'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }

                //Get Session info
                $openid             = isset($user_array['data']['app_id']) && !empty($user_array['data']['app_id']) ? sanitize_textarea_field( $user_array['data']['app_id'] ) : '';
                $unionid            = isset($_SESSION['toms_facebook']['id']) && !empty($_SESSION['toms_facebook']['id']) ? sanitize_textarea_field( $_SESSION['toms_facebook']['id'] ) : '';
                $avatar             = isset($_SESSION['toms_facebook']['picture']['data']['url']) && !empty($_SESSION['toms_facebook']['picture']['data']['url']) ? sanitize_textarea_field( $_SESSION['toms_facebook']['picture']['data']['url'] ) : '';
                $nickname           = isset($_SESSION['toms_facebook']['name']) && !empty($_SESSION['toms_facebook']['name']) ? sanitize_textarea_field( $_SESSION['toms_facebook']['name'] ) : $unionid;
                $email              = isset($_SESSION['toms_facebook']['email']) && !empty($_SESSION['toms_facebook']['email']) ? sanitize_email( $_SESSION['toms_facebook']['email'] ) : '';

                //Associate wordpress user
                $TomSUserRequest = new TomSUserRequest();
                $user = $TomSUserRequest->TomS_User_Request($unionid, $type, $avatar, $email, $openid, $nickname, $request_url);
            }
        }
    }
    new TomSFacebookLogin();
}
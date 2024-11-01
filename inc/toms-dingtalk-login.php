<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TomSDingTalkLogin') ){
    class TomSDingTalkLogin{

        public function __construct() {
            add_action( 'init', array($this, 'TomS_Login_Process') );
        }

        function TomS_Login_Process(){

            $state = isset($_GET['state']) ? sanitize_textarea_field( $_GET['state'] ) : '';
            //Get Social Type
            preg_match("/^dingtalk/", $state, $matche_type);
            $type = isset($matche_type[0]) ? $matche_type[0] : '';

            //Get Nonce
            $string = preg_replace('/^dingtalk/', '', $state);
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
                $persistent_code_url= $obj->fetch_user_info->persistent_code_url;
                $sns_token_url      = $obj->fetch_user_info->sns_token_url;
                $get_userinfo_url   = $obj->fetch_user_info->get_userinfo_url;
                $redirect_uri       = $obj->params->redirect_uri;
            }

            if( !empty( $type ) && $type === 'dingtalk' && isset($_GET['code']) && wp_verify_nonce($nonce, $nonce_name) ){
                
                //Exchange token
                $code = sanitize_textarea_field( $_GET['code'] );

                $ac_params = [
                    'appid'         => $client_id,
                    'appsecret'     => $secret
                ];

                $access_token_uri   = $access_token_URL . http_build_query( $ac_params );

                $TSSL_Exchange_Data = new TSSL_Exchange_Data();
                
                $access_token_data  = $TSSL_Exchange_Data->TSSL_GET( $access_token_uri );

                $token  = isset($access_token_data['access_token']) && !empty($access_token_data['access_token']) ? sanitize_textarea_field( $access_token_data['access_token'] ) : '';
               
                //Exchange unionid, openid, persistent_code
                $get_persistent_param = [
                    'access_token' => $token
                ];
                $get_persistent_url = $persistent_code_url . http_build_query( $get_persistent_param );

                $persistent_json_param = [
                    'tmp_auth_code' => $code
                ];
                $persistent_data = $TSSL_Exchange_Data->TSSL_REQUEST( $get_persistent_url, $persistent_json_param );
                
                if( isset($access_token_data['errmsg']) && $access_token_data['errmsg'] != 'ok' ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $access_token_data['errmsg'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }

                $unionid            = isset($persistent_data['unionid']) && !empty($persistent_data['unionid']) ? sanitize_textarea_field( $persistent_data['unionid'] ) : '';
                $openid             = isset($persistent_data['openid']) && !empty($persistent_data['openid']) ? sanitize_textarea_field( $persistent_data['openid'] ) : '';
                $persistent_code    = isset($persistent_data['persistent_code']) && !empty($persistent_data['persistent_code']) ? sanitize_textarea_field( $persistent_data['persistent_code'] ) : '';

                //Exchange sns_token
                $get_sns_token_param = [
                    'access_token' => $token
                ];
                $get_sns_token_url  = $sns_token_url . http_build_query( $get_sns_token_param );

                $sns_token_json_params = [
                    'openid'            => $openid,
                    'persistent_code'   => $persistent_code
                ];
                $sns_token_data = $TSSL_Exchange_Data->TSSL_REQUEST( $get_sns_token_url, $sns_token_json_params );

                if( isset($sns_token_data['errmsg']) && $sns_token_data['errmsg'] != 'ok' ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $sns_token_data['errmsg'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }

                $sns_token      = isset($sns_token_data['sns_token']) && !empty($sns_token_data['sns_token']) ? sanitize_textarea_field( $sns_token_data['sns_token'] ) : '';

                //EXchange User info
                $sns_token_param = [
                    'sns_token' => $sns_token
                ];
                $get_userinfo_url   = $get_userinfo_url . http_build_query( $sns_token_param );                

                $sns_data           = $TSSL_Exchange_Data->TSSL_GET( $get_userinfo_url );

                if( isset( $sns_data['errmsg']) && $sns_data['errmsg'] != 'ok' ){
                    wp_die('<strong>' . __( 'Error:', 'toms-social-login') . '</strong> <span style="color: red;">' . ucwords( esc_textarea( $sns_data['errmsg'] ) ) . ' !</span><br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }
                
                //Save info to Session
                if (!session_id()) {
                    session_start();
                }
                $_SESSION['toms_dingtalk']   = isset( $sns_data['user_info'] ) ? $sns_data['user_info'] : '';
                
                //Get Session info
                $unionid            = isset($_SESSION['toms_dingtalk']['unionid']) && !empty($_SESSION['toms_dingtalk']['unionid']) ? sanitize_textarea_field( $_SESSION['toms_dingtalk']['unionid'] ) : '';
                $openid             = isset($_SESSION['toms_dingtalk']['openid']) && !empty($_SESSION['toms_dingtalk']['openid']) ? sanitize_textarea_field( $_SESSION['toms_dingtalk']['openid'] ) : '';
                $nickname           = isset($_SESSION['toms_dingtalk']['nick']) && !empty($_SESSION['toms_dingtalk']['nick']) ? sanitize_textarea_field( $_SESSION['toms_dingtalk']['nick'] ) : $unionid;
                $avatar             = isset($_SESSION['toms_dingtalk']['avatar']) && !empty($_SESSION['toms_dingtalk']['avatar']) ? sanitize_textarea_field( $_SESSION['toms_dingtalk']['avatar'] ) : '';
                
                //Associate wordpress user
                $TomSUserRequest = new TomSUserRequest();
                $user = $TomSUserRequest->TomS_User_Request($unionid, $type, $avatar, '', $openid, $nickname, $request_url);
            }
        }
    }
    new TomSDingTalkLogin();
}
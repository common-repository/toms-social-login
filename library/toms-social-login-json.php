<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Json_Data') ){
    class TSSL_Json_Data{
        public function __construct() {

        }

        function TSSL_Json(){

            $state              = wp_create_nonce( 'toms_social_login_nonce' );

            $style              = get_option( 'toms_social_login_style', 'square-icon' );

            $wechat_enabled     = get_option( 'toms_social_login_wechat_enabled' );
            $qq_enabled         = get_option( 'toms_social_login_qq_enabled' );
            $weibo_enabled      = get_option( 'toms_social_login_weibo_enabled' );
            $dingtalk_enabled   = get_option( 'toms_social_login_dingtalk_enabled' );
            $github_enabled     = get_option( 'toms_social_login_github_enabled' );
            $google_enabled     = get_option( 'toms_social_login_google_enabled' );
            $facebook_enabled   = get_option( 'toms_social_login_facebook_enabled' );
            $paypal_enabled     = get_option( 'toms_social_login_paypal_enabled' );
            
            $wechat_client_id     = get_option( 'toms_social_login_wechat_client_id' );
            $qq_client_id         = get_option( 'toms_social_login_qq_client_id' );
            $weibo_client_id      = get_option( 'toms_social_login_weibo_client_id' );
            $dingtalk_client_id   = get_option( 'toms_social_login_dingtalk_client_id' );
            $github_client_id     = get_option( 'toms_social_login_github_client_id' );
            $google_client_id     = get_option( 'toms_social_login_google_client_id' );
            $facebook_client_id   = get_option( 'toms_social_login_facebook_client_id' );
            $paypal_client_id     = get_option( 'toms_social_login_paypal_client_id' );

            $wechat_secret     = get_option( 'toms_social_login_wechat_secret_key' );
            $qq_secret         = get_option( 'toms_social_login_qq_secret_key' );
            $weibo_secret      = get_option( 'toms_social_login_weibo_secret_key' );
            $dingtalk_secret   = get_option( 'toms_social_login_dingtalk_secret_key' );
            $github_secret     = get_option( 'toms_social_login_github_secret_key' );
            $google_secret     = get_option( 'toms_social_login_google_secret_key' );
            $facebook_secret   = get_option( 'toms_social_login_facebook_secret_key' );
            $paypal_secret     = get_option( 'toms_social_login_paypal_secret_key' );

            $wechat_redirect_uri     = !empty( get_option( 'toms_social_login_wechat_callback_url' ) ) ? get_option( 'toms_social_login_wechat_callback_url' ) : home_url();
            $qq_redirect_uri         = !empty( get_option( 'toms_social_login_qq_callback_url' ) ) ? get_option( 'toms_social_login_qq_callback_url' ) : home_url('index.php');
            $weibo_redirect_uri      = !empty( get_option( 'toms_social_login_weibo_callback_url' ) ) ? get_option( 'toms_social_login_weibo_callback_url' ) : home_url();
            $dingtalk_redirect_uri   = !empty( get_option( 'toms_social_login_dingtalk_callback_url' ) ) ? get_option( 'toms_social_login_dingtalk_callback_url' ) : home_url( '/' );
            $github_redirect_uri     = !empty( get_option( 'toms_social_login_github_callback_url' ) ) ? get_option( 'toms_social_login_github_callback_url' ) : home_url();
            $google_redirect_uri     = !empty( get_option( 'toms_social_login_google_callback_url' ) ) ? get_option( 'toms_social_login_google_callback_url' ) : home_url();
            $facebook_redirect_uri   = !empty( get_option( 'toms_social_login_facebook_callback_url' ) ) ? get_option( 'toms_social_login_facebook_callback_url' ) : home_url( '/' );
            $paypal_redirect_uri     = !empty( get_option( 'toms_social_login_paypal_callback_url' ) ) ? get_option( 'toms_social_login_paypal_callback_url' ) : home_url();

            $wechat_request_uri     = 'https://open.weixin.qq.com/connect/qrconnect';
            $qq_request_uri         = 'https://graph.qq.com/oauth2.0/authorize';
            $weibo_request_uri      = 'https://api.weibo.com/oauth2/authorize';
            $dingtalk_request_uri   = 'https://oapi.dingtalk.com/connect/qrconnect';
            $github_request_uri     = 'https://github.com/login/oauth/authorize';
            $google_request_uri     = 'https://accounts.google.com/o/oauth2/auth';
            $facebook_request_uri   = 'https://www.facebook.com/v14.0/dialog/oauth';
            $paypal_request_uri     = 'https://www.paypal.com/connect';

            $google_scope           = 'https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile';
            $facebook_scope         = 'email';
            $paypal_scope           = 'openid profile email https://uri.paypal.com/services/paypalattributes';

            $wechat_request_url     = $wechat_request_uri . '?appid=' . $wechat_client_id . '&redirect_uri=' . $wechat_redirect_uri . '&response_type=code&scope=snsapi_login&state=wechat' . $state;
            $qq_request_url         = $qq_request_uri . '?response_type=code&client_id=' . $qq_client_id . '&redirect_uri=' . $qq_redirect_uri . '&scope=get_user_info&state=qq' . $state;
            $weibo_request_url      = $weibo_request_uri . '?response_type=code&client_id=' . $weibo_client_id . '&redirect_uri=' . $weibo_redirect_uri . '&state=weibo' . $state ;
            $dingtalk_request_url   = $dingtalk_request_uri . '?appid=' . $dingtalk_client_id . '&redirect_uri=' . $dingtalk_redirect_uri . '&response_type=code&scope=snsapi_login&state=dingtalk' . $state;
            $github_request_url     = $github_request_uri . '?client_id=' . $github_client_id . '&redirect_uri=' . $github_redirect_uri . '&state=github' . $state;
            $google_request_url     = $google_request_uri . '?client_id=' . $google_client_id . '&response_type=code&redirect_uri=' . $google_redirect_uri . '&scope=' . $google_scope . '&state=google' . $state;
            $facebook_request_url   = $facebook_request_uri . '?client_id=' . $facebook_client_id . '&response_type=code&redirect_uri=' . $facebook_redirect_uri . '&scope=' . $facebook_scope . '&state=facebook' . $state;
            $paypal_request_url     = $paypal_request_uri. '?flowEntry=static&client_id=' . $paypal_client_id . '&response_type=code&scope=' . $paypal_scope . '&redirect_uri=' . $paypal_redirect_uri . '&state=paypal' . $state;

            $json = '';
            ob_start(); ?>
                [
                    {
                        "enabled"   : "<?php echo esc_textarea( $wechat_enabled ); ?>",
                        "name"      : "<?php echo __( 'Wechat', 'toms-social-login' ); ?>",
                        "type"      : "wechat",
                        "icon"      : "wechat",
                        "color"     : "#ffffff",
                        "BgColor"   : "#3cb035",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "0",
                        "client_id" : "<?php echo esc_textarea( $wechat_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $wechat_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $wechat_request_url ); ?>",
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'App ID', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'App Secret', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Authorization Callback Domain', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url() ); ?>",
                        "params":{
                            "url"           : "",
                            "response_type" : "code",
                            "redirect_uri"  : "",
                            "scope"         : "snsapi_login",
                            "state"         : "&state=wechat"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://api.weixin.qq.com/sns/oauth2/access_token?",
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "https://api.weixin.qq.com/sns/userinfo?",
                            "access_token"  : "?access_token=",
                            "openid"        : "&openid="
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://open.weixin.qq.com/cgi-bin/appcreate",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    },
                    {
                        "enabled"   : "<?php echo esc_textarea( $qq_enabled ); ?>",
                        "name"      : "<?php echo __( 'QQ', 'toms-social-login' ); ?>",
                        "type"      : "qq",
                        "icon"      : "qq",
                        "color"     : "#ffffff",
                        "BgColor"   : "#4b99f2",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "1",
                        "client_id" : "<?php echo esc_textarea( $qq_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $qq_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $qq_request_url ); ?>", 
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'App ID', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'APP Key', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Authorization Callback Domain', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url( 'index.php' ) ); ?>",
                        "params" : {
                            "url"           : "",
                            "response_type" : "code",
                            "redirect_uri"  : "<?php echo esc_textarea( $qq_redirect_uri ); ?>",
                            "scope"         : "get_user_info",
                            "state"         : "qq"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://graph.qq.com/oauth2.0/token?",
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "https://graph.qq.com/oauth2.0/me?",
                            "access_token"  : "",
                            "openid"        : "",
                            "data_url"      : "https://graph.qq.com/user/get_user_info?"
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://connect.qq.com/",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    },
                    {
                        "enabled"   : "<?php echo esc_textarea( $weibo_enabled ); ?>",
                        "name"      : "<?php echo __( 'Weibo', 'toms-social-login' ); ?>",
                        "type"      : "weibo",
                        "icon"      : "weibo",
                        "color"     : "#ffffff",
                        "BgColor"   : "#fb6622",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "2",
                        "client_id" : "<?php echo esc_textarea( $weibo_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $weibo_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $weibo_request_url ); ?>",
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'App Key', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'App Secret', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Authorization callback URL', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url() ); ?>",
                        "params" : {
                            "url"           : "",
                            "response_type" : "code",
                            "redirect_uri"  : "<?php echo esc_url( $weibo_redirect_uri); ?>",
                            "scope"         : "",
                            "state"         : "weibo"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://api.weibo.com/oauth2/access_token?",
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "https://api.weibo.com/2/users/show.json?",
                            "access_token"  : "",
                            "openid"        : ""
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://open.weibo.com/apps",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    },
                    {
                        "enabled"   : "<?php echo esc_textarea( $dingtalk_enabled ); ?>",
                        "name"      : "<?php echo __( 'DingTalk', 'toms-social-login' ); ?>",
                        "type"      : "dingtalk",
                        "icon"      : "dingtalk",
                        "color"     : "#ffffff",
                        "BgColor"   : "#3397f9",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "3",
                        "client_id" : "<?php echo esc_textarea( $dingtalk_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $dingtalk_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $dingtalk_request_url ); ?>",
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'App Id', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'App Secret', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Authorization Callback Domain', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url( '/' ) ); ?>",
                        "params" : {
                            "url"           : "https://oapi.dingtalk.com/connect/qrconnect?",
                            "response_type" : "code",
                            "redirect_uri"  : "<?php echo esc_url( $dingtalk_redirect_uri ); ?>",
                            "scope"         : "snsapi_login",
                            "state"         : "dingtalk"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://oapi.dingtalk.com/sns/gettoken?",
                            "ac_params"     : {
                                "appid"     : "appid",
                                "appsecret" : "appsecret"
                            },
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "",
                            "persistent_code_url"   : "https://oapi.dingtalk.com/sns/get_persistent_code?",
                            "persistent_code_json_params": {
                                "tmp_auth_code" : "tmp_auth_code"
                            },
                            "sns_token_url"         : "https://oapi.dingtalk.com/sns/get_sns_token?",
                            "sns_token_json_params" : {
                                "openid" : "openid",
                                "persistent_code" : "persistent_code"
                            },
                            "get_userinfo_url"      : "https://oapi.dingtalk.com/sns/getuserinfo?",
                            "get_userinfo_params" : {
                                "sns_token" : "sns_token"
                            },
                            "access_token"  : "",
                            "openid"        : ""
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://open-dev.dingtalk.com/",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    },
                    {
                        "enabled"   : "<?php echo esc_textarea( $github_enabled ); ?>",
                        "name"      : "<?php echo __( 'GitHub', 'toms-social-login' ); ?>",
                        "type"      : "github",
                        "icon"      : "github",
                        "color"     : "#ffffff",
                        "BgColor"   : "#2b414d",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "4",
                        "client_id" : "<?php echo esc_textarea( $github_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $github_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $github_request_url ); ?>",
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'Client ID', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'Client Secrets', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Authorization Callback URL', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url() ); ?>",
                        "params" : {
                            "url"           : "",
                            "response_type" : "code",
                            "redirect_uri"  : "",
                            "scope"         : "snsapi_login",
                            "state"         : "github"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://github.com/login/oauth/access_token?",
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "https://api.github.com/user?",
                            "user_info_url" : "",
                            "access_token"  : "",
                            "openid"        : ""
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://github.com/settings/developers",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    },
                    {
                        "enabled"   : "<?php echo esc_textarea( $google_enabled ); ?>",
                        "name"      : "<?php echo __( 'Google', 'toms-social-login' ); ?>",
                        "type"      : "google",
                        "icon"      : "google",
                        "color"     : "#ffffff",
                        "BgColor"   : "#ea4335",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "5",
                        "client_id" : "<?php echo esc_textarea( $google_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $google_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $google_request_url ); ?>",
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'Client ID', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'Client secret', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Authorized Redirect URI', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url() ); ?>",
                        "params" : {
                            "url"           : "",
                            "response_type" : "code",
                            "redirect_uri"  : "<?php echo esc_url( $google_redirect_uri ); ?>",
                            "scope"         : "https://www.googleapis.com/auth/userinfo.email https://www.googleapis.com/auth/userinfo.profile",
                            "state"         : "google"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://accounts.google.com/o/oauth2/token?",
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "https://www.googleapis.com/oauth2/v3/userinfo?",
                            "access_token"  : "",
                            "openid"        : ""
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://console.cloud.google.com/apis/dashboard",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    },
                    {
                        "enabled"   : "<?php echo esc_textarea( $facebook_enabled ); ?>",
                        "name"      : "<?php echo __( 'Facebook', 'toms-social-login' ); ?>",
                        "type"      : "facebook",
                        "icon"      : "facebook",
                        "color"     : "#ffffff",
                        "BgColor"   : "#39569d",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "6",
                        "client_id" : "<?php echo esc_textarea( $facebook_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $facebook_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $facebook_request_url ); ?>",
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'App ID', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'App Secret', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Valid OAuth Redirect URI', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url( '/' ) ); ?>",
                        "params" : {
                            "url"           : "https://www.facebook.com/v14.0/dialog/oauth?",
                            "response_type" : "code",
                            "redirect_uri"  : "<?php echo esc_url( $facebook_redirect_uri ); ?>",
                            "scope"         : "snsapi_login",
                            "state"         : "facebook"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://graph.facebook.com/v14.0/oauth/access_token?",
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "https://graph.facebook.com/debug_token?",
                            "input_token"   : "input_token",
                            "access_token"  : "",
                            "user_id"       : "",
                            "user_info_url" : "https://graph.facebook.com/",
                            "openid"        : ""
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://developers.facebook.com/apps",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    },
                    {
                        "enabled"   : "<?php echo esc_textarea( $paypal_enabled ); ?>",
                        "name"      : "<?php echo __( 'PayPal', 'toms-social-login' ); ?>",
                        "type"      : "paypal",
                        "icon"      : "paypal",
                        "color"     : "#ffffff",
                        "BgColor"   : "#0070BA",
                        "style"     : "<?php echo esc_textarea( $style ); ?>",
                        "Sort"      : "7",
                        "client_id" : "<?php echo esc_textarea( $paypal_client_id ); ?>",
                        "secret"    : "<?php echo esc_textarea( $paypal_secret ); ?>",
                        "request_URL"   : "<?php echo esc_url( $paypal_request_url ); ?>",
                        "nonce_name"    : "toms_social_login_nonce",
                        "client_id_text"    : "<?php echo __( 'Client ID', 'toms-social-login' ); ?>",
                        "secret_text"       : "<?php echo __( 'Secret', 'toms-social-login' ); ?>",
                        "callback_url_text" : "<?php echo __( 'Return URL', 'toms-social-login' ); ?>",
                        "callback_url_text_title" : "<?php echo esc_url( home_url() ); ?>",
                        "params" : {
                            "url"           : "https://www.paypal.com/connect?",
                            "response_type" : "code",
                            "redirect_uri"  : "",
                            "scope"         : "snsapi_login",
                            "state"         : "paypal"
                        },
                        "access_token_URL"  : {
                            "url"           : "https://api-m.paypal.com/v1/oauth2/token?",
                            "code"          : "",
                            "grant_type"    : "authorization_code"
                        },
                        "fetch_user_info"   : {
                            "url"           : "https://api-m.paypal.com/v1/identity/oauth2/userinfo?",
                            "access_token"  : "",
                            "openid"        : ""
                        },
                        "login_css"  : {
                            "icon" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "32px",
                                "font_weight"       : "",
                                "line_height"       : "",
                                "border_radius"     : "2px 2px 2px 2px",
                                "vertical_align"    : "middle",
                                "word_break"        : "break-word",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            },
                            "text" : {
                                "order"             : "",
                                "opacity"           : "0.8",
                                "margin"            : "2px 2px 2px 2px",
                                "padding"           : "0 0 0 0",
                                "font_size"         : "18px",
                                "font_weight"       : "bold",
                                "line_height"       : "",
                                "border_radius"     : "",
                                "vertical_align"    : "",
                                "word_break"        : "",
                                "align_self"        : "",
                                "cursor"            : "",
                                "color"             : "",
                                "background_color"      : "",
                                "background"            : "",
                                "background_size"       : "",
                                "background_position"   : "",
                                "background_repeat"     : ""
                            }
                        },
                        "create_app_url"    : "https://developer.paypal.com/developer/applications",
                        "help"              : "https://toms-caprice.org/toms-social-login"
                    }
                    <?php 
                        //TomS Extra Social Json
                        do_action( 'TomS_Extra_Social_Json', $state, $style );
                    ?>
                ]
            <?php $json .= ob_get_clean();

            return $json;
        }
    }
}
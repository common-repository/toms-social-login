<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TomSUserRequest') ){
    class TomSUserRequest{
        public function __construct() {

        }

        /**
         * Process TomS Social Login User Request.
         *
         * @since  1.0.0
         *
         * @param   $unionid        The social Unique identification string.
         * @param   $social_type    The social type.
         * @param   $avatar         The social profile image url.
         * @param   $email          The social email address.
         * @param   $openid         The social open id.
         * @param   $nickname       The social nickname.
         * 
         */
        function TomS_User_Request($unionid, $type, $avatar, $email, $openid, $nickname, $request_url){
            $unionid    = !empty($unionid)  ? $unionid : '';
            $type       = !empty($type)  ? $type : '';
            $avatar     = !empty($avatar)  ? $avatar : '';
            $email      = !empty($email)  ? $email : '';
            $openid     = !empty($openid)  ? $openid : '';
            $nickname   = !empty($nickname)  ? $nickname : '';
            $request_url= !empty($request_url) ? $request_url : '';


            //如果用户未登录，执行创建新用户及相关操作
            if( !is_user_logged_in() ){
                //查询数据库unionid是否存在
                $TSSL_Data_Query    = new TSSL_Data_Query();
                $tomsUserData       = $TSSL_Data_Query->TSSL_Query('unionid', $unionid, '%s', 'socialtype',  $type, '%s');
                //获取数据库内的unionid, 如果不存在，否则为空值。
                $db_unionided       = isset( $tomsUserData[0]->unionid ) && !empty( $tomsUserData[0]->unionid ) ? $tomsUserData[0]->unionid : '';
                $db_user_id         = isset( $tomsUserData[0]->userid ) && !empty($tomsUserData[0]->userid) ? $tomsUserData[0]->userid : '';
                $id                 = isset( $tomsUserData[0]->id ) && !empty( $tomsUserData[0]->id ) ? $tomsUserData[0]->id : '';

                if( !empty($unionid) && empty($db_unionided) ){

                    //创建新用户
                    $TSSL_User_Process  = new TSSL_User_Process();
                    $new_user_id        = $TSSL_User_Process->TSSL_Create_User($unionid, $type, $avatar, $email, $openid, $nickname);

                    //用户注册后，自动登录
                    if( $new_user_id ){
                        $login = $TSSL_User_Process->TSSL_Process_User_login( $new_user_id, $type, $request_url );
                    }
                }
                
                if( !empty($unionid) && $unionid === $db_unionided && !empty( $db_user_id ) ){

                    //用户已存在，则直接登录, 并更新用户信息为最新
                    if( get_user_by( 'id', $db_user_id) ){
                        
                        $TSSL_User_Process  = new TSSL_User_Process();
                        //update user info if change
                        $update_id  = $TSSL_User_Process->TSSL_Update_User( $db_user_id, $type, $avatar, $email, $openid, $nickname);
                        
                        //Login if user exists.
                        $login      = $TSSL_User_Process->TSSL_Process_User_login($db_user_id, $type, $request_url);

                    }else{
                        //进到这里，说明用户信息已存在，但是关联的wordpress用户不存在，可能被管理手动删除，这个时候我们就要创建新用户并关联新用户id到已有的
                        //创建新用户
                        $TSSL_User_Process  = new TSSL_User_Process();
                        $new_user_id        = $TSSL_User_Process->TSSL_Create_User($unionid, $type, $avatar, $email, $openid, $nickname);
                        
                        //用户创建成功后，自动登录
                        $login = $TSSL_User_Process->TSSL_Process_User_login($new_user_id, $type, $request_url);
                    }
                }
            }

            //如果用户已经登录 执行"绑定"操作
            if( is_user_logged_in() ){
                //先获取已登录用户的id
                $current_user_id = get_current_user_id();

                //查询数据库
                $TSSL_Data_Query    = new TSSL_Data_Query();
                
                //获取已登录的用户的微信unionid,如果为空则用户未绑定
                $tomsUserData       = $TSSL_Data_Query->TSSL_Query('userid', $current_user_id, '%d', 'socialtype',  $type, '%s');
                $db_unionided       = isset( $tomsUserData[0]->unionid ) && !empty($tomsUserData[0]->unionid) ? $tomsUserData[0]->unionid : '';
                $db_Userid          = isset( $tomsUserData[0]->userid ) && !empty($tomsUserData[0]->userid) ? $tomsUserData[0]->userid : '';
                
                //查询数据库内是否已经有当前扫码的微信unionid,(如果有说明：已经绑定过其他账号)
                $tomsUserUnionid    = $TSSL_Data_Query->TSSL_Query('unionid', $unionid, '%s', 'socialtype',  $type, '%s');
                $tomsUnionid        = isset( $tomsUserUnionid[0]->unionid ) && !empty($tomsUserUnionid[0]->unionid) ? $tomsUserUnionid[0]->unionid : '';
                $tomsUserid         = isset( $tomsUserUnionid[0]->userid ) && !empty($tomsUserUnionid[0]->userid) ? $tomsUserUnionid[0]->userid : '';

                //绑定用户
                if( empty($tomsUnionid) && empty( $tomsUserid ) ){
                    $TSSL_User_Process  = new TSSL_User_Process();
                    $toms_bind_user_id = $TSSL_User_Process->TSSL_Binding_User($current_user_id, $unionid, $type, $avatar, $email, $openid, $nickname);

                    //绑定后跳转回原来的页面
                    if( wp_validate_boolean( get_userdata( $toms_bind_user_id) ) ){
                        
                        $request_url    = preg_replace('/\?.*/i','', $request_url);//删除链接？及后面的所有字符
                        
                        if( empty( $request_url ) ){
                            $request_url = home_url();
                        }
                        wp_safe_redirect( $request_url );
                        exit;
                    }
                    
                }
                //如果社交账号已存在，但是绑定的用户已被手动删除，且当前用户已经登录，将已存在的社交账号绑定到该登录用户上，并更新userid为当前登录用户，以及更新其他相关信息
                if( !empty($tomsUnionid) && !wp_validate_boolean( get_userdata( $tomsUserid ) ) ){
                    $TSSL_User_Process  = new TSSL_User_Process();
                    $toms_update_and_bind_user_id  = $TSSL_User_Process->TSSL_Update_Exists_Social_Account_And_Binding_User($current_user_id, $unionid, $type, $avatar, $email, $openid, $nickname);

                    //绑定后跳转回原来的页面
                    if( wp_validate_boolean( get_userdata( $toms_update_and_bind_user_id ) ) ){
                        
                        $request_url    = preg_replace('/\?.*/i','', $request_url);//删除链接？及后面的所有字符
                        
                        if( empty( $request_url ) ){
                            $request_url = home_url();
                        }
                        wp_safe_redirect( $request_url );
                        exit;
                    }
                }

                if( strlen( $type ) > 2 ){
                    $social_type = ucwords( $type );
                }else{
                    $social_type = strtoupper( $type );
                }

                //This social account is already binding this user account.
                if( !empty($db_unionided) && $db_unionided === $unionid && wp_validate_boolean( get_userdata( $db_Userid ) ) ){
                    
                    wp_die( __( 'Your ', 'toms-social-login' ) . '<strong style="color: red">' . esc_html( $social_type ) . '</strong>' . __(' account is already binding this account !', 'toms-social-login') . '<br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                    
                }

                //This user account is already binding another social account, unbind it first.
                if( !empty($db_unionided) && $db_unionided != $unionid && wp_validate_boolean( get_userdata( $tomsUserid ) ) ){
                    
                    wp_die( __('Another ', 'toms-social-login') . '<strong style="color: red">' . esc_html( $social_type ) . '</strong>' . __(' account has been binding this account, Unbind it first !') . ' <br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                    
                }

                //Social account is already binding another user account.
                if( empty($db_unionided) && $tomsUnionid === $unionid && wp_validate_boolean( get_userdata( $tomsUserid ) )){
                    
                    wp_die( __( 'Your ', 'toms-social-login' ) . '<strong style="color: red">' . esc_html( $social_type ) . '</strong>' . __(' account is already binding another user account, please log out and use your ', 'toms-social-login') . esc_textarea( $social_type ) . __(' account to login again', 'toms-social-login') . '<br/><br/><a href="' . esc_url( $request_url ) . '">« ' . __('Back', 'toms-social-login') . '</a>');
                }
            }
        }
    }
}
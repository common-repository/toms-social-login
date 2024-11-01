<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_User_Process') ){
    class TSSL_User_Process{
        public function __construct() {

        }

        /**
         * Auto Create TomS Social Login User.
         *
         * @since  1.0.0
         *
         * @param   $unionid        The social Unique identification string.
         * @param   $social_type    The social type.
         * @param   $avatar         The social profile image url.
         * @param   $email          The social email address.
         * @param   $openid         The social open id.
         * @param   $nickname       The social nickname.
         * @return $userid          The Register user id.
         */
        function TSSL_Create_User( $unionid, $social_type, $avatar, $email, $openid, $nickname){
            $unionid        = !empty($unionid) ? $unionid : '';
            $social_type    = !empty($social_type) ? $social_type : '';
            $avatar         = !empty($avatar) ? $avatar : '';
            $email          = !empty($email) && !email_exists($email) ? $email : '';
            $openid         = !empty($openid) ? $openid : '';
            $nickname       = !empty($nickname) ? $nickname : '';

            //Social type check
            $TSSL_Social_Type   = new TSSL_Social_Type();
            $type_check         = $TSSL_Social_Type->TSSL_Type_Check($social_type);

            //Save Social avatar
            $TSSL_Avatar    = new TSSL_Avatar();
            $avatar         = $TSSL_Avatar->TSSL_User_Avatar( $social_type, $avatar, $unionid );
            $avatar         = !empty($avatar) ? $avatar : '';

            //unionid username if exists add a number to the end
            $e_unionid = str_replace(' ', '', $unionid);
            $e_unionid = preg_replace('/[^A-Za-z0-9]/', '', $e_unionid);
            $e_unionid = strtolower($e_unionid); 
            $e_unionid = strrev($e_unionid);
            $e_unionid = !username_exists( $social_type . '_' . $e_unionid ) && !empty( $e_unionid ) ? $social_type . '_' . $e_unionid : $social_type . '_' . $e_unionid . wp_rand( 1, 999 );

            //Process the Special characters from the nickname for username
            $username = $nickname;
            $username = str_replace(' ', '', $username);
            $username = preg_replace('/[^A-Za-z0-9]/', '', $username);
            $username = strtolower($username);
            $username = !username_exists( $social_type . '_' . $username ) && !empty( $username ) ? $social_type . '_' . $username : $e_unionid;

            //Retrieve the data if unionid exists
            $TSSL_Data_Query    = new TSSL_Data_Query();
            $tomsUserData       = $TSSL_Data_Query->TSSL_Query('unionid', $unionid, '%s', 'socialtype',  $social_type, '%s');

            //Get the unionid for $db_unionided from database if exists.Update social user info if change
            $db_unionided   = isset( $tomsUserData[0]->unionid ) && !empty( $tomsUserData[0]->unionid ) ? $tomsUserData[0]->unionid : '';
            $db_social_type = isset( $tomsUserData[0]->socialtype ) && !empty( $tomsUserData[0]->socialtype ) ? $tomsUserData[0]->socialtype : '';
            $userid         = isset( $tomsUserData[0]->userid ) && !empty( $tomsUserData[0]->userid ) ? $tomsUserData[0]->userid : '';
            $id             = isset( $tomsUserData[0]->id ) && !empty($tomsUserData[0]->id) ? $tomsUserData[0]->id : '';
            $logintimes     = isset( $tomsUserData[0]->logintimes ) && !empty($tomsUserData[0]->logintimes) ? $tomsUserData[0]->logintimes + 1 : 0;
            $role           = get_option('default_role', 'subscriber');
            $role           = apply_filters( 'TomSRole', $role );

            //Create New User for New Social account
            if(!empty($unionid) && !empty($social_type) && $type_check === 'yes' && empty($db_unionided) && empty($db_social_type) && !get_userdata( $userid ) ){
                //Create an generate password;
                $password = wp_generate_password();

                $new_wp_user_id = wp_insert_user(
                                        array(
                                            'user_login'        => sanitize_user( $username ),
                                            'user_email'        => sanitize_email( $email ),
                                            'user_pass'         => sanitize_text_field( $password ),
                                            'nickname'          => sanitize_text_field( $nickname ),
                                            'display_name'      => sanitize_text_field( $nickname ),
                                            'user_registered'   => date('Y-m-d H:i:s'),
                                            'role'              => sanitize_text_field( $role )
                                        )
                                    );

                //Save social user data
                global $wpdb;
                $tomsTable  = $wpdb->prefix . 'toms_social_login';
                $toms_insert_user = $wpdb->insert(
                                            $tomsTable,
                                            array(
                                                'socialtype'    => sanitize_text_field( $social_type ),
                                                'userid'        => sanitize_text_field( $new_wp_user_id ),
                                                'unionid'       => sanitize_text_field( $unionid ),
                                                'useremail'     => sanitize_email( $email ),
                                                'nickname'      => sanitize_text_field( $nickname ),
                                                'avatar'        => sanitize_text_field( $avatar ),
                                                'openid'        => sanitize_text_field( $openid ),
                                                'lastlogin'     => date('Y-m-d H:i:s'),
                                                'logintimes'    => 1
                                            ),
                                            array( '%s','%d', '%s', '%s', '%s', '%s','%s','%s','%d' )
                                        );

                if( wp_validate_boolean($new_wp_user_id) && wp_validate_boolean($toms_insert_user) ){
                    return $new_wp_user_id;
                }elseif( wp_validate_boolean($new_wp_user_id) ){
                    wp_die( __('User Create Failed !', 'toms-social-login') );
                }else{
                    wp_die( __('Social User Create Failed !', 'toms-social-login') );
                }
            }

            //Create New User and relate for Exists Social account if wordpress default account deleted by admin
            if(!empty($unionid) && !empty($social_type) && $type_check === 'yes' && $db_unionided == $unionid && $db_social_type == $social_type && !get_userdata( $userid ) ){
                //Create an generate password;
                $password = wp_generate_password();

                $new_wp_user_id = wp_insert_user(
                                        array(
                                            'user_login'        => sanitize_user( $username ),
                                            'user_email'        => sanitize_email( $email ),
                                            'user_pass'         => sanitize_text_field( $password ),
                                            'nickname'          => sanitize_text_field( $nickname ),
                                            'display_name'      => sanitize_text_field( $nickname ),
                                            'user_registered'   => date('Y-m-d H:i:s'),
                                            'role'              => sanitize_text_field( $role )
                                        )
                                    );
                //Get social email from database
                $db_useremail   = isset( $tomsUserData[0]->useremail ) && !empty($tomsUserData[0]->useremail) ? $tomsUserData[0]->useremail : '';
                $useremail      = !empty($email) && $db_useremail != $email ? $email : '';
                

                if( wp_validate_boolean($new_wp_user_id) ){
                    global $wpdb;
                    $tomsTable  = $wpdb->prefix . 'toms_social_login';
                    $update_info = $wpdb->update( 
                                        $tomsTable,
                                        array(
                                            'userid'        => sanitize_text_field( $new_wp_user_id ),
                                            'nickname'      => sanitize_text_field( $nickname ),
                                            'lastlogin'     => date('Y-m-d H:i:s'),
                                            'avatar'        => sanitize_text_field( $avatar ),
                                            'useremail'     => sanitize_email( $useremail ),
                                            'openid'        => sanitize_text_field( $openid ),
                                            'logintimes'    => sanitize_text_field( $logintimes )
                                        ),
                                        array( 'ID' => $id ),
                                        array( '%s','%s', '%s', '%s', '%s', '%d' ), //对应上面第一个数组的4个字段的类型
                                        array( '%d' ) //对应第二个数组的字段的类型
                                    );
                    if( wp_validate_boolean($update_info) ){
                        return $new_wp_user_id;
                    }else{
                        wp_die( __('Social User Update Failed !', 'toms-social-login') );
                    }
                }else{
                    wp_die( __('User Create Failed !', 'toms-social-login') );
                }
            }
        }

        /**
         * Auto Update TomS Social Login User.
         *
         * @since  1.0.0
         *
         * @param   $userid         The social user id.
         * @param   $avatar         The social profile image url.
         * @param   $email          The social email address.
         * @param   $openid         The social open id.
         * @param   $nickname       The social nickname.
         * @return $id              Return the id of TomS Social login table.
         */
        function TSSL_Update_User( $userid, $social_type, $avatar, $email, $openid, $nickname){
            $userid         = !empty($userid) ? $userid : '';
            $social_type    = !empty($social_type) ? $social_type : '';
            $avatar         = !empty($avatar) ? $avatar : '';
            $email          = !empty($email) ? $email : '';
            $openid         = !empty($openid) ? $openid : '';
            $nickname       = !empty($nickname) ? $nickname : '';

            //Retrieve the data if unionid exists
            $TSSL_Data_Query    = new TSSL_Data_Query();
            $tomsUserData       = $TSSL_Data_Query->TSSL_Query('userid', $userid, '%d', 'socialtype', $social_type, '%s');

            //Update social user info if change
            $id             = isset( $tomsUserData[0]->id ) && !empty($tomsUserData[0]->id) ? $tomsUserData[0]->id : '';
            $logintimes     = isset( $tomsUserData[0]->logintimes ) && !empty($tomsUserData[0]->logintimes) ? $tomsUserData[0]->logintimes + 1 : 0;
            $avatar         = isset( $tomsUserData[0]->avatar ) && !empty($avatar) && $tomsUserData[0]->avatar != $avatar ? $avatar : $tomsUserData[0]->avatar;
            $useremail      = isset( $tomsUserData[0]->useremail ) && !empty($email) && $tomsUserData[0]->useremail != $email ? $email : $tomsUserData[0]->useremail;
            $nickname       = isset( $tomsUserData[0]->nickname ) && !empty($nickname) && $tomsUserData[0]->nickname != $nickname ? $nickname : $tomsUserData[0]->nickname;
            $openid         = isset( $tomsUserData[0]->openid ) && !empty($openid) && $tomsUserData[0]->openid != $openid ? $openid : $tomsUserData[0]->openid;
            $userid         = isset( $tomsUserData[0]->userid ) && !empty( $tomsUserData[0]->userid ) ? $tomsUserData[0]->userid : '';
            $db_unionided   = isset( $tomsUserData[0]->unionid ) && !empty( $tomsUserData[0]->unionid ) ? $tomsUserData[0]->unionid : '';

            //Save Social avatar
            $TSSL_Avatar    = new TSSL_Avatar();
            $avatar         = $TSSL_Avatar->TSSL_User_Avatar( $social_type, $avatar, $db_unionided );
            $avatar         = !empty($avatar) ? $avatar : '';

            global $wpdb;
            $tomsTable  = $wpdb->prefix . 'toms_social_login';
            $update_info = $wpdb->update( 
                                    $tomsTable,
                                    array(
                                        'nickname'      => sanitize_text_field( $nickname ),
                                        'lastlogin'     => date('Y-m-d H:i:s'),
                                        'avatar'        => sanitize_text_field( $avatar ),
                                        'useremail'     => sanitize_email( $useremail ),
                                        'openid'        => sanitize_text_field( $openid ),
                                        'logintimes'    => sanitize_text_field( $logintimes )
                                    ),
                                    array( 'ID' => $id ),
                                    array( '%s','%s', '%s', '%s', '%s', '%d' ), //对应上面第一个数组的4个字段的类型
                                    array( '%d' ) //对应第二个数组的字段的类型
                                );
            if( wp_validate_boolean($update_info) ){
                return $id;
            }else{
                wp_die( __('User Update Failed !', 'toms-social-login') );
            }
        }

        /**
         * Auto Bindding TomS Social Login User.
         *
         * @since   1.0.0
         * 
         * @param   $userid         The logined user id
         * @param   $unionid        The social Unique identification string.
         * @param   $social_type    The social type.
         * @param   $avatar         The social profile image url.
         * @param   $email          The social email address.
         * @param   $openid         The social open id.
         * @param   $nickname       The social nickname.
         * @return $userid          The Register user id.
         */
        function TSSL_Binding_User($userid, $unionid, $social_type, $avatar, $email, $openid, $nickname){
            $userid         = !empty($userid) ? $userid : '';
            $unionid        = !empty($unionid) ? $unionid : '';
            $social_type    = !empty($social_type) ? $social_type : '';
            $avatar         = !empty($avatar) ? $avatar : '';
            $email          = !empty($email) ? $email : '';
            $openid         = !empty($openid) ? $openid : '';
            $nickname       = !empty($nickname) ? $nickname : '';

            //Social type check
            $TSSL_Social_Type   = new TSSL_Social_Type();
            $type_check         = $TSSL_Social_Type->TSSL_Type_Check($social_type);

            //Save Social avatar
            $TSSL_Avatar    = new TSSL_Avatar();
            $avatar         = $TSSL_Avatar->TSSL_User_Avatar( $social_type, $avatar, $unionid );
            $avatar         = !empty($avatar) ? $avatar : '';

            //Retrieve the data if unionid exists
            $TSSL_Data_Query    = new TSSL_Data_Query();
            $tomsUserData       = $TSSL_Data_Query->TSSL_Query('userid', $userid, '%d', 'socialtype', $social_type, '%s');

            $db_unionided   = isset( $tomsUserData[0]->unionid ) && !empty( $tomsUserData[0]->unionid ) ? $tomsUserData[0]->unionid : '';

            // If User already exists, linked social info for the current user
            if(!empty($unionid) && !empty($social_type) && $type_check === 'yes' && empty($db_unionided) && wp_validate_boolean( get_userdata( $userid ) ) ){
                global $wpdb;
                $tomsTable  = $wpdb->prefix . 'toms_social_login';
                $toms_bind_user = $wpdb->insert(
                                            $tomsTable,
                                            array(
                                                'socialtype'    => sanitize_text_field( $social_type ),
                                                'userid'        => sanitize_text_field( $userid ),
                                                'unionid'       => sanitize_text_field( $unionid ),
                                                'useremail'     => sanitize_email( $email ),
                                                'nickname'      => sanitize_text_field( $nickname ),
                                                'avatar'        => sanitize_text_field( $avatar ),
                                                'openid'        => sanitize_text_field( $openid ),
                                                'lastlogin'     => date('Y-m-d H:i:s'),
                                                'logintimes'    => 1
                                            ),
                                            array( '%s','%d', '%s', '%s', '%s', '%s','%s','%s','%d' )
                                        );
                if( wp_validate_boolean($toms_bind_user) ){
                    return $userid;
                }else{
                    wp_die( __('Social User Binding Failed !', 'toms-social-login') );
                }
            }
        }

        /**
         * Auto Update Exists Social Account And Bindding TomS Social Login User.
         *
         * @since   1.0.0
         * 
         * @param   $userid         The logined user id
         * @param   $unionid        The social Unique identification string.
         * @param   $social_type    The social type.
         * @param   $avatar         The social profile image url.
         * @param   $email          The social email address.
         * @param   $openid         The social open id.
         * @param   $nickname       The social nickname.
         * @return $userid          The Register user id.
         */
        function TSSL_Update_Exists_Social_Account_And_Binding_User($userid, $unionid, $social_type, $avatar, $email, $openid, $nickname){
            $userid         = !empty($userid) ? $userid : '';
            $unionid        = !empty($unionid) ? $unionid : '';
            $social_type    = !empty($social_type) ? $social_type : '';
            $avatar         = !empty($avatar) ? $avatar : '';
            $email          = !empty($email) ? $email : '';
            $openid         = !empty($openid) ? $openid : '';
            $nickname       = !empty($nickname) ? $nickname : '';

            //Social type check
            $TSSL_Social_Type   = new TSSL_Social_Type();
            $type_check         = $TSSL_Social_Type->TSSL_Type_Check($social_type);

            //Save Social avatar
            $TSSL_Avatar    = new TSSL_Avatar();
            $avatar         = $TSSL_Avatar->TSSL_User_Avatar( $social_type, $avatar, $unionid );
            $avatar         = !empty($avatar) ? $avatar : '';

            //Retrieve the data if unionid exists
            $TSSL_Data_Query    = new TSSL_Data_Query();
            $tomsUserData       = $TSSL_Data_Query->TSSL_Query('userid', $userid, '%d', 'socialtype', $social_type, '%s');
            $db_UserData        = $TSSL_Data_Query->TSSL_Query('unionid', $unionid, '%d', 'socialtype', $social_type, '%s');

            //Update social user info if change
            $toms_unionid   = isset( $tomsUserData[0]->unionid ) && !empty( $tomsUserData[0]->unionid ) ? $tomsUserData[0]->unionid : '';
            $toms_userid    = isset( $tomsUserData[0]->userid ) && !empty( $tomsUserData[0]->userid ) ? $tomsUserData[0]->userid : '';

            $id             = isset( $db_UserData[0]->id ) && !empty($db_UserData[0]->id) ? $db_UserData[0]->id : '';
            $db_userid      = isset( $db_UserData[0]->userid ) && !empty($db_UserData[0]->userid) ? $db_UserData[0]->userid : '';
            $db_unionid     = isset( $db_UserData[0]->unionid ) && !empty( $db_UserData[0]->unionid ) ? $db_UserData[0]->unionid : '';
            $logintimes     = isset( $db_UserData[0]->logintimes ) && !empty($db_UserData[0]->logintimes) ? $db_UserData[0]->logintimes + 1 : 0;
            $avatar         = isset( $db_UserData[0]->avatar ) && !empty($avatar) && $db_UserData[0]->avatar != $avatar ? $avatar : $db_UserData[0]->avatar;
            $useremail      = isset( $db_UserData[0]->useremail ) && !empty($email) && $db_UserData[0]->useremail != $email ? $email : $db_UserData[0]->useremail;
            $nickname       = isset( $db_UserData[0]->nickname ) && !empty($nickname) && $db_UserData[0]->nickname != $nickname ? $nickname : $db_UserData[0]->nickname;
            $openid         = isset( $db_UserData[0]->openid ) && !empty($openid) && $db_UserData[0]->openid != $openid ? $openid : $db_UserData[0]->openid;
            
            // If User already exists, linked social info for the current user
            if( !empty($unionid) && 
                !empty($social_type) && 
                $type_check === 'yes' &&
                empty($toms_unionid) &&
                empty( $toms_userid ) &&
                !get_userdata( $db_userid ) &&
                $db_unionid === $unionid
            ){
                global $wpdb;
                $tomsTable  = $wpdb->prefix . 'toms_social_login';
                $toms_update_bind_user = $wpdb->update(
                                            $tomsTable,
                                            array(
                                                'userid'        => sanitize_text_field( $userid ),
                                                'useremail'     => sanitize_email( $email ),
                                                'nickname'      => sanitize_text_field( $nickname ),
                                                'avatar'        => sanitize_text_field( $avatar ),
                                                'openid'        => sanitize_text_field( $openid ),
                                                'lastlogin'     => date('Y-m-d H:i:s'),
                                                'logintimes'    => sanitize_text_field( $logintimes )
                                            ),
                                            array( 'ID' => $id ),
                                            array( '%d', '%s', '%s', '%s', '%s', '%s','%d' ),
                                            array( '%d' )
                                        );
                if( wp_validate_boolean($toms_update_bind_user) ){
                    return $userid;
                }else{
                    wp_die( __('Social User Update and Binding Failed !', 'toms-social-login') );
                }
            }
        }

        /**
         * Process TomS Social Login User login.
         *
         * @since   1.0.0
         * 
         * @param   $userid         The logined user id
         * @param   $social_type    The Social Type
         * @param   $request_url    The Request url
         * 
         */
        function TSSL_Process_User_login( $userid, $social_type, $request_url ){
            $userid         = !empty( $userid ) ? $userid : '';
            $social_type    = !empty( $social_type ) ? $social_type : '';
            $request_url    = !empty( $request_url ) ? $request_url : '';
            //Retrieve the data if userid exists
            $TSSL_Data_Query    = new TSSL_Data_Query();
            $tomsUserData       = $TSSL_Data_Query->TSSL_Query('userid', $userid, '%d', 'socialtype', $social_type, '%s');

            $id         = isset( $tomsUserData[0]->id ) && !empty( $tomsUserData[0]->id ) ? $tomsUserData[0]->id : '';
            $unionid    = isset( $tomsUserData[0]->unionid ) && !empty( $tomsUserData[0]->unionid ) ? $tomsUserData[0]->unionid : '';

            //Allowed third party plugins add Additional Meta key and Meta value 
            if( !empty( $userid ) && !empty( $id ) && !empty( $unionid ) && get_userdata( $userid ) ){
                $toms_user_id       = $userid;
                $toms_user_meta     = array();
                $toms_user_meta     = apply_filters( 'TomSUserMeta', (array)$toms_user_meta, $toms_user_id );

                foreach( $toms_user_meta as $meta_key => $meta_value ){
                    if( !empty( $meta_key ) && !empty( $meta_value ) ){
                        update_user_meta( $toms_user_id, $meta_key, $meta_value );
                    }
                }
            }
            // Let the User login after social login processed
            if( !empty( $userid ) && !empty( $id ) && !empty( $unionid ) && get_userdata( $userid ) ){
                $secure_cookie = is_ssl();
                $secure_cookie = apply_filters('secure_signon_cookie', $secure_cookie, array());
                
                global $auth_secure_cookie;
                $auth_secure_cookie = $secure_cookie;
                
                wp_set_auth_cookie($userid, true, $secure_cookie);
                $user_obj = get_userdata($userid);
                do_action('wp_login', $user_obj->user_login, $user_obj );
                
                //Redirect to the previous page
                $default_login_url = wp_login_url();
                $previous_page_url = $request_url;
                $previous_page_url = preg_replace('/\?.*/i', '', $previous_page_url); //remove all the url actions

                if( $default_login_url === $previous_page_url ){
                    $previous_page_url = home_url();
                }
                //TomS Redirect Url Filter
                $previous_page_url = apply_filters( 'TomSRedirectURL', $previous_page_url );

                //echo $previous_page_url;
                wp_safe_redirect( $previous_page_url );
                exit;
            }else{
                wp_die( __('Login Failed! User id or social type not exists.', 'toms-social-login') );
            }
        }
    }
}
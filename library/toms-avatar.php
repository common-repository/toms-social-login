<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Avatar') ){
    class TSSL_Avatar{
        public function __construct() {
            add_filter( 'get_avatar' , array($this, 'TSSL_Get_Avatar'), 10, 6 );
            add_filter( 'default_avatar_select' , array($this, 'TSSL_Avatar_Select'), 10, 1 );
            add_filter( 'get_avatar_url', array($this, 'TSSL_Avatar_URL'), 10, 3 );
        }

        function TSSL_User_Avatar( $social_type, $avatar, $unionid ){

            if( !empty( $avatar ) && !empty( $social_type ) ){
                if ( ! function_exists( 'download_url' ) ) {
                    require_once ABSPATH . 'wp-admin/includes/file.php';
                }
                $image_url  = sanitize_url( $avatar );
                $upload_dir = wp_upload_dir();
                //$image_data = file_get_contents( $image_url );
                if( $social_type === "qq" || $social_type === "wechat" ){
                    $i_unionid = strtolower( $unionid );
                    $i_unionid = preg_replace('/[^A-Za-z0-9\-]/', '', $i_unionid);
                    $i_unionid = strrev($i_unionid);
                }else{
                    $i_unionid = '';
                }
                
                if( $social_type === "weibo" ){
                    $filename = strtolower( $unionid );
                    $filename = preg_replace('/[^A-Za-z0-9\-]/', '', $unionid);
                    $filename = strrev($unionid);
                }else{
                    $filename   = basename( $image_url );
                }
                $filename   = str_replace(' ', '', $filename);
                $filename   = preg_replace('/[^A-Za-z0-9]/', '', $filename);
                $filename   = strtolower($filename);
                $filename   = 'tssl_avatar_' . $social_type . '_' . $i_unionid . $filename . '.jpg';
                $file       = '';

                if ( wp_mkdir_p( $upload_dir['basedir'] ) ) {
                    $tssl_dir = $upload_dir['basedir'] . '/tssl-avatar';
                    if ( ! file_exists( $tssl_dir ) ) {
                        wp_mkdir_p( $tssl_dir );
                    }
                    $file = trailingslashit( $tssl_dir ) . $filename;
                
                    if( ! file_exists( $file ) ){
                        //save image file
                        //file_put_contents( $file, $image_data );

                        // resize image and save image
                        $image = wp_get_image_editor( $image_url );
                        if ( ! is_wp_error( $image ) ) {
                            $image->resize( 100, 100, true );
                            $image->save( $file );
                        }
                        // echo '<pre>';
                        // print_r( $image );
                        // echo '</pre>';

                        $wp_filetype = wp_check_filetype( $filename, null );

                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => sanitize_file_name( preg_replace( '/\.[^.]+$/', '', $filename) ),
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );

                        $attach_id = wp_insert_attachment( $attachment, $file );

                        if ( ! function_exists( 'wp_crop_image' ) ) {
                            include( ABSPATH . 'wp-admin/includes/image.php' );
                        }
                        $attach_data = wp_generate_attachment_metadata( $attach_id, $file );

                        wp_update_attachment_metadata( $attach_id, $attach_data );

                        return '/uploads/tssl-avatar/' . $filename;
                    }
                    return '/uploads/tssl-avatar/' . $filename;
                }
            }
        }

        function TSSL_Get_Avatar($avatar, $id_or_email, $size, $default, $alt, $args){
            //Check the $id_or_email is object or id or email, then trans to an user object.
            if( get_option( 'avatar_default', 'mystery' ) === 'tssl_avatar' ){
                if( is_numeric( $id_or_email ) ){
                    $user = get_user_by( 'id', (int) $id_or_email );
                }elseif( is_object( $id_or_email ) && property_exists( $id_or_email, 'ID' ) ){
                    $user = get_user_by( 'id', (int) $id_or_email->ID );
                }elseif( is_object( $id_or_email ) && property_exists( $id_or_email, 'user_id' ) ){
                    $user = get_user_by( 'id', (int) $id_or_email->user_id );
                }else{
                    $user = get_user_by( 'email', $id_or_email );
                }
                if( isset( $user->ID ) && !empty( $user->ID ) ){
                    //Get User ID
                    $user_id = $user->ID;
                    $avatar_url = plugin_dir_url( __FILE__ ) . 'assets/img/default_avatar.jpg';
                    
                    $first_social_account_socialtype = 'not_binding';

                    $TSSL_Data_Query = new TSSL_Data_Query();
                    $data = $TSSL_Data_Query->TSSL_Query_Single('userid', $user_id, '%d');
                    if( isset($data[0]->socialtype ) ) {
                        foreach( $data as $key){
                            if( isset( $key->avatar ) && !empty( $key->avatar ) ){
                                $first_social_account_avatar = $key->avatar;
                                $first_social_account_socialtype = $key->socialtype;
                                break;
                            }else{
                                $first_social_account_socialtype = $key->socialtype;
                                break;
                            }
                        }

                        if( !empty($first_social_account_avatar) ){
                            $avatar_url = content_url( $first_social_account_avatar );
                        }
                        $avatar = '<img alt="TSSL_' . esc_attr( $first_social_account_socialtype ) . '" src="' . esc_url( $avatar_url ) . '" class="avatar avatar-' . esc_attr( $size ) . ' photo" height="' . esc_attr( $size ) . '" width="' . esc_attr( $size ) . '" />';
                    }else{
                        $avatar = '<img alt="TSSL_' . esc_attr( $first_social_account_socialtype ) . '" src="' . esc_url( $avatar_url ) . ' "class="avatar avatar-' . esc_attr( $size ) . ' photo" height="' . esc_attr( $size ) . '" width="' . esc_attr( $size ) . '" />';
                    }
                }
            }

            return $avatar;
        }

        function TSSL_Avatar_Select( $avatar_list ){
            if( current_user_can( 'manage_options' ) ){
                $avatar_defaults = array(
                    'tssl_avatar'      => __( 'TomS Social Login Avatar', 'toms-social-login' ),
                    'mystery'          => __( 'Mystery Person', 'toms-social-login' ),
                    'blank'            => __( 'Blank', 'toms-social-login' ),
                    'gravatar_default' => __( 'Gravatar Logo', 'toms-social-login' ),
                    'identicon'        => __( 'Identicon (Generated)', 'toms-social-login' ),
                    'wavatar'          => __( 'Wavatar (Generated)', 'toms-social-login' ),
                    'monsterid'        => __( 'MonsterID (Generated)', 'toms-social-login' ),
                    'retro'            => __( 'Retro (Generated)', 'toms-social-login' ),
                );
                $default         = get_option( 'avatar_default', 'mystery' );
                $avatar_list     = '';
                
                foreach ( $avatar_defaults as $default_key => $default_name ) {
                    switch( $default_key ){
                        case 'tssl_avatar':
                            $toms_avatar = plugin_dir_url( __FILE__ ) . 'assets/img/default_avatar.jpg';
                            break;
                        case 'mystery':
                            $toms_avatar = 'https://secure.gravatar.com/avatar/840afac7ffddba505b8d444fb388a542?s=32&amp;d=mm&amp;f=y&amp;r=g';
                            break;
                        case 'blank':
                            $toms_avatar = 'https://secure.gravatar.com/avatar/840afac7ffddba505b8d444fb388a542?s=32&d=blank&f=y&r=g';
                            break;
                        case 'gravatar_default':
                            $toms_avatar = 'https://secure.gravatar.com/avatar/840afac7ffddba505b8d444fb388a542?s=32&f=y&r=g';
                            break;
                        case 'identicon':
                            $toms_avatar = 'https://secure.gravatar.com/avatar/840afac7ffddba505b8d444fb388a542?s=32&d=identicon&f=y&r=g';
                            break;
                        case 'wavatar':
                            $toms_avatar = 'https://secure.gravatar.com/avatar/840afac7ffddba505b8d444fb388a542?s=32&d=wavatar&f=y&r=g';
                            break;
                        case 'monsterid':
                            $toms_avatar = 'https://secure.gravatar.com/avatar/840afac7ffddba505b8d444fb388a542?s=32&d=monsterid&f=y&r=g';
                            break;
                        case 'retro':
                            $toms_avatar = 'https://secure.gravatar.com/avatar/840afac7ffddba505b8d444fb388a542?s=32&d=retro&f=y&r=g';
                            break;
                    }
                    $selected     = ( $default === $default_key ) ? 'checked="checked" ' : '';
                    $avatar_list .= "\n\t" . '<label><input type="radio" name="avatar_default" id="avatar_' . esc_attr( $default_key ) . '" value="' . esc_attr( $default_key ) . '" ' . esc_attr( $selected ) . ' /> ';
                    $avatar_list .= '<img alt="" src="' . esc_url( $toms_avatar ) . '" srcset="' . esc_url( $toms_avatar ) . ' 2x" class="avatar avatar-32 photo avatar-default" height="32" width="32" loading="lazy">';
                    $avatar_list .= ' ' . esc_textarea( $default_name ) . '</label>';
                    $avatar_list .= '<br />';
                }
            }

            return $avatar_list;
        }

        function TSSL_Avatar_URL( $url, $id_or_email, $args ){
            if( get_option( 'avatar_default', 'mystery' ) === 'tssl_avatar' ){
                if( is_numeric( $id_or_email ) ){
                    $user = get_user_by( 'id', (int) $id_or_email );
                }elseif( is_object( $id_or_email ) && property_exists( $id_or_email, 'ID' ) ){
                    $user = get_user_by( 'id', (int) $id_or_email->ID );
                }elseif( is_object( $id_or_email ) && property_exists( $id_or_email, 'user_id' ) ){
                    $user = get_user_by( 'id', (int) $id_or_email->user_id );
                }else{
                    $user = get_user_by( 'email', $id_or_email );
                }
                if( isset( $user->ID ) && !empty( $user->ID ) ){
                    //Get User ID
                    $user_id = $user->ID;
                    $avatar_url = plugin_dir_url( __FILE__ ) . 'assets/img/default_avatar.jpg';
                    
                    $first_social_account_socialtype = 'not_binding';

                    $TSSL_Data_Query = new TSSL_Data_Query();
                    $data = $TSSL_Data_Query->TSSL_Query_Single('userid', $user_id, '%d');
                    if( isset($data[0]->socialtype ) ) {
                        foreach( $data as $key){
                            if( isset( $key->avatar ) && !empty( $key->avatar ) ){
                                $first_social_account_avatar = $key->avatar;
                                $first_social_account_socialtype = $key->socialtype;
                                break;
                            }else{
                                $first_social_account_socialtype = $key->socialtype;
                                break;
                            }
                        }

                        if( !empty($first_social_account_avatar) ){
                            $avatar_url = content_url( $first_social_account_avatar );
                        }
                        $url = $avatar_url;
                    }else{
                        $url = $avatar_url;
                    }
                }
            }

            return $url;
        }
    }

    $TSSL_Avatar = new TSSL_Avatar();
}
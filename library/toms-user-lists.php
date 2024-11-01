<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Users') ){
    class TSSL_Users{
        public function __construct() {
            add_action('admin_head', array($this, 'TSSL_User_Lists_column_style') );
            add_filter( 'manage_users_columns', array($this, 'TSSL_User_Lists_column') );
            add_filter( 'manage_users_custom_column',  array($this, 'TSSL_User_Lists_column_content'), 10, 3 );
        }

        function TSSL_User_Lists_column_style(){
            $data       = new TSSL_Data_Process();
            $BTN        = $data->Data_Process_Array();

            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();

            $style = '';
            ob_start(); ?>
            <style>
                .toms_social_login_binding .fa{
                    font-size: 32px;
                    line-height: 32px;
                    margin: 3px;
                    vertical-align: middle;
                    border-radius: 2px;
                    color: #fff;
                    cursor: pointer;
                    word-break: break-word;
                }
                .toms_social_login_binding .fa:hover{
                    opacity: 0.7;
                }
                <?php foreach( $BTN as $key => $obj ){ ?>
                    <?php echo '.toms_social_login_binding .fa-' . esc_attr( $obj->type ); ?>{
                        background-color: <?php echo esc_attr( $obj->BgColor ); ?>;
                    }
                <?php } ?>
            </style>
            <?php
            $style .= ob_get_clean();

            echo wp_kses( $style, $allowed_html, $allowed_protocols ); 
        }

        function TSSL_User_Lists_column( $columns ){
            $columns['toms_social_login_binding'] = 'Binding';
            return $columns;
        }

        function TSSL_User_Lists_column_content($contents, $column_name, $user_id){
            $TSSL_Data_Query    = new TSSL_Data_Query();
            $tomsUserData       = $TSSL_Data_Query->TSSL_Query_Single( 'userid', $user_id, '%d' );

            if( 'toms_social_login_binding' == $column_name ){
                if( $tomsUserData ){
                    $binding_types = '';
                    foreach( $tomsUserData as $key => $obj ){
                        $social_type    = isset( $obj->socialtype ) && !empty( $obj->socialtype ) ? $obj->socialtype : '';
                        $binding_types .= '<span class="fa fa-' . esc_textarea( $social_type ) . '" ></span>';
                    }
                    return $binding_types;
                }else{
                    return __('Not Binding', 'toms-social-login');
                }
            }

            return $contents;
        }
    }

    $TSSL_Users = new TSSL_Users;
}
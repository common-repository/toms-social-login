<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Login_BTN') ){
    class TSSL_Login_BTN{
        public function __construct() {
            //Wordpress
            add_action( 'login_form', array($this, 'TSSL_Button_For_No_Login_Action'), 10, 2 );
            add_action( 'register_form', array($this, 'TSSL_Button_For_No_Login_Action'), 10, 2 );
            add_action( 'lostpassword_form', array($this, 'TSSL_Button_For_No_Login_Action'), 10, 2 );

            //Shortcode
            add_shortcode( 'TSSL_Login_Button', array($this, 'TSSL_Login_Button_For_Shortcode' ) );
        }
        function TSSL_Login_Button_For_Shortcode(){
            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();

            $shortcode = self::TSSL_Login_Button();
            if( !is_user_logged_in() ){
                $html = '';
                $html .= wp_kses( $shortcode, $allowed_html, $allowed_protocols );
                return $html;
            }
        }
        function TSSL_Button_For_No_Login_Action(){
            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();

            $action = self::TSSL_Login_Button();

            //do_action 测试
            // add_action( 'TomS_Social_Login_button',  'TEST_do1');
            // function TEST_do1($test){
            // }
            // add_action( 'TomS_Social_Login_button',  'TEST_do');
            // function TEST_do(){
            //     echo 'hello';
            // }
            // $do_action = '你好';
            // do_action( 'TomS_Social_Login_button', $do_action);

            if( !is_user_logged_in() ){
                echo wp_kses( $action, $allowed_html, $allowed_protocols );
            }
        }
        function TSSL_Login_Button(){
            wp_enqueue_style( 'TSSL_Style', plugin_dir_url( __FILE__ ) . 'assets/css/iconfont.css' );
            
            $style      = get_option( 'toms_social_login_style', 'square-icon' );
            $TSSL_Style = new TSSL_Style();
            $Login_CSS  = $TSSL_Style->TSSL_Login_CSS( $style );

            $data       = new TSSL_Data_Process();
            $BTN        = $data->Data_Process_Array();
            $order_key  = get_option( 'toms_social_login__ajax_order' );

            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();

            $this->data_name    = 'toms_social_login_';

            $tssl_buttons = '';
            ob_start(); 
            if( !is_user_logged_in() ){
                echo wp_kses( $Login_CSS, $allowed_html, $allowed_protocols );
            ?>
                <div class="toms-social-login">
                    <div class="tssl-login-btn-heading">
                        <?php echo __('Connect Your Favorite Social', 'toms-social-login'); ?>
                    </div>
                    <div class="toms-social-login-buttons">
                        <?php 
                        $request_url = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) ? sanitize_textarea_field( $_SERVER['REQUEST_URI'] ) : '';
                        $request_url = preg_replace('/\?.*/i', '', $request_url);

                        foreach( $BTN as $key => $obj ){
                            ob_start();
                            ?>
                            <div class="tssl-login-btn-container <?php if( $obj->style === $style ){ echo 'tssl-login-btn-container-' . esc_attr( $obj->type ); } ?>">
                                <span class="toms-top-right"></span>
                                <a class="tssl-login-btn-link tssl-login-btn-link-<?php echo esc_attr( $obj->type ); ?>" <?php echo 'href="#void(0)" onclick="javascript:location.href=\'' . esc_url( $obj->request_URL ) . 'toms_state' . $request_url . '\'"'; ?> >
                                    <span class="toms-button-left"></span>
                                    <span class="tssl-login-btn-link-icon tssl-login-btn-link-icon-<?php echo esc_attr( $obj->type ); ?>">
                                        <i class="tssl-login-btn-link-icon-name fa fa-<?php echo esc_attr( $obj->type ); ?>"></i>
                                    </span>
                                    <span class="toms-button-center"></span>
                                    <span class="tssl-login-btn-link-text tssl-login-btn-link-text-<?php echo esc_attr( $obj->type ); ?>">
                                        <span class="tssl-login-btn-link-text-string tssl-login-btn-link-text-string-<?php echo esc_attr( $obj->type ); ?>"><?php echo esc_textarea( $obj->name ); ?></span>
                                    </span>
                                    <span class="toms-button-right"></span>
                                </a>
                                <span class="toms-bottom-left"></span>
                            </div>
                            <?php $button[$key] = ob_get_clean();
                        }

                        foreach($order_key as $sort_key => $reorder_value){
                            if( esc_textarea( get_option( $this->data_name . $BTN[$reorder_value]->type . '_enabled' ) ) == 'checked' ){
                                $social_buttons = $button[$reorder_value];
                                echo wp_kses( $social_buttons, $allowed_html, $allowed_protocols );
                            }
                        }
                        ?>
                    </div>
                </div>
            <?php }
            $tssl_buttons .= ob_get_clean();
            
            return wp_kses( $tssl_buttons, $allowed_html, $allowed_protocols );
        }
    }

    $TSSL_Login_BTN = new TSSL_Login_BTN();
}
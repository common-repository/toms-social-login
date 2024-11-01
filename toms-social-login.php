<?php
/**
 * Plugin Name:       TomS Social Login
 * Description:       Support Facebook, Google, Paypal, Github, Wechat, QQ, Weibo, Dingtalk accounts to login your wordpress site.
 * Requires at least: 5.8
 * Requires PHP:      7.0
 * Version:           1.1.0
 * Author:            Tom Sneddon
 * Author URI:        https://TomS-Caprice.org
 * License:           GPLv3 or later
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       toms-social-login
 * Domain Path:		  /languages
 */

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

require_once( plugin_dir_path( __FILE__ ) . 'vendor/toms-caprice.php');

if( !class_exists('TomSSocialLogin') ){
    class TomSSocialLogin {
        public function __construct() {
            $this->toms_slug    = 'toms-wp';
            $this->prefix       = 'toms-social-login';
            $this->name         = __('TomS Social Login', 'toms-social-login');
            $this->data_name    = 'toms_social_login_';
            
            global $wpdb;
            $this->toms_charset = $wpdb->get_charset_collate();
            $this->toms_table   = $wpdb->prefix . 'toms_social_login';


            add_action( 'activate_'.$this->prefix.'/'.$this->prefix.'.php', array($this, 'TomS_plugin_on_activate') );
            add_action( 'init', array($this, 'TomS_Init'), 10, 2);
            add_action( 'admin_menu', array($this, 'add_plugin_menu_to_TomS'), 10, 2);

            add_option( $this->data_name . '_ajax_order', array( 0,1,2,3,4,5,6,7 ), '', 'yes' );
            //add_option( $this->data_name . '_ajax_binding', '', '', 'yes' );
            add_action( 'wp_ajax_save_TSSL_order', array($this, 'Save_TSSL_Order'), 10, 2 );
            add_action( 'wp_ajax_save_TSSL_binding', array($this, 'Save_TSSL_Binding'), 10, 2 );

            //add settings button to Installed plugin page
            add_filter('plugin_action_links', array($this, 'plugin_page_setting_button'), 10, 2);
        }

        //Ajax TomS Social order
        function Save_TSSL_Order(){
            if( is_user_logged_in() ){
                if( !check_ajax_referer( 'tssl-ajax-nonce', 'security' ) ){
                    return wp_send_json_error( __('Invalid Nonce !', 'toms-social-login') );
                }
                if( current_user_can( 'manage_options' ) ){
                    $order = isset( $_POST['order'] ) ? $_POST['order'] : '';
                    $arr = [];
                    foreach($order as $key => $value){
                        $arr[$key] = (int)$value;
                    }
                    update_option( $this->data_name . '_ajax_order', $arr);
                    wp_send_json_success( __('Reorder Successfully !', 'toms-social-login') );
                }
                wp_send_json_error( __('Sorry, you don\'t have permission to reorder the icons.', 'toms-social-login') );
            }
            wp_send_json_error( __('Illegal operation, user is not logged in!', 'toms-social-login') );
        }
        //Ajax TomS Social Binding
        function Save_TSSL_Binding(){
            if( is_user_logged_in() ){
                $current_user_id    = get_current_user_id();
                $fetch_user_id      = isset( $_POST['userid'] ) && !empty( $_POST['userid'] ) ? $_POST['userid'] : '';
                $fetch_socialtype   = isset( $_POST['socialtype'] ) && !empty( $_POST['socialtype'] ) ? $_POST['socialtype'] : '';
                
                if( $current_user_id == $fetch_user_id ){
                    $TSSL_Data_Query = new TSSL_Data_Query;
                    $data = $TSSL_Data_Query->TSSL_Query('userid', $fetch_user_id, '%d', 'socialtype', $fetch_socialtype, '%s' );

                    $db_id      = isset($data[0]->id) && !empty($data[0]->id) ? $data[0]->id : '';
                    $db_type    = isset($data[0]->socialtype) && !empty($data[0]->socialtype) ? $data[0]->socialtype : ''; 

                    if( !empty( $db_id ) && !empty( $db_type )){
                        if( !check_ajax_referer( 'toms-unbind-nonce', 'security' ) ){
                            return wp_send_json_error( __('Invalid Nonce !', 'toms-social-login') );
                        }
                        if( current_user_can( 'read' ) ){
                            global $wpdb;
                            $wpdb->delete( $this->toms_table, array('id' => $db_id) );
                            return wp_send_json_success( __('Unbinding <strong>Succeeded</strong> !', 'toms-social-login') );
                        }
                        return  wp_send_json_error( __('Sorry, you don\'t have permission to Unbind this account.', 'toms-social-login') );
                    }
                    if( strlen( $fetch_socialtype ) > 2 ){
                        $error_type = esc_attr( ucwords( $fetch_socialtype ) );
                    }else{
                        $error_type = esc_attr( strtoupper( $fetch_socialtype ) );
                    }
                    return wp_send_json_error( '<strong>' . sprintf( __('%s'), $error_type ) . '</strong> ' . __( 'account is not binding!', 'toms-social-login') );
                }
                return wp_send_json_error( __('Illegal operation, unkown source!', 'toms-social-login') );
            }
            return wp_send_json_error( __('Illegal operation, user is not logged in!', 'toms-social-login') );
        }

        //Initialize the necessary database tables on plugin activate
        public function TomS_plugin_on_activate(){
            require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
            dbDelta( "CREATE TABLE $this->toms_table (
                id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
                socialtype varchar(60) NOT NULL DEFAULT '',
                userid bigint(20) unsigned NOT NULL,
                nickname varchar(60) NOT NULL DEFAULT '',
                unionid varchar(255) NOT NULL DEFAULT '',
                useremail varchar(100) NOT NULL DEFAULT '',
                logintimes bigint(20) unsigned NOT NULL,
                lastlogin varchar(60) NOT NULL DEFAULT '',
                avatar varchar(255) NOT NULL DEFAULT '',
                openid varchar(255) NOT NULL DEFAULT '',
                PRIMARY KEY  (id)
            ) $this->toms_charset;" );
        }

        public function TomS_Init(){

            load_plugin_textdomain( $this->prefix, false, dirname(plugin_basename( __FILE__ )). '/languages' );

        }

        //Add TomS Plugin's Menu and Style to Admin
        public function add_plugin_menu_to_TomS(){
            add_submenu_page(
                $this->toms_slug,
                $this->name,
                '<span class="toms-menu-item"><span class="'.$this->prefix.'"></span><span class="toms-menu-text">' . $this->name . '</span></span>',
                'manage_options',
                $this->prefix . '-settings',
                array($this,'TomS_Settings')
            );
            add_action( "admin_enqueue_scripts", array($this, 'TomS_global_style') );
            add_action( $this->toms_slug . '_page_' . $this->prefix . '-settings', array($this, 'TomS_plugin_style') );
        }

        //Plugin Global Style
        public function TomS_global_style(){
            if( is_user_logged_in() ){
                wp_enqueue_style( $this->prefix . '-global-style', plugin_dir_url( __FILE__ ) . 'admin/assets/css/' . $this->prefix . '-global.css' );
                wp_enqueue_style( 'TSSL_Style', plugin_dir_url( __FILE__ ) . 'library/assets/css/iconfont.css' );
            }
        }
        //Plugin Settings Page Style
        public function TomS_plugin_style(){
            wp_enqueue_style( $this->prefix . '-style', plugin_dir_url( __FILE__ ) . 'admin/assets/css/' . $this->prefix . '.css' );
            if( !wp_is_mobile() ){
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-sortable');
                wp_enqueue_script( $this->prefix . '-js', plugin_dir_url( __FILE__ ) . 'admin/assets/js/' . $this->prefix . '.js' );
                //通过PHP传输参数给指定的js文件
                wp_localize_script( $this->prefix . '-js', 'TSSL_Ajax_Security', array(
                    'security'  => wp_create_nonce( 'tssl-ajax-nonce' )
                ) );
            }
        }

        //Save Data
        function TomSHandleForm(){
            $data   = new TSSL_Data_Process();
            $BTN    = $data->Data_Process_Array();

            $get_nonce = isset($_POST[ $this->data_name . 'ADMIN_NONCE' ]) ? $_POST[ $this->data_name . 'ADMIN_NONCE' ] : '';
            if( wp_verify_nonce( $get_nonce, $this->data_name . 'admin_nonce' ) && current_user_can( 'manage_options' ) ){
                update_option( $this->data_name . 'style', sanitize_text_field( $_POST[ $this->data_name . 'style' ] ) );

                foreach( $BTN as $type => $obj ){
                    update_option( $this->data_name . $obj->type . '_enabled', isset( $_POST[ $this->data_name . $obj->type . '_enabled' ] ) ? sanitize_text_field( $_POST[ $this->data_name . $obj->type . '_enabled' ] )  : '');
                    update_option( $this->data_name . $obj->type . '_client_id', isset( $_POST[ $this->data_name . $obj->type . '_client_id' ] ) ? sanitize_text_field( $_POST[ $this->data_name . $obj->type . '_client_id' ] )  : '');
                    update_option( $this->data_name . $obj->type . '_secret_key', isset( $_POST[ $this->data_name . $obj->type . '_secret_key' ] ) ? sanitize_text_field( $_POST[ $this->data_name . $obj->type . '_secret_key' ] )  : '');
                    update_option( $this->data_name . $obj->type . '_callback_url', isset( $_POST[ $this->data_name . $obj->type . '_callback_url' ] ) ? sanitize_text_field( $_POST[ $this->data_name . $obj->type . '_callback_url' ] )  : '');
                }

                update_option( $this->data_name . 'binding_enabled', sanitize_text_field( $_POST[ $this->data_name . 'binding_enabled' ] ) );
                update_option( $this->data_name . 'unbind_enabled', sanitize_text_field( $_POST[ $this->data_name . 'unbind_enabled' ] ) );
                update_option( $this->data_name . 'clear_data', isset( $_POST[ $this->data_name . 'clear_data' ] ) ? sanitize_text_field( $_POST[ $this->data_name . 'clear_data' ] ) : '');
            ?>
            <div class="updated notice notice-success settings-error is-dismissible">
                <p><strong><?php _e('Settings saved.', 'toms-social-login'); ?></strong></p>
            </div>
        <?php } else { ?>
            <div class="error notice notice-success settings-error is-dismissible">
                <p><strong><?php _e('ERROR : Settings save failed.', 'toms-social-login'); ?></strong></p>
                <p class="description"><?php _e('Sorry, you don\'t have permission to perform this action.', 'toms-social-login'); ?> </p>
            </div>
        <?php }

        }

        //Plugin Settings Page Contents
        public function TomS_Settings() {

            $data   = new TSSL_Data_Process();
            $BTN    = $data->Data_Process_Array();

            $order_key  = get_option( 'toms_social_login__ajax_order' );
            $order_key  = apply_filters( 'TomS_Social_Login_Order', (array)$order_key );

            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();

        ?>
            <style>
                .toms-social-login-icon{
                    display: inline-block;
                    vertical-align: middle;
                }
                .toms-heading-text{
                    font-weight: 600;
                }
                .toms-social-login-config,
                .toms-social-login-button-style{
                    padding: 10px;
                    background-color: #ffffff;
                    border: 2px dashed #dcdcde;
                    margin: 20px 0;
                }
                .toms-social-login-button-style-items{
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: space-evenly;
                }
                .toms-social-login-button-style-items .toms-label{
                    display: flex;
                    width: 280px;
                    min-width: 280px;
                    padding: 20px;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox{
                    font-size: 24px;
                    padding: 10px;
                    display: flex;
                    align-items: center;
                    width: 240px;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox input[type=checkbox], input[type=radio]{
                    border-radius: 0;
                    align-self: center;
                    height: 2rem;
                    width: 2rem;
                    margin: 0;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox input[type=radio]:checked::before{
                    height: 1.5rem;
                    width: 1.5rem;
                    border-radius: 0;
                    margin: 3px;
                    background-color: #e91e63;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox input[type=radio]:focus{
                    border-color: #3cb035;
                    box-shadow: 0 0 0 1px #3cb035;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-square-icon .fa-toms-social-login-icon:before,
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-square-icon .fa-wechat:before{
                    font-size: 32px;
                    margin: 3px;
                    padding: 2px;
                    vertical-align: middle;
                    border-radius: 2px;
                    color: #fff;
                    background-color: #3cb035;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-square-icon{
                    display: flex;
                    align-items: center;
                    color: #fff;
                    margin-left: 15px;
                    border-radius: 50px;
                    line-height: 1.4;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-square-text .fa-toms-social-login-icon,
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-square-text .fa-wechat{
                    font-size: 32px;
                    vertical-align: middle;
                    border-radius: 2px;
                    color: #fff;
                    background-color: #3cb035;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-square-text{
                    display: flex;
                    align-items: center;
                    color: #fff;
                    margin-left: 15px;
                    padding-left: 15px;
                    border-radius: 4px;
                    width: 168px;
                    line-height: 1.4;
                    background-color: #3cb035;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-square-text .toms-square-text__text{
                    font-size: 18px;
                    font-weight: bold;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-circle-icon .fa-toms-social-login-icon:before,
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-circle-icon .fa-wechat:before{
                    font-size: 32px;
                    margin: 3px;
                    padding: 4px;
                    vertical-align: middle;
                    border-radius: 50%;
                    color: #fff;
                    background-color: #3cb035;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-circle-icon{
                    display: flex;
                    align-items: center;
                    color: #fff;
                    margin-left: 15px;
                    line-height: 1.4;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-circle-text .fa-toms-social-login-icon,
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-circle-text .fa-wechat{
                    font-size: 32px;
                    vertical-align: middle;
                    border-radius: 50%;
                    color: #fff;
                    background-color: #3cb035;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-circle-text{
                    display: flex;
                    align-items: center;
                    color: #fff;
                    margin-left: 15px;
                    padding-left: 15px;
                    width: 168px;
                    border-radius: 50px;
                    line-height: 1.4;
                    background-color: #3cb035;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-circle-text .toms-circle-text__text{
                    font-size: 18px;
                    font-weight: bold;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-qt-style .fa-toms-social-login-icon,
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-qt-style .fa-wechat{
                    font-size: 32px;
                    vertical-align: middle;
                    color: #fff;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-qt-style{
                    display: flex;
                    align-items: center;
                    color: #fff;
                    margin-left: 15px;
                    padding-left: 15px;
                    width: 168px;
                    line-height: 1.4;
                    background: linear-gradient(-45deg, transparent 9px, #3cb035 0) right,
                                linear-gradient(135deg, transparent 9px, #3cb035 0) left;
                    background-size: 51% 100%;
                    background-repeat: no-repeat;
                }
                .toms-social-login-button-style-items .toms-label .toms-checkbox .toms-qt-style .toms-qt-style__text{
                    font-size: 18px;
                    font-weight: bold;
                }
                .toms-social-login .toms-notice-ajax-sort{
                    font-size: 18px;
                    line-height: 30px;
                    height: 30px;
                    font-weight: 500;
                }
                .toms-social-login .updated{
                    color: green;
                }
                .toms-social-login .error{
                    color: red;
                }
                .toms-drag-drop{
                    display: flex;
                    flex-direction: column;
                    padding: 10px;
                    background-color: #ffffff;
                    border: 2px dashed #dcdcde;
                    margin: 20px 0;
                }
                .toms-drag-drop .p-top{
                    align-self: center;
                    color: #a1a1a1;
                }
                .toms-drag-drop .p-bottom{
                    align-self: flex-end;
                    color: rgb(244 67 54 / 38%);
                }
                .toms-social-login-config{
                    display: flex;
                    flex-direction: column;
                }
                .toms-social-login-config .callback-url-text:hover{
                    cursor: help;
                }
                .p-title{
                    font-weight: 100;
                    font-size: 18px;
                    line-height: 1;
                    padding: 0 0 15px 0;
                    margin: 0;
                }
                /* horizontal */
                .toms-drag-drop-horizontal{
                    display: flex;
                    flex-direction: row;
                    flex-wrap: wrap;
                    line-height: 1.5;
                    justify-content: center;
                }
                /* vertical */
                .toms-drag-drop-vertical{
                    display: flex;
                    flex-direction: column;
                    width: 100%;
                    min-width: 270px;
                }
                .toms-drag-drop-vertical .toms-draggable {
                    line-height: 1.4;
                    margin-bottom: 5px;
                    border-radius: 4px;
                    padding-left: 15px;
                }
                .toms-drag-drop-vertical .toms-draggable .toms-btn-texts{
                    font-size: 18px;
                    font-weight: bold;
                }
                .toms-draggable .toms-icon .fa {
                    font-size: 32px;
                    margin: 3px;
                    vertical-align: middle;
                    border-radius: 2px;
                    color: #fff;
                    word-break: break-word;
                }
                .toms-drag-drop-horizontal .toms-draggable .toms-icon .fa{
                    padding-right: 0;
                }
                .toms-drag-drop-vertical .toms-draggable .toms-icon .fa{
                    padding-right: 5px;
                }
                .toms-social-login-style{
                    display: flex;
                    flex-wrap: wrap;
                    justify-content: space-evenly;
                    padding-bottom: 30px
                }
                .toms-social-login-style .toms-social-login-type{
                    display: flex;
                    flex-direction: column;
                    margin: 10px 0;
                    padding: 15px 15px 15px 15px;
                    border-radius: 4px;
                }
                .toms-social-login-style .toms-social-login-type .toms-item{
                    display: flex;
                    justify-content: space-between;
                    flex-direction: column;
                    padding-bottom: 10px;
                }
                .toms-social-login-style .toms-social-login-type .toms-item-icon{
                    justify-content: flex-end;
                    margin-top: 10px;
                    margin-right: -10px;
                    margin-bottom: -15px;
                    flex-direction: row;
                    padding-bottom: 0px;
                }
                .toms-social-login-style .toms-social-login-type .toms-item-checkbox{
                    font-size: 24px;
                    font-weight: bold;
                    padding-bottom: 15px;
                }
                .toms-social-login-style .toms-social-login-type .toms-item input[type=checkbox]{
                    width: 2rem;
                    height: 2rem;
                }
                .toms-social-login-style .toms-social-login-type .toms-item input[type=checkbox]:checked::before{
                    width: 3.5rem;
                    height: 3rem;
                    padding: 0;
                    margin-top: -11px;
                    margin-left: -16px;
                }
                .toms-social-login-style .toms-social-login-type .toms-item-help{
                    flex-direction: row;
                    padding-top: 10px;
                    justify-content: space-between;
                }
                .toms-social-login-style .toms-social-login-type .toms-item .toms-item-key{
                    display: flex;
                    flex-direction: row;
                }
                .toms-social-login-style .toms-social-login-type .toms-item .toms-item-key .fa-key{
                    font-size: 20px;
                    padding-right: 5px;
                }
                .toms-social-login-style .toms-social-login-type .toms-item-help a{
                    color: #ffffff;
                }
                .toms-social-login-style .toms-social-login-type .toms-item-help a:hover{
                    color: #e9bebe;
                }
                .toms-social-login-style .toms-social-login-type .fa{
                    font-size: 65px;
                }
                <?php 
                
                do_action( 'TomS_Extra_style_name_css' );
                
                foreach( $BTN as $type => $obj ){ ?>
                .toms-drag-drop-vertical <?php echo '.toms-draggable-' . esc_attr( $obj->type ) . ','; ?>
                .toms-draggable .toms-icon <?php echo '.fa-' . esc_attr( $obj->type ) . ','; ?>
                .toms-social-login-style <?php echo '.' . esc_attr( $obj->type ); ?>{
                    color: <?php echo esc_attr( $obj->color ); ?>;
                    background-color: <?php echo esc_attr( $obj->BgColor ); ?>;
                    opacity: 1;
                    cursor: move;
                    <?php if($obj->type == "google"): ?>
                    background: linear-gradient(to right, #ea4335 0%, #ea4335 50%, #fbbc05 50%, #fbbc05 100%), linear-gradient(to right, #34a853 0%, #34a853 50%, #4285f4 50%, #4285f4 100%);
                    background-size: 100% 50%;
                    background-position: center top, center bottom;
                    background-repeat: no-repeat;
                    <?php endif; ?>
                }
                .toms-drag-drop-vertical <?php echo '.toms-draggable-' . esc_attr( $obj->type ) . ':hover,'; ?>
                .toms-draggable .toms-icon <?php echo '.fa-' . esc_attr( $obj->type ) . ':hover'; ?>{
                    opacity: 0.8;
                    color: <?php echo esc_attr( $obj->color ); ?>;
                    background-color: <?php echo esc_attr( $obj->BgColor ); ?>;
                    <?php if($obj->type == "google"): ?>
                    background: linear-gradient(to right, #ea4335 0%, #ea4335 50%, #fbbc05 50%, #fbbc05 100%), linear-gradient(to right, #34a853 0%, #34a853 50%, #4285f4 50%, #4285f4 100%);
                    background-size: 100% 50%;
                    background-position: center top, center bottom;
                    background-repeat: no-repeat;
                    <?php endif; ?>
                }
                <?php } ?>
                @media screen and (max-width: 500px) {
                    .toms-social-login-button-style-items .toms-label{
                        width: 100%;
                    }
                }
                @media screen and (max-width: 768px) {
                    .toms-social-login-type{
                        width: 100%;
                    }
                }

                .toms-clear-data{
                    display: flex;
                    flex-direction: column;
                    padding: 10px;
                    background-color: #ffffff;
                    border: 2px dashed #dcdcde;
                    margin:20px 0; 
                }
                .toms-clear-data .toms-label{
                    padding: 10px;
                }
                .toms-clear-data .delete-text{
                    opacity: 0.7;
                }
                .toms-clear-data .delete-warning-text{
                    opacity: 0.5;
                    padding-top: 10px;
                }
                .toms-clear-data .delete-warning-title{
                    color: #ff0f6b;
                }
                
            </style>
            <div class="wrap <?php echo esc_attr( $this->prefix ); ?>">
                <h1>
                    <span class="<?php echo esc_attr( $this->prefix ); ?>-heading">
                        <span class="<?php echo esc_attr( $this->prefix ); ?>-icon"><img src="<?php echo esc_url( plugin_dir_url( __FILE__ ) . 'vendor/assets/img/' . $this->prefix . '.png'); ?>" width="35" /></span>
                        <span class="toms-heading-text"><?php echo esc_html( $this->name ); ?></span>
                    </span>
                </h1>
                <?php if( !wp_is_mobile() ) : ?>
                <p class="description"><?php echo __('Support Facebook, Google, Paypal, Github, Wechat, QQ, Weibo, Dingtalk accounts to login your wordpress site.', 'toms-social-login');?></p>
                <?php endif; ?>
                <?php if( isset($_POST['justsubmitted']) && $_POST['justsubmitted'] == "true") $this->TomSHandleForm(); ?>
                <div class="toms-drag-drop">
                    <p class="p-title"><?php _e('Ordering', 'toms-social-login'); ?></p>
                    <p class="p-top"><?php _e('You can drag and drop to change the order of icons', 'toms-social-login'); ?></p>
                    <div class="toms-drag-drop-horizontal <?php if( !wp_is_mobile() ){ echo ' ui-sortable'; } ?>" >
                        <?php 
                        $social_icon_order = [];
                        foreach( $BTN as $type => $obj ){
                            ob_start(); ?>
                            <span order_key="<?php echo esc_attr( $obj->Sort ); ?>" class="toms-draggable <?php if( !wp_is_mobile() ){ echo ' ui-sortable-handle'; } ?> toms-draggable-<?php echo esc_attr( $obj->type ); ?>" >
                                <span class="toms-icon"><i class="fa fa-<?php echo esc_attr( $obj->type ); ?>"></i></span>
                            </span>
                        <?php 
                            $social_icon_order[$type] = ob_get_clean();
                        } 
                        foreach($order_key as $sort_key => $reorder_value){
                            $order_icon =  isset( $social_icon_order[$reorder_value] ) ? $social_icon_order[$reorder_value] : '' ;
                            echo wp_kses($order_icon, $allowed_html);
                        }
                        ?>
                    </div>
                    <p class="p-bottom"><?php _e('Warning: Drag and drop with mouse only!', 'toms-social-login'); ?></p>
                </div>

                <form method="post" class="<?php echo esc_attr( $this->prefix ); ?>-form">
                    <input type="hidden" name="justsubmitted" value="true" />
                    <?php if ( function_exists('wp_nonce_field') ){ wp_nonce_field( $this->data_name . 'admin_nonce', $this->data_name . 'ADMIN_NONCE'); }  //create a nonce to confirm the user submit from current page. ?>

                    <!--TomS Social Login Style-->
                    <div class="<?php echo esc_attr( $this->prefix ); ?>-button-style">
                        <p class="<?php echo esc_attr( $this->prefix ); ?>-button-style-title  p-title"><?php _e('Button Style', 'toms-social-login'); ?></p>
                        <div class="<?php echo esc_attr( $this->prefix ); ?>-button-style-items">
                            <?php $toms_style = !empty( esc_textarea( get_option( $this->data_name . 'style' ) ) ) ? esc_textarea( get_option( $this->data_name . 'style', 'square-icon' ) ) : 'square-icon';?>
                            <label class="toms-label">
                                <div class="toms-checkbox">
                                    <input type="radio" class="toms-square-icon-radio" name="<?php echo esc_attr( $this->data_name ); ?>style" value="square-icon" <?php if( $toms_style == 'square-icon' || empty( $toms_style ) ) echo 'checked="checked"'; ?> />
                                    <span class="toms-square-icon"><i class="fa fa-toms-social-login-icon"></i></span>
                                </div>
                            </label>
                            <label class="toms-label">
                                <div class="toms-checkbox">
                                    <input type="radio" class="toms-circle-icon-radio" name="<?php echo esc_attr( $this->data_name ); ?>style" value="circle-icon" <?php if( $toms_style == 'circle-icon' ) echo 'checked="checked"'; ?> />
                                    <span class="toms-circle-icon"><i class="fa fa-toms-social-login-icon"></i></span>
                                </div>
                            </label>
                            <label class="toms-label">
                                <div class="toms-checkbox">
                                    <input type="radio" class="toms-square-text-radio" name="<?php echo esc_attr( $this->data_name ); ?>style" value="square-text" <?php if( $toms_style == 'square-text') echo 'checked="checked"'; ?> />
                                    <span class="toms-square-text"><i class="fa fa-toms-social-login-icon"></i><span class="toms-square-text__text"><?php echo __('TomS Social');?></span></span>
                                </div>
                            </label>
                            <label class="toms-label">
                                <div class="toms-checkbox">
                                    <input type="radio" class="toms-circle-text-radio" name="<?php echo esc_attr( $this->data_name ); ?>style" value="circle-text" <?php if( $toms_style == 'circle-text') echo 'checked="checked"'; ?> />
                                    <span class="toms-circle-text"><i class="fa fa-toms-social-login-icon"></i><span class="toms-circle-text__text"><?php echo __('TomS Social');?></span></span>
                                </div>
                            </label>
                            <label class="toms-label">
                                <div class="toms-checkbox">
                                    <input type="radio" class="toms-qt-style-radio" name="<?php echo esc_attr( $this->data_name ); ?>style" value="qt-style" <?php if( $toms_style == 'qt-style') echo 'checked="checked"'; ?> />
                                    <span class="toms-qt-style"><i class="fa fa-toms-social-login-icon"></i><span class="toms-qt-style__text"><?php echo __('TomS Social QT');?></span></span>
                                </div>
                            </label>
                            <?php
                            //TomS Extra Style options
                            $toms_extra_style = array();
                            $toms_extra_style = apply_filters( 'TomS_Extra_Style_Option', (array)$toms_extra_style );
                            foreach( $toms_extra_style as $style => $style_name ){
                                if( !empty( $style ) ){ ?>
                                    <label class="toms-label">
                                        <div class="toms-checkbox">
                                            <input type="radio" class="toms-<?php echo esc_attr( $style ); ?>-radio" name="<?php echo esc_attr( $this->data_name ); ?>style" value="<?php echo esc_attr( $style ); ?>" <?php if( $toms_style == $style) echo 'checked="checked"'; ?> />
                                            <span class="toms-<?php echo esc_attr( $style ); ?>"><i class="fa fa-<?php echo esc_attr( $style ); ?>"></i><span class="toms-<?php echo esc_attr( $style ); ?>__text"><?php echo esc_textarea( $style_name ); ?></span></span>
                                        </div>
                                    </label>
                            <?php } } ?>
                        </div>
                    </div>

                    <div class="toms-social-login-config">
                        <p class="p-title"><?php _e('Configuration', 'toms-social-login'); ?></p>
                        <div class="toms-social-login-style">
                            <?php 
                            $social_textfield_reorder = [];
                            foreach( $BTN as $type => $obj ){ 
                                ob_start();
                                ?>
                                <div class="<?php echo esc_attr( $this->prefix ); ?>-type <?php echo esc_attr( $obj->type )?>" >
                                    <span class="toms-item toms-item-icon">
                                        <i class="fa fa-<?php echo esc_attr( $obj->icon ); ?>"></i>
                                    </span>
                                    <!--Enabled or Disabled-->
                                    <span class="toms-item toms-item-checkbox">
                                        <label class="toms-label">
                                            <input type="checkbox" name="<?php echo esc_attr( $this->data_name . $obj->type );?>_enabled" value="checked" <?php echo ' ' . esc_textarea( get_option( $this->data_name . $obj->type . '_enabled') ); ?> />
                                            <span class="toms-text"><?php echo esc_attr( $obj->name ); ?></span>
                                        </label>
                                    </span>
                                    <!--Client ID-->
                                    <span class="toms-item">
                                        <label for="toms-label">
                                            <span><?php echo esc_textarea( $obj->client_id_text ); ?></span>
                                        </label>
                                        <input type="text" name="<?php echo esc_attr( $this->data_name . $obj->type );?>_client_id" id="<?php echo esc_attr( $this->data_name . $obj->type );?>client_id" value="<?php echo esc_textarea( get_option( $this->data_name . $obj->type . '_client_id') ); ?>" />
                                    </span>
                                    <!--Secret Key-->
                                    <span class="toms-item">
                                        <label for="toms-label">
                                            <span><?php echo esc_textarea( $obj->secret_text ); ?></span>
                                        </label>
                                        <input type="text" name="<?php echo esc_attr( $this->data_name . $obj->type );?>_secret_key" id="<?php echo esc_attr( $this->data_name . $obj->type );?>_secret_key" value="<?php echo esc_textarea( get_option( $this->data_name . $obj->type . '_secret_key') ); ?>" />
                                    </span>
                                    <!--CallBack URI-->
                                    <span class="toms-item">
                                        <label for="toms-label">
                                            <span class="callback-url-text" title="<?php echo __('Default ', 'toms-social-login') . esc_attr( $obj->callback_url_text ) . __(' is: ', 'toms-social-login') . esc_attr( $obj->callback_url_text_title ); ?>" ><?php echo esc_textarea( $obj->callback_url_text ); ?></span>
                                        </label>
                                        <input type="text" name="<?php echo esc_attr( $this->data_name . $obj->type );?>_callback_url" id="<?php echo esc_attr( $this->data_name . $obj->type );?>_callback_url" value="<?php echo esc_textarea( get_option( $this->data_name . $obj->type . '_callback_url') );  ?>" />
                                    </span>
                                    <span  class="toms-item toms-item-help">
                                        <span class="toms-item-key"><i class="fa fa-key"></i><a href="<?php echo esc_url( $obj->create_app_url ); ?>" ><?php _e('Get the Keys', 'toms-social-login'); ?></a></span>
                                        <span ><a href="<?php echo esc_url( $obj->help ); ?>" ><?php _e('Need help?', 'toms-social-login'); ?></a></span>
                                    </span>
                                </div>
                            <?php $social_textfield_reorder[$type] = ob_get_clean(); 
                            }
                            foreach($order_key as $textfield => $reorder_field){
                                $order_text_field = isset( $social_textfield_reorder[$reorder_field] ) ? $social_textfield_reorder[$reorder_field] : '';
                                echo wp_kses($order_text_field, $allowed_html);
                            }
                            ?>
                        </div>
                    </div>

                    <!-- Bind button config -->
                    <div class="<?php echo esc_attr( $this->prefix ); ?>-button-style">
                        <p class="<?php echo esc_attr( $this->prefix ); ?>-button-style-title  p-title"><?php _e('Binding/Unbind Button', 'toms-social-login'); ?></p>
                        <p class="" ><?php _e('Show Binding/Unbind Button on user profile page.', 'toms-social-login'); ?></p>
                        <div class="" style="padding: 1em;">
                        <!--Enabled or Disabled-->
                            <span class="toms-item toms-item-checkbox">
                                <label class="toms-label" style="background-color: #1ba415; border-radius: 4px; padding: 1em; margin: 1em; color: #fff; font-weight: bold; font-size: 16px;">
                                    <input type="checkbox" name="<?php echo esc_attr( $this->data_name ); ?>binding_enabled" value="checked" <?php echo ' ' . esc_textarea( get_option( $this->data_name . 'binding_enabled', 'checked' ) ); ?> />
                                    <span class="toms-text"><?php _e('Binding Button', 'toms-social-login'); ?></span>
                                </label>
                                <label class="toms-label" style="background-color: #dc0612; border-radius: 4px; padding: 1em; margin: 1em; color: #fff; font-weight: bold; font-size: 16px;">
                                    <input type="checkbox" name="<?php echo esc_attr( $this->data_name ); ?>unbind_enabled" value="checked" <?php echo ' ' . esc_textarea( get_option( $this->data_name . 'unbind_enabled', 'checked') ); ?> />
                                    <span class="toms-text"><?php _e('Unbind Button', 'toms-social-login'); ?></span>
                                </label>
                            </span>
                        </div>
                    </div>

                    <!--Clear all data option-->
                    <div class="toms-clear-data">
                        <div class="toms-clear-data-contents">
                            <div class="toms-label">
                                <input type="checkbox" name="<?php echo esc_attr( $this->data_name . 'clear_data' ); ?>" value="0"  <?php if( esc_textarea( get_option( $this->data_name . 'clear_data') ) == "0" )  echo 'checked="checked"'; ?> />
                                <span class="delete-text"><?php _e('Delete all the configuration Data!', 'toms-social-login'); ?></span>
                                <div class="delete-warning-text"><span class="delete-warning-title"><?php _e('Warning: ', 'toms-social-login'); ?></span> <?php _e('Please check this option carefully, it will delete all data saved on this page when the plugin is deleted .', 'toms-social-login'); ?></div>
                            </div>
                        </div>
                    </div>

                    <!--Submit Button-->
                    <input type="submit" name="submit" id="submit" class="button button-primary" value="<?php _e('Save Changes', 'toms-social-login'); ?>" />
                </form>
            </div>
            <?php
        }

        /**
         * Add settings link to plugin actions
         *
         * @param  array  $plugin_actions
         * @param  string $plugin_file
         * @since  1.0
         * @return array
         */
        public function plugin_page_setting_button( $plugin_actions, $plugin_file ){
 
            $plugin_name = $this->prefix . '/' . $this->prefix . '.php';

            if ( $plugin_name === $plugin_file ) {
                $plugin_actions[] = sprintf( __( '<a href="%s">Settings</a>', 'toms-social-login' ), esc_url( admin_url( 'admin.php?page=' . $this->prefix . '-settings' ) ) );
            }
            return $plugin_actions;
        }
    }

    $TomSSocialLogin = new TomSSocialLogin();

    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-allowed.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-style.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-social-login-json.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-social-type.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-exchange-data.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-data-query.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-avatar.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-user-process.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-data-process.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-user-request.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-binding-btn.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-login-btn.php');
    require_once( plugin_dir_path( __FILE__ ) . 'library/toms-user-lists.php');

    require_once( plugin_dir_path( __FILE__ ) . 'default-supported-plugins/default-supported-plugins.php' );

    //Include TomS Plugins extra php file. glob() make the path as array.
    // $toms_include_files_array = glob( plugin_dir_path( __FILE__ ) . "inc/*.php" );
    // foreach ( $toms_include_files_array as $file ) {
    //     include_once $file;
    // }
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-wechat-login.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-qq-login.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-weibo-login.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-dingtalk-login.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-github-login.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-facebook-login.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-google-login.php' );
    require_once( plugin_dir_path( __FILE__ ) . 'inc/toms-paypal-login.php' );
}
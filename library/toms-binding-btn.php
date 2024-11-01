<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Binding_BTN') ){
    class TSSL_Binding_BTN{
        public function __construct() {
            add_action( 'show_user_profile', array($this, 'TSSL_Binding_Button_For_Action') );
            add_shortcode( 'TSSL_Binding_Button', array($this, 'TSSL_Binding_Button_For_Shortcode' ) );
        }

        function TSSL_Binding_Button_For_Shortcode(){
            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();

            $shortcode = self::TSSL_Binding_Button();
            
            return wp_kses( $shortcode, $allowed_html, $allowed_protocols );
        }
        function TSSL_Binding_Button_For_Action(){
            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();

            $action = self::TSSL_Binding_Button();
            
            echo wp_kses( $action, $allowed_html, $allowed_protocols );
        }
        function TSSL_Binding_Button(){
            wp_enqueue_style( 'TSSL_Style', plugin_dir_url( __FILE__ ) . 'assets/css/iconfont.css' );

            $TSSL_Style = new TSSL_Style();
            $Bind_CSS   = $TSSL_Style->TSSL_Binding_CSS();

            $data       = new TSSL_Data_Process();
            $BTN        = $data->Data_Process_Array();
            $order_key  = get_option( 'toms_social_login__ajax_order' );

            $allowed            = new TomS_Allowed_HTML();
            $allowed_html       = $allowed->Allowed_Html();
            $allowed_protocols  = $allowed->Allowed_Protocols();
            
            $this->data_name    = 'toms_social_login_';

            $binding_btn = '';
            ob_start(); 
            if( is_user_logged_in() ){
                wp_enqueue_style( 'wp-jquery-ui-dialog' );
                wp_enqueue_script( 'jquery-ui-dialog' );

                echo wp_kses( $Bind_CSS, $allowed_html, $allowed_protocols );
            ?>
                <div class="toms-social-login-btn-container">
                    <div class="toms-title">
                        <?php  _e('Binding/Unbind Your Favorite Social', 'toms-social-login'); ?>
                    </div>
                    <div class="toms-social-login" >
                        <?php
                        $request_url = isset($_SERVER['REQUEST_URI']) && !empty($_SERVER['REQUEST_URI']) ? sanitize_textarea_field( $_SERVER['REQUEST_URI'] ) : '';
                        $request_url = preg_replace('/\?.*/i', '', $request_url);

                        $get_current_user_id  = get_current_user_id();
                        $bind_query = new TSSL_Data_Query();

                        foreach( $BTN as $type => $obj ){
                            //查询用户是否已绑定相关社交账号
                            $binded_user = $bind_query->TSSL_Query('userid', $get_current_user_id, '%d', 'socialtype', $obj->type, '%s');
                            $user_unionid = isset( $binded_user[0]->unionid ) ? $binded_user[0]->unionid : '';

                            ob_start(); ?>
                            <div class="toms-social-login-btn <?php echo 'tssl-binding tssl-binding-'. esc_attr( $obj->type ); ?> toms-<?php echo esc_attr( $obj->type ); ?>" >
                                <a class="toms-link tssl-binding-link">
                                    <span class="toms-icon"><i class="fa fa-<?php echo esc_attr( $obj->type ); ?>"></i></span>
                                    <span class="toms-btn-texts" ><?php echo esc_attr( $obj->name ); ?></span>
                                </a>
                                <div class="toms-binding-btn">
                                    <?php 
                                    if( get_option( 'toms_social_login_unbind_enabled', 'checked' ) !== 'checked' && get_option( 'toms_social_login_binding_enabled','checked' ) !== 'checked' ){
                                            if( !empty( $user_unionid ) ){ ?>
                                                <span class="toms-btn">
                                                    <span class="toms-btn-click associated">
                                                        <a class="toms-link">
                                                            <?php echo __( 'Associated', 'toms-social-login' ); ?>
                                                        </a>
                                                    </span>
                                                </span>
                                        <?php }
                                            if( empty( $user_unionid ) ){?>
                                                <span class="toms-btn">
                                                    <span class="toms-btn-click notassociated" >
                                                        <span class="toms-link" >
                                                            <?php echo __( 'Not associated', 'toms-social-login' ); ?>
                                                        </span>
                                                    </span>
                                            </span>
                                        <?php } ?>
                                    <?php 
                                    }else{
                                    if( get_option( 'toms_social_login_binding_enabled','checked' ) === 'checked' ) { 
                                        if( empty( $user_unionid ) ){ ?>
                                            <span class="toms-btn">
                                                <span class="toms-btn-click toms-bind-btn">
                                                    <a class="toms-link" <?php echo 'href="#void(0)" onclick="javascript:location.href=\'' . esc_url( $obj->request_URL ) . 'toms_state' . $request_url . '\'"'; ?> >
                                                        <?php echo __( 'Binding', 'toms-social-login' ); ?>
                                                    </a>
                                                </span>
                                            </span>
                                    <?php }else{ ?>
                                            <span class="toms-btn">
                                                <span class="toms-btn-click toms-bind-btn associated">
                                                    <a class="toms-link">
                                                        <?php echo __( 'Associated', 'toms-social-login' ); ?>
                                                    </a>
                                                </span>
                                            </span>
                                        <?php }
                                    }
                                    
                                    if( get_option( 'toms_social_login_unbind_enabled', 'checked' ) === 'checked' ){
                                        if( !empty( $user_unionid ) ){ ?>
                                            <span class="toms-btn">
                                                <span class="toms-btn-click toms-unbind-btn" >
                                                    <span class="toms-link" >
                                                        <?php echo __( 'Unbind', 'toms-social-login' ); ?>
                                                    </span>
                                                </span>
                                            </span>
                                            <span class="toms-unbind-dialog  <?php echo ' toms-unbind-' . esc_attr( $obj->type ); ?>" data-type="<?php echo esc_attr( $obj->type ); ?>" title="<?php echo esc_attr( __( 'Unbind This Account', 'toms-social-login' ) ); ?>" style="">
                                                <span class="toms-confirm-unbind <?php echo ' toms-confirm-unbind-' . esc_attr( $obj->type ); ?>">
                                                    <?php _e('Are you sure you want to unbind ', 'toms-social-login'); ?>
                                                        <strong>
                                                            <?php if( strlen( $obj->type ) > 2 ){
                                                                    echo esc_attr( ucwords( $obj->type ) );
                                                                }else{
                                                                    echo esc_attr( strtoupper( $obj->type ) );
                                                                }
                                                            ?>
                                                        </strong>
                                                    <?php _e(' account from current user?', 'toms-social-login' ); ?>
                                                </span>
                                            </span>
                                    <?php }else{ ?>
                                            <span class="toms-btn">
                                                <span class="toms-btn-click notassociated" >
                                                    <span class="toms-link" >
                                                        <?php echo __( 'Not associated', 'toms-social-login' ); ?>
                                                    </span>
                                                </span>
                                            </span>
                                    <?php } } } ?>
                                </div>
                            </div>
                        <?php $social_icon_order[$type] = ob_get_clean();
                        }
                        
                        foreach($order_key as $sort_key => $reorder_value){
                            if( esc_textarea( get_option( $this->data_name . $BTN[$reorder_value]->type . '_enabled' ) ) == 'checked' ){
                                $social_buttons = $social_icon_order[$reorder_value];
                                echo wp_kses( $social_buttons, $allowed_html, $allowed_protocols );
                            }
                        }
                        ?>
                    </div>
                </div>

                <?php 
                
                $current_user_id   = get_current_user_id(); ?>
                <script type="text/javascript">
                    jQuery(document).ready( function($) {
                        var DeleteBtn       = $('.toms-unbind-btn');
                        var Dialog          = $('.toms-unbind-dialog');
                        var TomSAjaxUrl     = '<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>';
                        DeleteBtn.each( function(index){
                            $(this).on('click', index, function(){
                                var SocialType = Dialog.eq(index).attr('data-type');
                                $( '.toms-unbind-'+SocialType ).css('opacity', '1');
                                $( '.toms-unbind-'+SocialType ).dialog({
                                    modal: true,
                                    resizable: false,
                                    height: "auto",
                                    width: 260,
                                    buttons: {
                                        "<?php echo esc_js( __('Yes Unbinding!', 'toms-social-login') ); ?>": function() {
                                            $.ajax({
                                                url: TomSAjaxUrl,
                                                type: 'POST',
                                                data: {
                                                    action: 'save_TSSL_binding',
                                                    userid: <?php echo '"' . esc_attr( $current_user_id ) . '"'; ?>,
                                                    socialtype: SocialType,
                                                    security: <?php echo '"' . wp_create_nonce( 'toms-unbind-nonce' ) . '"'; ?>,
                                                },
                                                success: function( response ){
                                                    var error_class = response.success === true ? 'updated' : 'error';
                                                    $('.toms-unbind-'+SocialType).before('<div id="message" class="'+error_class+' toms-notice-ajax-'+SocialType+'">'+response.data+'</div>');
                                                    $('.toms-confirm-unbind-'+SocialType).css('display', 'none');
                                                    setTimeout(function() {
                                                        $('.toms-unbind-'+SocialType).dialog( "close" );
                                                        $('.toms-notice-ajax-'+SocialType).remove();
                                                        $('.toms-confirm-unbind-'+SocialType).css('display', 'block');
                                                    }, "2000")
                                                },
                                                error: function( error ){
                                                    $('.toms-unbind-'+SocialType).before('<div id="message" class="error toms-notice-ajax-'+SocialType+'">'+error+'</div>');
                                                    setTimeout(function() {
                                                        $('.toms-unbind-'+SocialType).dialog( "close" );
                                                        $('.toms-notice-ajax-'+SocialType).remove();
                                                        $('.toms-confirm-unbind-'+SocialType).css('display', 'block');
                                                    }, "2000")
                                                }
                                            });
                                        },
                                        Cancel: function() {
                                            $( this ).css('opacity', '0');
                                            $( this ).dialog( "close" );
                                        }
                                    }
                                });
                                $( '.ui-dialog' ).addClass( 'toms-dialog' );
                            })
                        })
                    })
                </script>
            <?php }
            $binding_btn .= ob_get_clean();
            
            return wp_kses( $binding_btn, $allowed_html, $allowed_protocols );
        }
    }

    $TSSL_Binding_BTN = new TSSL_Binding_BTN();
}
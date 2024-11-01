<?php

if( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if( class_exists('TomSSocialLogin') && !class_exists('TSSL_Style') ){
    class TSSL_Style{
        public function __construct() {
            
        }

        function TSSL_Login_CSS( $style ){
            $data       = new TSSL_Data_Process();
            $BTN        = $data->Data_Process_Array();

            $css = '';
            
            //TomS Extra Login Button style name
            $TomS_Extra_css = "square-icon";
            $TomS_Extra_css = apply_filters( 'TomS_Social_Extra_CSS', $TomS_Extra_css );

            switch( $style ){ /* Switch Start */
                case $TomS_Extra_css:
                ob_start(); ?>
                    <style>
                        /* TSSL Heading */
                        .toms-social-login .tssl-login-btn-heading{
                            margin-bottom: 8px;
                        }
                        /* TSSL Button Container */
                        .toms-social-login .toms-social-login-buttons{
                            display: flex;
                            margin-bottom: 15px;
                            flex-wrap: wrap;
                        }
                        /* TSSL Button Icon Item Container */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container{
                            max-width: 100%;
                        }
                        /* TSSL Button Link */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link{
                            display: flex;
                            align-items: center;
                            text-decoration: none;
                        }
                        /* TSSL Button Icon <Span> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon{
                            line-height: 1.25;
                        }
                        /* TSSL Button Icon <i> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon .tssl-login-btn-link-icon-name{
                            margin: 2px 2px 2px 2px;
                            padding: 2px;
                            font-size: 32px;
                            border-radius: 2px 2px 2px 2px;
                            vertical-align: middle;
                            word-break: break-word;
                        }
                        /* TSSL Button Text <Span> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-text{
                            display: none;
                            opacity: 0;
                        }

                        <?php foreach( $BTN as $color => $obj ){ ?>
                            /* TSSL Button Icon CSS */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon <?php echo ' .fa-' . esc_attr( $obj->type ); ?>{
                                color:              <?php echo esc_attr( $obj->color ); ?>;
                                background-color:   <?php echo esc_attr( $obj->BgColor ); ?>;
                                <?php if( $obj->type == "google" ) : ?>
                                background: linear-gradient(to right, #ea4335 0%, #ea4335 50%, #fbbc05 50%, #fbbc05 100%), linear-gradient(to right, #34a853 0%, #34a853 50%, #4285f4 50%, #4285f4 100%);
                                background-size: 100% 50%;
                                background-position: center top, center bottom;
                                background-repeat: no-repeat;
                                <?php endif; ?>
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon <?php echo ' .fa-' . esc_attr( $obj->type ) . ':hover'; ?>{
                                opacity: 0.8;
                            }
                        <?php } ?>

                    </style>
                <?php  
                $css_content = ob_get_clean();
                //TomS Social Extra Button Style Contents filter
                $css .= apply_filters( 'TomS_Social_Extra_CSS_content', $css_content );
                break; //square-icon end

                case "circle-icon":
                ob_start(); ?>
                    <style>
                        /* TSSL Heading */
                        .toms-social-login .tssl-login-btn-heading{
                            margin-bottom: 8px;
                        }
                        /* TSSL Button Container */
                        .toms-social-login .toms-social-login-buttons{
                            display: flex;
                            margin-bottom: 15px;
                            flex-wrap: wrap;
                        }
                        /* TSSL Button Icon Item Container */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container{
                            max-width: 100%;
                        }
                        /* TSSL Button Link */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link{
                            display: flex;
                            align-items: center;
                            text-decoration: none;
                        }
                        /* TSSL Button Icon <Span> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon{
                            line-height: 1.6;
                            margin: 2px;
                        }
                        /* TSSL Button Icon <i> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon .tssl-login-btn-link-icon-name{
                            margin: 2px 2px 2px 2px;
                            padding: 6px;
                            font-size: 32px;
                            border-radius: 50px;
                            vertical-align: middle;
                            word-break: break-word;
                        }
                        /* TSSL Button Text <Span> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-text{
                            display: none;
                            opacity: 0;
                        }

                        <?php foreach( $BTN as $color => $obj ){ ?>
                            /* TSSL Button Icon CSS */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon <?php echo ' .fa-' . esc_attr( $obj->type ); ?>{
                                color:              <?php echo esc_attr( $obj->color ); ?>;
                                background-color:   <?php echo esc_attr( $obj->BgColor ); ?>;
                                <?php if( $obj->type == "google" ) : ?>
                                background: linear-gradient(to right, #ea4335 0%, #ea4335 50%, #fbbc05 50%, #fbbc05 100%), linear-gradient(to right, #34a853 0%, #34a853 50%, #4285f4 50%, #4285f4 100%);
                                background-size: 100% 50%;
                                background-position: center top, center bottom;
                                background-repeat: no-repeat;
                                <?php endif; ?>
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon <?php echo ' .fa-' . esc_attr( $obj->type ) . ':hover'; ?>{
                                opacity: 0.8;
                            }
                        <?php } ?>

                    </style>
                <?php  
                $css .= ob_get_clean();
                break; //circle-icon end

                case "square-text":
                ob_start(); ?>
                    <style>
                        /* TSSL Heading */
                        .toms-social-login .tssl-login-btn-heading{
                            margin-bottom: 8px;
                        }
                        /* TSSL Button Container */
                        .toms-social-login .toms-social-login-buttons{
                            display: flex;
                            margin-bottom: 15px;
                            flex-wrap: wrap;
                            flex-direction: column;
                        }
                        /* TSSL Button Icon Item Container */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container{
                            max-width: 100%;
                            display: flex;
                            align-items: center;
                            border-radius: 4px;
                            line-height: 1.4;
                            margin: 2px;
                        }
                        /* TSSL Button Link */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link{
                            display: flex;
                            align-items: center;
                            text-decoration: none;
                            padding: 5px 15px;
                            width: 100%;
                        }
                        /* TSSL Button Icon <Span> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon{
                            line-height: 1.1;
                        }
                        /* TSSL Button Icon <i> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon .tssl-login-btn-link-icon-name{
                            margin: 2px 2px 2px 2px;
                            padding: 0 5px 0 0;
                            font-size: 32px;
                            border-radius: 2px 2px 2px 2px;
                            vertical-align: middle;
                            word-break: break-word;
                        }
                        <?php foreach( $BTN as $color => $obj ){ ?>
                            /* TSSL Button Icon CSS */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon <?php echo ' .fa-' . esc_attr( $obj->type ); ?>{
                                color:              <?php echo esc_attr( $obj->color ); ?>;
                            }
                            /* TSSL Button String CSS */
                            .toms-social-login .toms-social-login-buttons <?php echo ' .tssl-login-btn-container-' . esc_attr( $obj->type ); ?>{
                                color:              <?php echo esc_attr( $obj->color ); ?>;
                                background-color:   <?php echo esc_attr( $obj->BgColor ); ?>;
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-text <?php echo ' .tssl-login-btn-link-text-string-' . esc_attr( $obj->type ); ?>{
                                font-size: 18px;
                                font-weight: bold;
                                color: <?php echo esc_attr( $obj->color ); ?>;
                            }
                            .toms-social-login .toms-social-login-buttons <?php echo ' .tssl-login-btn-container-' . esc_attr( $obj->type ). ':hover'; ?>{
                                opacity: 0.8;
                            }
                        <?php } ?>

                    </style>
                <?php  
                $css .= ob_get_clean();
                break; //square-text end

                case "circle-text":
                ob_start(); ?>
                    <style>
                        /* TSSL Heading */
                        .toms-social-login .tssl-login-btn-heading{
                            margin-bottom: 8px;
                        }
                        /* TSSL Button Container */
                        .toms-social-login .toms-social-login-buttons{
                            display: flex;
                            margin-bottom: 15px;
                            flex-wrap: wrap;
                            flex-direction: column;
                        }
                        /* TSSL Button Icon Item Container */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container{
                            max-width: 100%;
                            display: flex;
                            align-items: center;
                            border-radius: 50px 50px 50px 50px;
                            line-height: 1.4;
                            margin: 2px;
                        }
                        /* TSSL Button Link */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link{
                            display: flex;
                            align-items: center;
                            text-decoration: none;
                            padding: 5px 15px;
                            width: 100%;
                        }
                        /* TSSL Button Icon <Span> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon{
                            line-height: 1.1;
                        }
                        /* TSSL Button Icon <i> */
                        .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon .tssl-login-btn-link-icon-name{
                            margin: 2px 2px 2px 2px;
                            padding: 0 5px 0 0;
                            font-size: 32px;
                            vertical-align: middle;
                            word-break: break-word;
                        }
                        <?php foreach( $BTN as $color => $obj ){ ?>
                            /* TSSL Button Icon CSS */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon <?php echo ' .fa-' . esc_attr( $obj->type ); ?>{
                                color:              <?php echo esc_attr( $obj->color ); ?>;
                            }
                            /* TSSL Button String CSS */
                            .toms-social-login .toms-social-login-buttons <?php echo ' .tssl-login-btn-container-' . esc_attr( $obj->type ); ?>{
                                color:              <?php echo esc_attr( $obj->color ); ?>;
                                background-color:   <?php echo esc_attr( $obj->BgColor ); ?>;
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-text <?php echo ' .tssl-login-btn-link-text-string-' . esc_attr( $obj->type ); ?>{
                                font-size: 18px;
                                font-weight: bold;
                                color: <?php echo esc_attr( $obj->color ); ?>;
                            }
                            .toms-social-login .toms-social-login-buttons <?php echo ' .tssl-login-btn-container-' . esc_attr( $obj->type ). ':hover'; ?>{
                                opacity: 0.8;
                            }
                        <?php } ?>

                    </style>
                <?php  
                $css .= ob_get_clean();
                break; //circle-text end

                case "qt-style":
                    ob_start(); ?>
                        <style>
                            /* TSSL Heading */
                            .toms-social-login .tssl-login-btn-heading{
                                margin-bottom: 8px;
                            }
                            /* TSSL Button Container */
                            .toms-social-login .toms-social-login-buttons{
                                display: flex;
                                margin-bottom: 15px;
                                flex-wrap: wrap;
                                flex-direction: column;
                            }
                            /* TSSL Button Icon Item Container */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container{
                                max-width: 100%;
                                display: flex;
                                align-items: center;
                                line-height: 1.4;
                                margin: 2px;
                            }
                            /* TSSL Button Link */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link{
                                display: flex;
                                align-items: center;
                                justify-content: space-between;
                                text-decoration: none;
                                padding: 5px 15px;
                                width: 100%;
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .toms-button-left{
                                display: none;
                                opacity: 0;
                                order: 0;
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .toms-button-center{
                                display: none;
                                opacity: 0;
                                order: 2;
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .toms-button-right{
                                display: none;
                                opacity: 0;
                                order: 4;
                            }
                            /* TSSL Button Icon <Span> */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon{
                                line-height: 1.1;
                                order: 1;
                            }
                            /* TSSL Button Icon <i> */
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon .tssl-login-btn-link-icon-name{
                                margin: 2px 2px 2px 2px;
                                padding: 0 5px 0 0;
                                font-size: 32px;
                            }
                            .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-text{
                                align-self: flex-start;
                                margin-top: 0px;
                                margin-right: -5px;
                                order: 3;
                            }
                            <?php foreach( $BTN as $color => $obj ){ ?>
                                /* TSSL Button Icon CSS */
                                .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-icon <?php echo ' .fa-' . esc_attr( $obj->type ); ?>{
                                    color:              <?php echo esc_attr( $obj->color ); ?>;
                                }
                                /* TSSL Button String CSS */
                                .toms-social-login .toms-social-login-buttons <?php echo ' .tssl-login-btn-container-' . esc_attr( $obj->type ); ?>{
                                    color: <?php echo esc_attr( $obj->color ); ?>;
                                    background: linear-gradient(-45deg, transparent 9px, <?php echo esc_attr( $obj->BgColor ); ?> 0) right,
                                                linear-gradient(135deg, transparent 9px, <?php echo esc_attr( $obj->BgColor ); ?> 0) left;
                                    background-size: 51% 100%;
                                    background-repeat: no-repeat;
                                    }
                                .toms-social-login .toms-social-login-buttons .tssl-login-btn-container .tssl-login-btn-link .tssl-login-btn-link-text <?php echo ' .tssl-login-btn-link-text-string-' . esc_attr( $obj->type ); ?>{
                                    font-size: 18px;
                                    font-weight: bold;
                                    color: <?php echo esc_attr( $obj->color ); ?>;
                                }
                                .toms-social-login .toms-social-login-buttons <?php echo ' .tssl-login-btn-container-' . esc_attr( $obj->type ). ':hover'; ?>{
                                    opacity: 0.8;
                                }
                            <?php } ?>
    
                        </style>
                    <?php  
                    $css .= ob_get_clean();
                    break; //QT-style end

            } /* Switch end */

            return $css;
        }

        //Binding Button style
        function TSSL_Binding_CSS(){
            $data       = new TSSL_Data_Process();
            $BTN        = $data->Data_Process_Array();
            $defalut_css = '';
            ob_start(); ?>
                <style>
                    /* Binding btn container */
                    .toms-social-login-btn-container{
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 10px;
                        background-color: #ffffff;
                        border: 2px dashed #dcdcde;
                        margin: 20px 0;
                    }
                    .toms-social-login-btn-container .toms-title{
                        font-size: 16px;
                        font-weight: 100;
                        align-self: flex-start;
                        margin-bottom: 8px;
                    }
                    .toms-social-login{
                        display: flex;
                        margin-bottom: 15px;
                        flex-direction: row;
                        flex-wrap: wrap;
                        justify-content: space-evenly;
                    }
                    .toms-social-login .tssl-binding{
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        margin: 8px;
                        padding: 10px;
                    }
                    .toms-social-login .tssl-binding .tssl-binding-link{
                        align-self: flex-start;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn{
                        margin: 20px 0 0 0;
                        background-color: rgba(255, 255, 255, 0.9);
                        padding: 10px 5px 10px 5px;
                        border-radius: 5px;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn{
                        margin: 5px 10px;
                        display: inline-block;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn .toms-btn-click{
                        border: none;
                        display: block;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn .toms-bind-btn{
                        background-color: #1ba415;
                        border-radius: 4px;
                    }
                    div.toms-social-login .tssl-binding .toms-binding-btn .toms-btn .associated:hover,
                    div.toms-social-login .tssl-binding .toms-binding-btn .toms-btn .associated{
                        background-color: rgb(76 175 80 / 55%);
                        border-radius: 4px;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn .toms-bind-btn:hover{
                        background-color: #007c00;
                        cursor: pointer;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn .toms-unbind-btn{
                        background-color: #dc0612;
                        border-radius: 4px;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn .notassociated{
                        background-color: rgb(244 67 54 / 55%);
                        border-radius: 4px;
                        cursor: pointer;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn .toms-unbind-btn:hover{
                        background-color: #ff1000;
                        cursor: pointer;
                    }
                    .toms-social-login .tssl-binding .toms-binding-btn .toms-btn .toms-link{
                        padding: 10px 15px;
                        line-height: 1.4;
                        text-decoration: none;
                        font-size: 14px;
                        font-weight: 500;
                    }
                    .toms-social-login-btn{
                        margin-bottom: 5px;
                        border-radius: 4px;
                        max-width: 100%;
                    }
                    .toms-social-login-btn .toms-link{
                        display: flex;
                        align-items: center;
                        color: #fff;
                        margin-right: 1px;
                        line-height: 1;
                        text-decoration: none;
                    }
                    .toms-social-login-btn .toms-link .toms-icon .fa{
                        font-size: 32px;
                        margin: 2px;
                        padding-right: 0;
                        vertical-align: middle;
                        border-radius: 2px;
                        color: #fff;
                        word-break: break-word;
                    }
                    .toms-social-login-btn .toms-link .toms-btn-texts{
                        font-size: 18px;
                        font-weight: bold;
                    }
                    /* Icon background color */
                    <?php
                        foreach($BTN as $name => $color){ ?>
                            <?php echo '.toms-social-login .tssl-binding-' . esc_attr( $color->type ) . ','; ?>
                            .toms-social-login-btn .toms-link .toms-icon <?php echo '.fa-' . esc_attr( $color->type ); ?>{
                                background-color: <?php echo esc_attr( $color->BgColor ); ?>
                            }
                            <?php if( $color->type === 'google' ) : ?>
                            .toms-social-login .tssl-binding-google{
                                background: linear-gradient(to right, #ea4335 0%, #ea4335 50%, #fbbc05 50%, #fbbc05 100%), linear-gradient(to right, #34a853 0%, #34a853 50%, #4285f4 50%, #4285f4 100%);
                                background-size: 100% 50%;
                                background-position: center top, center bottom;
                                background-repeat: no-repeat;
                             }
                             <?php endif; ?>
                        <?php }
                    ?>
                    @media screen and (max-width: 500px) {
                        .toms-social-login .tssl-binding{
                            width: 100%;
                        }
                    }
                    /* Dialog Style */
                    .toms-unbind-dialog{
                        display: none;
                        padding: 0;
                        opacity: 0;
                    }
                    /* Modal style */
                    .toms-dialog{
                        padding: 10px;
                        border-radius: 5px;
                        background: #ffffff;
                    }
                    .toms-dialog .updated strong{
                        font-weight: bold;
                        color: #00a32a;
                    }
                    .toms-dialog .updated,
                    .toms-dialog .error{
                        padding: 10px 5px;
                        margin: 10px 0;
                        font-size: 16px;
                    }
                    .toms-dialog .ui-dialog-titlebar{
                        padding: 0 8px 10px 8px;
                    }
                    .toms-dialog .ui-dialog-buttonpane{
                        padding: 10px 5px 0 5px;
                    }
                    .toms-dialog .ui-dialog-buttonpane .ui-dialog-buttonset{
                        padding: 4px 0 0 0;
                    }
                    .toms-dialog .ui-dialog-buttonpane .ui-dialog-buttonset .ui-button{
                        line-height: 1;
                        padding: 15px;
                        height: auto;
                        font-weight: bold;
                        border-radius: 4px;
                        color: #ffffff;
                        border: none;
                        display: inline-block;
                    }
                    .toms-dialog .ui-dialog-buttonpane .ui-dialog-buttonset .ui-button:first-child{
                        background-color: #dc0612;
                    }
                    .toms-dialog .ui-dialog-buttonpane .ui-dialog-buttonset .ui-button:first-child:hover{
                        background-color: #ff1000;
                        cursor: pointer;
                    }
                    .toms-dialog .ui-dialog-buttonpane .ui-dialog-buttonset .ui-button:last-child{
                        background-color: #1ba415;
                    }
                    .toms-dialog .ui-dialog-buttonpane .ui-dialog-buttonset .ui-button:last-child:hover{
                        background-color: #007c00;
                        cursor: pointer;
                    }
                    .toms-dialog .toms-confirm-unbind{
                        padding: 10px;
                        display: inline-block;
                        font-size: 16px;
                        line-height: 1.4;
                    }
                    .toms-dialog .error strong,
                    .toms-dialog .toms-confirm-unbind strong{
                        color: red;
                    }
                </style>
            <?php
            $defalut_css .= ob_get_clean();

            return $defalut_css;
        }
    }
}
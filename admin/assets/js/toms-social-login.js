jQuery(document).ready( function($) {
    var TSSL_Msg = $('.description');
    var TSSL_Order = $('.ui-sortable');
    TSSL_Order.sortable({
        update: function (event, ui) {
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'save_TSSL_order',
                    order: TSSL_Order.sortable( 'toArray' , { attribute: 'order_key' }),
                    security: TSSL_Ajax_Security.security
                },
                success: function( response ){
                    TSSL_Msg.before('<div id="message" class="updated is-dismissible toms-notice-ajax-sort">'+response.data+'</div>');
                    setTimeout(function() {
                        $('.toms-notice-ajax-sort').remove();
                    }, "1500")
                },
                error: function( error ){
                    TSSL_Msg.before('<div id="message" class="error is-dismissible toms-notice-ajax-sort">'+error.data+'</div>');
                    setTimeout(function() {
                        $('.toms-notice-ajax-sort').remove();
                    }, "1500")
                }
            });
        }
    });
});

$(document).ready(function () {
   prestashop.on(
           'updateCart',
           function (event) {
              var refreshURL = $('.gmgetfreeshipping').data('refresh-url');
              var requestData = {};

              if (event && event.reason) {
                 requestData = {
                    id_product_attribute: event.reason.idProductAttribute,
                    id_product: event.reason.idProduct,
                    action: event.reason.linkAction
                 };
              }

              $.post(refreshURL, requestData).then(function (resp) {
                 $('.gmgetfreeshipping').replaceWith($(resp.preview));
              }).fail(function (resp) {
                 prestashop.emit('handleError', {eventType: 'updateShoppingCart', resp: resp});
              });
           }
   );
});
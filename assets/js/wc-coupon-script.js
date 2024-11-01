jQuery(document).ready(function($) {
  jQuery('#eplugins_apply_coupon').on('click', function(e) {
      e.preventDefault();
      // BELT-KNIFE1test-DR7CTU5637
      var coupon_code = jQuery('#eplugins_coupon_code').val();
      jQuery('#eplugins_coupon_code').attr('disabled', true);
      jQuery('#eplugins_coupon_message').html('');
      
      jQuery.ajax({
          url: '/?wc-ajax=apply_coupon',
          type: 'POST',
          data: {
              coupon_code: coupon_code,
              security: eplugins_coupon.apply_coupon_nonce
          },
          success: function(response) {
              // Re-enable input and button
              jQuery('#eplugins_coupon_code').attr('disabled', false);
              jQuery('#eplugins_apply_coupon').html('Apply Coupon');
              jQuery('.woocommerce-notices-wrapper').html(response);
              jQuery(document.body).trigger('wc_update_cart');
              
          },
          error: function(jqXHR,textStatus,errorThrown) {
              // Handle the error
              jQuery('#eplugins_coupon_code').attr('disabled', false);
              jQuery('#eplugins_apply_coupon').html('Apply Coupon');
              jQuery('#eplugins_coupon_message').html('<div class="woocommerce-error">There was an error applying the coupon. Please try again.</div>');
          }
      });
  });
});

jQuery(document).ready(function($) {
    // Add close button to notices
    jQuery(document).on('click', '.woocommerce-notices-wrapper .notice-close', function() {
        $(this).closest('.woocommerce-message, .woocommerce-error, .woocommerce-info').fadeOut();
    });

    // Append close button to notices dynamically
    jQuery('.woocommerce-notices-wrapper .woocommerce-message, .woocommerce-notices-wrapper .woocommerce-error, .woocommerce-notices-wrapper .woocommerce-info').each(function() {
        jQuery(this).append('<span class="notice-close" style="cursor: pointer; float: right;">&times;</span>');
    });
});

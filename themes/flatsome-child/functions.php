<?php
// Add custom Theme Functions here
add_filter('use_block_editor_for_post', '__return_false');
/**
 * Allow HTML in term (category, tag) descriptions
 */

// Add Font Awesome
function wpb_load_fa() {
	wp_enqueue_style( 'wpb-fa', get_stylesheet_directory_uri() . '/fontawesome-pro-5.15.3-web/css/all.css' );
}
add_action( 'wp_enqueue_scripts', 'wpb_load_fa' );
 
 
foreach ( array( 'pre_term_description' ) as $filter ) {
	remove_filter( $filter, 'wp_filter_kses' );
	if ( ! current_user_can( 'unfiltered_html' ) ) {
		add_filter( $filter, 'wp_filter_post_kses' );
	}
}
 
foreach ( array( 'term_description' ) as $filter ) {
	remove_filter( $filter, 'wp_kses_data' );
}

/*Sale price by devvn - levantoan.com*/
function devvn_price_html($product, $is_variation = false){
    ob_start();
 
    if($product->is_on_sale()):
    ?>
    <style>
        .devvn_single_price {
            background-color: #199bc42e;
            border: 1px dashed #199bc4;
            padding: 10px;
            border-radius: 3px;
            -moz-border-radius: 3px;
            -webkit-border-radius: 3px;
            margin: 0 0 10px;
            color: #000;
        }
 
        .devvn_single_price span.label {
            color: #333;
            font-weight: 400;
            font-size: 14px;
            padding: 0;
            margin: 0;
            float: left;
            width: 82px;
            text-align: left;
            line-height: 18px;
        }
        .gia_sale .amount {
			font-size: 30px !important;
			color: #e5322d;
			font-weight: 600;
			text-shadow: 1px 1px 0 #fff,-1px -1px 0 #fff,1px -1px 0 #fff,-1px 1px 0 #fff,3px 3px 5px #333;
		}
		.devvn_single_price small{
			margin-left: 10px !important;
			bottom: 1px;
			position: relative;
			color: #444;
		}
		.devvn_single_price span.devvn_price{
			font-size: 14px;
		}
		</style>
    <?php
    endif;
 
    if($product->is_on_sale() && ($is_variation || $product->is_type('simple') || $product->is_type('external'))) {
        $sale_price = $product->get_sale_price();
        $regular_price = $product->get_regular_price();
        if($regular_price) {
            $sale = round(((floatval($regular_price) - floatval($sale_price)) / floatval($regular_price)) * 100);
            $sale_amout = $regular_price - $sale_price;
            ?>
            <div class="devvn_single_price">
                <div>
                    <!--<span class="label">Giá:</span>-->
                    <span class="devvn_price gia_sale"><?php echo wc_price($sale_price); ?></span><small class="mgl10">( Đã có VAT )</small>
                </div>
                <div>
                    <span class="label">Thị trường:</span>
                    <span class="devvn_price gia_thi_truong"><del><?php echo wc_price($regular_price); ?></del></span>
                </div>
                <div>
                    <span class="label">Tiết kiệm:</span>
                    <span class="devvn_price tiet_kiem"> <?php echo wc_price($sale_amout); ?> (<?php echo $sale; ?>%)</span>
                </div>
            </div>
            <?php
        }
    }elseif($product->is_on_sale() && $product->is_type('variable')){
        $prices = $product->get_variation_prices( true );
        if ( empty( $prices['price'] ) ) {
            $price = apply_filters( 'woocommerce_variable_empty_price_html', '', $product );
        } else {
            $min_price     = current( $prices['price'] );
            $max_price     = end( $prices['price'] );
            $min_reg_price = current( $prices['regular_price'] );
            $max_reg_price = end( $prices['regular_price'] );
 
            if ( $min_price !== $max_price ) {
                $price = wc_format_price_range( $min_price, $max_price ) . $product->get_price_suffix();
            } elseif ( $product->is_on_sale() && $min_reg_price === $max_reg_price ) {
                $sale = round(((floatval($max_reg_price) - floatval($min_price)) / floatval($max_reg_price)) * 100);
                $sale_amout = $max_reg_price - $min_price;
                ?>
                <div class="devvn_single_price">
                    <div>
                        <span class="label">Giá:</span>
                        <span class="devvn_price"><?php echo wc_price($min_price); ?></span>
                    </div>
                    <div>
                        <span class="label">Thị trường:</span>
                        <span class="devvn_price"><del><?php echo wc_price($max_reg_price); ?></del></span>
                    </div>
                    <div>
                        <span class="label">Tiết kiệm:</span>
                        <span class="devvn_price sale_amount"> <?php echo wc_price($sale_amout); ?> (<?php echo $sale; ?>%)</span>
                    </div>
                </div>
                <?php
            } else {
                $price = wc_price( $min_price ) . $product->get_price_suffix();
            }
        }
        echo $price;
 
    }else{ ?>
        <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) );?>"><?php echo $product->get_price_html(); ?></p>
    <?php }
    return ob_get_clean();
}
function woocommerce_template_single_price(){
    global $product;
    echo devvn_price_html($product);
}
 
add_filter('woocommerce_available_variation','devvn_woocommerce_available_variation', 10, 3);
function devvn_woocommerce_available_variation($args, $thisC, $variation){
    $old_price_html = $args['price_html'];
    if($old_price_html){
        $args['price_html'] = devvn_price_html($variation, true);
    }
    return $args;
}

/*Tùy chỉnh trang thanh toán Woocommerce*/
/*Sắp xếp lại thứ tự các field*/
add_filter("woocommerce_checkout_fields", "order_fields");
function order_fields($fields) {
 
  //Shipping
  $order_shipping = array(
    "shipping_last_name",
    "shipping_phone",
    "shipping_address_1"
  );
  foreach($order_shipping as $field_shipping)
  {
    $ordered_fields2[$field_shipping] = $fields["shipping"][$field_shipping];
  }
  $fields["shipping"] = $ordered_fields2;
  return $fields;
}
/*Remove field Company, First Name, Postcode, Country, City, State, Address, Email*/
/*Bỏ First name và Last name thay bằng trường Họ và tên*/
add_filter( 'woocommerce_checkout_fields' , 'remove_email_checkout_form',9999 );
function remove_email_checkout_form( $fields ) {
	unset($fields['billing']['billing_email']); 
	return $fields;
}
add_filter( 'woocommerce_checkout_fields' , 'custom_override_checkout_fields',99 );
function custom_override_checkout_fields( $fields ) {
	  unset($fields['billing']['billing_company']);
	  unset($fields['billing']['billing_first_name']);
	  unset($fields['billing']['billing_postcode']);
	  unset($fields['billing']['billing_country']);
	  unset($fields['billing']['billing_city']);
	  unset($fields['billing']['billing_state']);
	  unset($fields['billing']['billing_address_2']);
	  $fields['billing']['billing_last_name'] = array(
		'label' => __('Họ và tên', 'devvn'),
		'placeholder' => _x('Nhập họ và tên của bạn', 'placeholder', 'devvn'),
		'required' => true,
		'class' => array('form-row-wide'),
		'clear' => true
	  );
	  $fields['billing']['billing_address_1']['placeholder'] = 'Nhập địa chỉ nhận hàng';
	 
	  unset($fields['shipping']['shipping_company']);
	  unset($fields['shipping']['shipping_postcode']);
	  unset($fields['shipping']['shipping_country']);
	  unset($fields['shipping']['shipping_city']);
	  unset($fields['shipping']['shipping_state']);
	  unset($fields['shipping']['shipping_address_2']);
	 
	  $fields['shipping']['shipping_phone'] = array(
		'label' => __('Điện thoại', 'devvn'),
		'placeholder' => _x('Số điện thoại người nhận hàng', 'placeholder', 'devvn'),
		'required' => true,
		'class' => array('form-row-wide'),
		'clear' => true
	  );
	  $fields['shipping']['shipping_last_name'] = array(
		'label' => __('Họ và tên', 'devvn'),
		'placeholder' => _x('Nhập đầy đủ họ và tên của người nhận', 'placeholder', 'devvn'),
		'required' => true,
		'class' => array('form-row-wide'),
		'clear' => true
	  );
	  $fields['shipping']['shipping_address_1']['placeholder'] = 'Nhập địa chỉ nhận hàng';
	 
	  return $fields;
}
 
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
function my_custom_checkout_field_display_admin_order_meta($order){
  echo '<p><strong>'.__('Số ĐT người nhận').':</strong> <br>' . get_post_meta( $order->id, '_shipping_phone', true ) . '</p>';
}
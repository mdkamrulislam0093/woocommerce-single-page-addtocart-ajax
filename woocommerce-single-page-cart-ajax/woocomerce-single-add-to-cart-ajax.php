<?php
/**
 * Plugin Name: WooCommerce Single Page Add to Cart Ajax
 * Plugin URI: #
 * Description: Woocommerce Only Single page Add to Cart Button Ajax.
 * Version: 1.0
 * Author: K
 * Author URI: #
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

defined( 'ABSPATH' ) || exit;
define( 'ATC_AJAX_VERSION', '1.0' ); // WRCS: DEFINED_VERSION.
define( 'ATC_PLUGIN_DIR', plugin_dir_url( __FILE__ ));

// Plugin init hook.
add_action( 'plugins_loaded', 'atc_ajax_init' );

/**
 * Initialize plugin.
 */
function atc_ajax_init() {

	if ( ! class_exists( 'WooCommerce' ) ) {
		add_action( 'admin_notices', 'atc_ajax_woocommerce_deactivated' );
		return;
	}

    $atc_ajax_main_class = ATC_AJAX_MAIN::get_instance();
}

/**
 * WooCommerce Deactivated Notice.
 */
function atc_ajax_woocommerce_deactivated() {
	/* translators: %s: WooCommerce link */
	echo '<div class="error"><p>' . sprintf( esc_html__( 'WooCommerce Single Ajax requires %s to be installed and active.', 'woocommerce-shipping-se' ), '<a href="https://woocommerce.com/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}

/**
 * ATC_AJAX_MAIN Main Class
 */
class ATC_AJAX_MAIN {

    private static $instance;

    public static function get_instance(){
        if (null === self::$instance) {
      self::$instance = new self();
    }
    return self::$instance;
    }


    function __construct() {
        if ( version_compare( WC_VERSION, '2.6.0', '>' ) ) {
            add_action( 'woocommerce_after_add_to_cart_button', [$this, 'atc_ajax_wc_after_add_to_cart_button'], 30 );
            add_action( 'wp_enqueue_scripts', [ $this, 'atc_ajax_wc_wp_enqueue_scripts' ] );
        }
    }

    public function atc_ajax_wc_after_add_to_cart_button() {
        global $product;

        if ( $product->is_type('simple') && $product->supports( 'ajax_add_to_cart' ) ) {
            $classes = implode(' ',
                    array_filter(
                        array(
                            'the_custom_atc button alt',
                            $product->is_purchasable() && $product->is_in_stock() ? $product->get_type() . '_add_to_cart_button' : '',
                            $product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
                        )
                    )
                );

            echo sprintf(
                    '<button data-quantity="1" class="%s" data-product_id="%s" data-product_sku="%s">%s</button>',
                    esc_attr( !empty($classes) ? $classes : 'button' ),
                    esc_attr($product->get_id()),
                    esc_attr($product->get_sku()),
                    esc_html( $product->single_add_to_cart_text() )

                );
        }
    }

    public function atc_ajax_wc_wp_enqueue_scripts() {
        wp_register_script( 'atc_ajax_wc_script', ATC_PLUGIN_DIR . 'assets/js/main.js', ['jquery'], 1.0, true );
        wp_register_style( 'atc_ajax_wc_style', ATC_PLUGIN_DIR . 'assets/css/style.css' );

        wp_enqueue_style( 'atc_ajax_wc_style' );
        wp_enqueue_script( 'atc_ajax_wc_script' );
    }
}


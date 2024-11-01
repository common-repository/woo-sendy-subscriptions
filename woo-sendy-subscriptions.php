<?php
/*
Plugin Name: Woo Sendy Subscriptions
Author: Indatos Technologies
Plugin URI: http://www.indatos.com?ref=sendy
Author URI: http://www.indatos.com?ref=sendy
Description: Add buyers to sendy mailer list
Version: 1.2


*/
if ( ! defined( 'ABSPATH' ) ) { 
    exit; // Exit if accessed directly
}
if ( ! class_exists( 'IDT_Woo_Sendy_Subscription' ) ) :

class IDT_Woo_Sendy_Subscription {

	/**
	* Construct the plugin.
	*/
	public function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
	}

	/**
	* Initialize the plugin.
	*/
	public function init() {

		// Checks if WooCommerce is installed.
		if ( class_exists( 'WC_Integration' ) ) {
			// Include our integration class.
			include_once 'includes/class-idt-woo-sendy-subscription.php';

			// Register the integration.
			add_filter( 'woocommerce_integrations', array( $this, 'idt_add_integration' ) );
		} else {
			// throw an admin error if you like
		}
	}

	/**
	 * Add a new integration to WooCommerce.
	 */
	public function idt_add_integration( $integrations ) {
		$integrations[] = 'IDT_Woo_Sendy_Subscription_Integration';
		return $integrations;
	}

}

$IDT_Woo_Sendy_Subscription = new IDT_Woo_Sendy_Subscription( __FILE__ );

endif;  
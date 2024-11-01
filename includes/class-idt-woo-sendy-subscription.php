<?php
/**
 * WooCommerce Sendy Subscriptions
 *
 * @package  IDT_Woo_Sendy_Subscription_Integration
 * @category Integration
 * @author   Indatos Technologies
 */

if ( ! class_exists( 'IDT_Woo_Sendy_Subscription_Integration' ) ) :

class IDT_Woo_Sendy_Subscription_Integration extends WC_Integration {

	/**
	 * Init and hook in the integration.
	 */
	public function __construct() {
		global $woocommerce;

		$this->id                 = 'idt-woo-sendy-subscription';
		$this->method_title       = __( 'Sendy Subscription', 'idt-woo-sendy-subscription' );
		$this->method_description = __( 'Add buyers to your Sendy Mailer list', 'idt-woo-sendy-subscription' );

		// Load the settings.
		$this->init_form_fields();
		$this->init_settings();

		// Define user set variables.
		$this->sendy_url          = $this->get_option( 'sendy_url' );
		$this->sendy_list        = $this->get_option( 'sendy_list' );

		// Actions.
		add_action( 'woocommerce_update_options_integration_' .  $this->id, array( &$this, 'process_admin_options' ) );
        add_action( 'woocommerce_payment_complete', array(&$this, 'add_to_sendy_mailer' ) );

	}


	/**
	 * Initialize integration settings form fields.
	 *
	 * @return void
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'sendy_url' => array(
				'title'             => __( 'Sendy URL', 'woocommerce-integration-demo' ),
				'type'              => 'text',
				'description'       => __( 'URL of your Sendy installtion', 'woocommerce-integration-demo' ),
				'desc_tip'          => true,
				'default'           => ''
			),
			'sendy_list' => array(
				'title'             => __( 'Sendy Mailer List Name', 'woocommerce-integration-demo' ),
				'type'              => 'text',
				'default'           => '',
               'desc_tip'          => true,
				'description'       => __( 'Name of your Sendy mailing list', 'woocommerce-integration-demo' ),
			),
	/*		'customize_button' => array(
				'title'             => __( 'Customize!', 'woocommerce-integration-demo' ),
				'type'              => 'button',
				'custom_attributes' => array(
					'onclick' => "location.href='http://www.woothemes.com'",
				),
				'description'       => __( 'Customize your settings by going to the integration site directly.', 'woocommerce-integration-demo' ),
				'desc_tip'          => true,
			)*/
		);
	}

   
   public function add_to_sendy_mailer( $order_id)
   {
      global $woocommerce;
      $order = new WC_Order($order_id);
      $url = rtrim($this->sendy_url ,"/");
      $boolean = 'true';
	//Subscribe
      $postdata = http_build_query(
         array(
            'name' => $order->billing_first_name. ' '.$order->billing_last_name,
            'email' => $order->billing_email,
            'list' => $this->sendy_list,
            'boolean' => 'true'
         )
      );
     /*
      $opts = array('http' => array('method'  => 'POST', 'header'  => 'Content-type: application/x-www-form-urlencoded', 'content' => $postdata));
      $context  = stream_context_create($opts);
      $result = file_get_contents($url.'/subscribe', false, $context);
      */
       $ch = curl_init();
      curl_setopt($ch,CURLOPT_URL,$url.'/subscribe');
       curl_setopt($ch,CURLOPT_POST, 1); 
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
         'Content-type: application/x-www-form-urlencoded'
      ));
     

      $result = curl_exec($ch);
      if($result == "1"){
          $order->add_order_note('User '. $order->billing_email.' added to Sendy Mailing list');
      }else{
         $order->add_order_note('Failed to add '. $order->billing_email.' to Sendy Mailing list');
      }
      return $order_status;
      
   }
	


	/**
	 * Santize our settings
	 * @see process_admin_options()
	 */
	public function sanitize_settings( $settings ) {
		
		return $settings;
	}

}


endif;

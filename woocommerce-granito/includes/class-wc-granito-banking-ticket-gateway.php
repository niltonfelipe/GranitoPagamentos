<?php
/**
 * Granito Banking Ticket gateway
 *
 * @package WooCommerce_Granito/Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Granito_Banking_Ticket_Gateway class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Granito_Banking_Ticket_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                   = 'granito-banking-ticket';
		$this->icon                 = apply_filters( 'WC_Granito_banking_ticket_icon', false );
		$this->has_fields           = true;
		$this->method_title         = __( 'Granito - Banking Ticket', 'woocommerce-granito' );
		$this->method_description   = __( 'Accept banking ticket payments using Granito.', 'woocommerce-granito' );
		$this->view_transaction_url = 'https://dashboard.Granito/#/transactions/%s';

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->title          = $this->get_option( 'title' );
		$this->description    = $this->get_option( 'description' );
		$this->api_key        = $this->get_option( 'api_key' );
		$this->encryption_key = $this->get_option( 'encryption_key' );
		$this->debug          = $this->get_option( 'debug' );
		$this->async          = $this->get_option( 'async' );

		// Active logs.
		if ( 'yes' === $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Set the API.
		$this->api = new WC_Granito_API( $this );

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'email_instructions' ), 10, 3 );
		add_action( 'woocommerce_api_WC_Granito_banking_ticket_gateway', array( $this, 'ipn_handler' ) );
	}

	/**
	 * Admin page.
	 */
	public function admin_options() {
		include dirname( __FILE__ ) . '/admin/views/html-admin-page.php';
	}

	/**
	 * Check if the gateway is available to take payments.
	 *
	 * @return bool
	 */
	public function is_available() {
		return parent::is_available() && ! empty( $this->api_key ) && ! empty( $this->encryption_key ) && $this->api->using_supported_currency();
	}

	/**
	 * Settings fields.
	 */
	public function init_form_fields() {
		$this->form_fields = array(
			'enabled' => array(
				'title'   => __( 'Enable/Disable', 'woocommerce-granito' ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable Granito Banking Ticket', 'woocommerce-granito' ),
				'default' => 'no',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-granito' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'default'     => __( 'Banking Ticket', 'woocommerce-granito' ),
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-granito' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'default'     => __( 'Pay with Banking Ticket', 'woocommerce-granito' ),
			),
			'integration' => array(
				'title'       => __( 'Integration Settings', 'woocommerce-granito' ),
				'type'        => 'title',
				'description' => '',
			),
			'api_key' => array(
				'title'             => __( 'Iss', 'woocommerce-granito' ),
				'type'              => 'text',
				'description'       => sprintf( __( 'Por favor digite seu Iss. Isso é necessário para processar o pagamento e as notificações. É possível obter sua chave de API em %s.', 'woocommerce-granito' ), '<a href="https://dashboard.Granito/">' . __( 'Granito Dashboard > Minha conta', 'woocommerce-granito' ) . '</a>' ),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'encryption_key' => array(
				'title'             => __( '
Secret key', 'woocommerce-granito' ),
				'type'              => 'text',
				'description'       => sprintf( __( 'Please enter your 
Secret key. This is needed to process the payment. Is possible get your Encryption Key in %s.', 'woocommerce-granito' ), '<a href="https://dashboard.Granito/">' . __( 'Granito Dashboard > Minha conta', 'woocommerce-granito' ) . '</a>' ),
				'default'           => '',
				'custom_attributes' => array(
					'required' => 'required',
				),
			),
			'async' => array(
				'title'       => __( 'Async', 'woocommerce-granito' ),
				'type'        => 'checkbox',
				'description' => sprintf( __( 'If enabled the banking ticket url will appear in the order page, if disabled it will appear after the checkout process.', 'woocommerce-granito' ) ),
				'default'     => 'no',
			),
			'testing' => array(
				'title'       => __( 'Gateway testes', 'woocommerce-granito' ),
				'type'        => 'title',
				'description' => '',
			),
			'debug' => array(
				'title'       => __( 'Debug Log', 'woocommerce-granito' ),
				'type'        => 'checkbox',
				'label'       => __( 'Enable logging', 'woocommerce-granito' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Registre eventos Granito, como solicitações de API. Você pode verificar o log in %s', 'woocommerce-granito' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'System Status &gt; Logs', 'woocommerce-granito' ) . '</a>' ),
			),
		);
	}

	/**
	 * Payment fields.
	 */
	public function payment_fields() {
		if ( $description = $this->get_description() ) {
			echo wp_kses_post( wpautop( wptexturize( $description ) ) );
		}

		wc_get_template(
			'banking-ticket/checkout-instructions.php',
			array(),
			'woocommerce/granito/',
			WC_Granito::get_templates_path()
		);
	}

	/**
	 * Process the payment.
	 *
	 * @param int $order_id Order ID.
	 *
	 * @return array Redirect data.
	 */
	public function process_payment( $order_id ) {
		return $this->api->process_regular_payment( $order_id );
	}

	/**
	 * Thank You page message.
	 *
	 * @param int $order_id Order ID.
	 */
	public function thankyou_page( $order_id ) {
		$order = wc_get_order( $order_id );
		$data  = get_post_meta( $order_id, '_WC_Granito_transaction_data', true );

		if ( isset( $data['boleto_url'] ) && in_array( $order->get_status(), array( 'processing', 'on-hold' ), true ) ) {
			$template = 'no' === $this->async ? 'payment' : 'async';

			wc_get_template(
				'banking-ticket/' . $template . '-instructions.php',
				array(
					'url' => $data['boleto_url'],
				),
				'woocommerce/granito/',
				WC_Granito::get_templates_path()
			);
		}
	}

	/**
	 * Add content to the WC emails.
	 *
	 * @param  object $order         Order object.
	 * @param  bool   $sent_to_admin Send to admin.
	 * @param  bool   $plain_text    Plain text or HTML.
	 *
	 * @return string                Payment instructions.
	 */
	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $sent_to_admin || ! in_array( $order->get_status(), array( 'processing', 'on-hold' ), true ) || $this->id !== $order->payment_method ) {
			return;
		}

		$data = get_post_meta( $order->id, '_WC_Granito_transaction_data', true );

		if ( isset( $data['boleto_url'] ) ) {
			$email_type = $plain_text ? 'plain' : 'html';

			wc_get_template(
				'banking-ticket/emails/' . $email_type . '-instructions.php',
				array(
					'url' => $data['boleto_url'],
				),
				'woocommerce/granito/',
				WC_Granito::get_templates_path()
			);
		}
	}

	/**
	 * IPN handler.
	 */
	public function ipn_handler() {
		$this->api->ipn_handler();
	}
}

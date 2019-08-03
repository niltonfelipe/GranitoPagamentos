<?php
/**
 * Granito Credit Card gateway
 *
 * @package WooCommerce_Granito/Gateway
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * WC_Granito_Credit_Card_Gateway class.
 *
 * @extends WC_Payment_Gateway
 */
class WC_Granito_Credit_Card_Gateway extends WC_Payment_Gateway {

	/**
	 * Constructor for the gateway.
	 */
	public function __construct() {
		$this->id                   = 'granito-credit-card';
		$this->icon                 = apply_filters( 'WC_Granito_credit_card_icon', false );
		$this->has_fields           = true;
		$this->method_title         = __( 'Granito - Credit Card', 'woocommerce-granito' );
		$this->method_description   = __( 'Accept credit card payments using Granito.', 'woocommerce-granito' );
		$this->view_transaction_url = 'https://gestao.granito.com.vc';

		// Load the form fields.
		$this->init_form_fields();

		// Load the settings.
		$this->init_settings();

		// Define user set variables.
		$this->title                  = $this->get_option( 'title' );
		$this->description            = $this->get_option( 'description' );
		$this->api_key                = $this->get_option( 'api_key' );
		$this->encryption_key         = $this->get_option( 'encryption_key' );
		$this->checkout               = $this->get_option( 'checkout' );
		$this->register_refused_order = $this->get_option( 'register_refused_order' );
		$this->max_installment        = $this->get_option( 'max_installment' );
		$this->smallest_installment   = $this->get_option( 'smallest_installment' );
		$this->interest_rate          = $this->get_option( 'interest_rate', '0' );
		$this->free_installments      = $this->get_option( 'free_installments', '1' );
		$this->debug                  = $this->get_option( 'debug' );

		// Active logs.
		if ( 'yes' === $this->debug ) {
			$this->log = new WC_Logger();
		}

		// Set the API.
		$this->api = new WC_Granito_API( $this );

		// Actions.
		add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'checkout_scripts' ) );
		add_action( 'woocommerce_thankyou_' . $this->id, array( $this, 'thankyou_page' ) );
		add_action( 'woocommerce_email_after_order_table', array( $this, 'email_instructions' ), 10, 3 );
		add_action( 'woocommerce_api_WC_Granito_credit_card_gateway', array( $this, 'ipn_handler' ) );
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
				'label'   => __( 'Habilitar Granito - Cartão de Crédito', 'woocommerce-granito' ),
				'default' => 'no',
			),
			'title' => array(
				'title'       => __( 'Title', 'woocommerce-granito' ),
				'type'        => 'text',
				'description' => __( 'This controls the title which the user sees during checkout.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'default'     => __( 'Cartão de Credito', 'woocommerce-granito' ),
			),
			'description' => array(
				'title'       => __( 'Description', 'woocommerce-granito' ),
				'type'        => 'textarea',
				'description' => __( 'This controls the description which the user sees during checkout.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'default'     => __( 'Pagar com Cartão de Crédito', 'woocommerce-granito' ),
			),
			'integration' => array(
				'title'       => __( 'Integration Settings', 'woocommerce-granito' ),
				'type'        => 'title',
				'description' => '',
			),
			'api_key' => array(
				'title'             => __( 'Iss', 'woocommerce-granito' ),
				'type'              => 'text',
				'description'       => sprintf( __( 'Por favor digite seu Iss. Isso é necessário para processar o pagamento e as notificações. É possível obter sua chave de API em %s.', 'woocommerce-granito' ), '<a href="https://gestao.granito.com.vc">' . __( 'Granito Dashboard > Acessar Minha conta', 'woocommerce-granito' ) . '</a>' ),
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
			'checkout' => array(
				'title'       => __( 'Checkout Granito', 'woocommerce-granito' ),
				'type'        => 'checkbox',
				'label'       => __( 'Habilitar checkout Granito', 'woocommerce-granito' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( "When enabled opens a Granito modal window to receive the customer's credit card information.", 'woocommerce-granito' ),
			),
			'register_refused_order' => array(
				'title'       => __( 'Registrar pedido recusado', 'woocommerce-granito' ),
				'type'        => 'checkbox',
				'label'       => __( 'Habilitar registro de pedido recusado', 'woocommerce-granito' ),
				'default'     => 'no',
				'desc_tip'    => true,
				'description' => __( 'Registrar pedido para transações recusadas when Granito Checkout is enabled' ),
			),
			'installments' => array(
				'title'       => __( 'Parcelas', 'woocommerce-granito' ),
				'type'        => 'title',
				'description' => '',
			),
			'max_installment' => array(
				'title'       => __( 'Número de parcelas', 'woocommerce-granito' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'default'     => '12',
				'description' => __( 'Maximum number of installments possible with payments by credit card.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'options'     => array(
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
				),
			),
			'smallest_installment' => array(
				'title'       => __( 'Menor parcela', 'woocommerce-granito' ),
				'type'        => 'text',
				'description' => __( 'Please enter with the value of smallest installment, Note: it not can be less than 5.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'default'     => '5',
			),
			'interest_rate' => array(
				'title'       => __( 'Taxa de juros', 'woocommerce-granito' ),
				'type'        => 'text',
				'description' => __( 'Please enter with the interest rate amount. Note: use 0 to not charge interest.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'default'     => '0',
			),
			'free_installments' => array(
				'title'       => __( 'Parcelas sem juros', 'woocommerce-granito' ),
				'type'        => 'select',
				'class'       => 'wc-enhanced-select',
				'default'     => '1',
				'description' => __( 'Number of installments with interest free.', 'woocommerce-granito' ),
				'desc_tip'    => true,
				'options'     => array(
					'0'  => _x( 'None', 'no free installments', 'woocommerce-granito' ),
					'1'  => '1',
					'2'  => '2',
					'3'  => '3',
					'4'  => '4',
					'5'  => '5',
					'6'  => '6',
					'7'  => '7',
					'8'  => '8',
					'9'  => '9',
					'10' => '10',
					'11' => '11',
					'12' => '12',
				),
			),
			'testing' => array(
				'title'       => __( 'Gateway testes', 'woocommerce-granito' ),
				'type'        => 'title',
				'description' => '',
			),
			'debug' => array(
				'title'       => __( 'Debug Log (modo desenvolvedor)', 'woocommerce-granito' ),
				'type'        => 'checkbox',
				'label'       => __( 'Habilitar LOG', 'woocommerce-granito' ),
				'default'     => 'no',
				'description' => sprintf( __( 'Registre eventos Granito, como solicitações de API. Você pode verificar o log in %s', 'woocommerce-granito' ), '<a href="' . esc_url( admin_url( 'admin.php?page=wc-status&tab=logs&log_file=' . esc_attr( $this->id ) . '-' . sanitize_file_name( wp_hash( $this->id ) ) . '.log' ) ) . '">' . __( 'System Status &gt; Logs', 'woocommerce-granito' ) . '</a>' ),
			),
		);
	}

	/**
	 * Checkout scripts.
	 */
	public function checkout_scripts() {
		if ( is_checkout() ) {
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			if ( 'yes' === $this->checkout ) {
				$customer = array();

				wp_enqueue_script( 'granito-checkout-library', $this->api->get_checkout_js_url(), array( 'jquery' ), null );
				wp_enqueue_script( 'granito-checkout', plugins_url( 'assets/js/checkout' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'jquery-blockui', 'granito-checkout-library' ), WC_Granito::VERSION, true );

				if ( is_checkout_pay_page() ) {
					$customer = $this->api->get_customer_data_from_checkout_pay_page();
				}

				wp_localize_script(
					'granito-checkout',
					'wcgranitoParams',
					array(
						'encryptionKey'          => $this->encryption_key,
						'interestRate'           => $this->api->get_interest_rate(),
						'freeInstallments'       => $this->free_installments,
						'postbackUrl'            => WC()->api_request_url( get_class( $this ) ),
						'customerFields'         => $customer,
						'checkoutPayPage'        => ! empty( $customer ),
						'uiColor'                => apply_filters( 'WC_Granito_checkout_ui_color', '#1a6ee1' ),
						'register_refused_order' => $this->register_refused_order,
					)
				);
			} else {
				wp_enqueue_script( 'wc-credit-card-form' );
				wp_enqueue_script( 'granito-library', $this->api->get_js_url(), array( 'jquery' ), null );
				wp_enqueue_script( 'granito-credit-card', plugins_url( 'assets/js/credit-card' . $suffix . '.js', plugin_dir_path( __FILE__ ) ), array( 'jquery', 'jquery-blockui', 'granito-library' ), WC_Granito::VERSION, true );

				wp_localize_script(
					'granito-credit-card',
					'wcgranitoParams',
					array(
						'encryptionKey' => $this->encryption_key,
					)
				);
			}
		}
	}

	/**
	 * Payment fields.
	 */
	public function payment_fields() {
		if ( $description = $this->get_description() ) {
			echo wp_kses_post( wpautop( wptexturize( $description ) ) );
		}

		$cart_total = $this->get_order_total();

		if ( 'no' === $this->checkout ) {
			$installments = $this->api->get_installments( $cart_total );

			wc_get_template(
				'credit-card/payment-form.php',
				array(
					'cart_total'           => $cart_total,
					'max_installment'      => $this->max_installment,
					'smallest_installment' => $this->api->get_smallest_installment(),
					'installments'         => $installments,
				),
				'woocommerce/granito/',
				WC_Granito::get_templates_path()
			);
		} else {
			echo '<div id="granito-checkout-params" ';
			echo 'data-total="' . esc_attr( $cart_total * 100 ) . '" ';
			echo 'data-max_installment="' . esc_attr( apply_filters( 'WC_Granito_checkout_credit_card_max_installments', $this->api->get_max_installment( $cart_total ) ) ) . '"';
			echo '></div>';
		}
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

		if ( isset( $data['installments'] ) && in_array( $order->get_status(), array( 'processing', 'on-hold' ), true ) ) {
			wc_get_template(
				'credit-card/payment-instructions.php',
				array(
					'card_brand'   => $data['card_brand'],
					'installments' => $data['installments'],
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

		if ( isset( $data['installments'] ) ) {
			$email_type = $plain_text ? 'plain' : 'html';

			wc_get_template(
				'credit-card/emails/' . $email_type . '-instructions.php',
				array(
					'card_brand'   => $data['card_brand'],
					'installments' => $data['installments'],
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

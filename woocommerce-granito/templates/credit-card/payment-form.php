<?php
/**
 * Credit Card - Checkout form.
 *
 * @author  Douglas Morais
 * @package WooCommerce_Granito/Templates
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<fieldset id="granito-credit-cart-form">
	<p class="form-row form-row-first">
		<label for="granito-card-holder-name"><?php esc_html_e( 'Nome:', 'woocommerce-granito' ); ?><span class="required">*</span></label>
		<input id="granito-card-holder-name" class="input-text" type="text" autocomplete="off" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="granito-card-number"><?php esc_html_e( 'Número do Cartão', 'woocommerce-granito' ); ?> <span class="required">*</span></label>
		<input id="granito-card-number" class="input-text wc-credit-card-form-card-number" type="text" maxlength="20" autocomplete="off" placeholder="&bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull; &bull;&bull;&bull;&bull;" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<div class="clear"></div>
	<p class="form-row form-row-first">
		<label for="granito-card-expiry"><?php esc_html_e( 'Vencimento (MM/AA)', 'woocommerce-granito' ); ?> <span class="required">*</span></label>
		<input id="granito-card-expiry" class="input-text wc-credit-card-form-card-expiry" type="text" autocomplete="off" placeholder="<?php esc_html_e( 'MM / AA', 'woocommerce-granito' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<p class="form-row form-row-last">
		<label for="granito-card-cvc"><?php esc_html_e( 'CVC', 'woocommerce-granito' ); ?> <span class="required">*</span></label>
		<input id="granito-card-cvc" class="input-text wc-credit-card-form-card-cvc" type="text" autocomplete="off" placeholder="<?php esc_html_e( 'CVC', 'woocommerce-granito' ); ?>" style="font-size: 1.5em; padding: 8px;" />
	</p>
	<div class="clear"></div>
	<?php if ( apply_filters( 'WC_Granito_allow_credit_card_installments', 1 < $max_installment ) ) : ?>
		<p class="form-row form-row-wide">
			<label for="granito-card-installments"><?php esc_html_e( 'Parcelas', 'woocommerce-granito' ); ?> <span class="required">*</span></label>
			<select name="granito_installments" id="granito-installments" style="font-size: 1.5em; padding: 8px; width: 100%;">
				<option value="0"><?php printf( esc_html__( 'Selecione a quantidade de Parcelas*', 'woocommerce-granito' ) ); ?></option>
				<?php
				foreach ( $installments as $number => $installment ) :
					if ( 1 !== $number && $smallest_installment > $installment['installment_amount'] ) {
						break;
					}

					$interest           = ( ( $cart_total * 100 ) < $installment['amount'] ) ? sprintf( __( '(total of %s)', 'woocommerce-granito' ), strip_tags( wc_price( $installment['amount'] / 100 ) ) ) : __( '(interest-free)', 'woocommerce-granito' );
					$installment_amount = strip_tags( wc_price( $installment['installment_amount'] / 100 ) );
					?>
				<option value="<?php echo absint( $installment['installment'] ); ?>"><?php printf( esc_html__( '%1$dx of %2$s %3$s', 'woocommerce-granito' ), absint( $installment['installment'] ), esc_html( $installment_amount ), esc_html( $interest ) ); ?></option>
				<?php endforeach; ?>
			</select>
		</p>
	<?php endif; ?>
</fieldset>

<?php
$customer  = affwp_get_customer( absint( $_GET['customer_id'] ) );
?>
<div class="wrap">

	<h2><?php _e( 'Edit Customer', 'affiliate-wp' ); ?></h2>

	<form method="post" id="affwp_edit_customer">

		<?php
		/**
		 * Fires at the top of the edit-customer admin screen.
		 *
		 * @param \AffWP\customer $customer The customer object.
		 */
		do_action( 'affwp_edit_customer_top', $customer );
		?>

		<table class="form-table">


			<tr class="form-row form-required">

				<th scope="row">
					<label for="customer_id"><?php _e( 'Customer ID', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input class="small-text" type="text" name="customer_id" id="customer_id" value="<?php echo esc_attr( $customer->ID ); ?>" disabled="disabled"/>
					<p class="description"><?php _e( 'The customer ID. This cannot be changed.', 'affiliate-wp' ); ?></p>
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="affiliate"><?php _e( 'Affiliate', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					Show all associated affiliates here
				</td>

			</tr>

			<tr class="form-row form-required">

				<th scope="row">
					<label for="date"><?php _e( 'Date Created', 'affiliate-wp' ); ?></label>
				</th>

				<td>
					<input type="text" name="date" id="date" value="<?php echo esc_attr( $customer->date_i18n( 'datetime' ) ); ?>" disabled="disabled" />
				</td>

			</tr>

		</table>

		<?php
		/**
		 * Fires at the bottom of the edit-customer admin screen (inside the form element).
		 *
		 * @param \AffWP\customer $customer The customer object.
		 */
		do_action( 'affwp_edit_customer_bottom', $customer );
		?>

		<?php echo wp_nonce_field( 'affwp_edit_customer_nonce', 'affwp_edit_customer_nonce' ); ?>
		<input type="hidden" name="customer_id" value="<?php echo absint( $customer->customer_id ); ?>" />
		<input type="hidden" name="affwp_action" value="process_update_customer" />

		<?php submit_button( __( 'Update Customer', 'affiliate-wp' ) ); ?>

	</form>

</div>

<?php
global $affwp_opt_in_redirect;
affiliate_wp()->integrations->opt_in->print_errors();

if ( isset( $_GET['affwp-notice'] ) && 'opted-in' == $_GET['affwp-notice'] ) : ?>
	<p class="affwp-notice"><?php _e( 'You have subscribed successfully.', 'affiliate-wp' ); ?></p>
<?php
	return;
endif ?>

<form id="affwp-opt-in-form" class="affwp-form" action="" method="post">
	<?php do_action( 'affwp_affiliate_opt_in_form_top' ); ?>

	<fieldset>
		<?php do_action( 'affwp_opt_in_fields_before' ); ?>

		<p>
			<label for="affwp-opt-in-name"><?php _e( 'First Name', 'affiliate-wp' ); ?></label>
			<input id="affwp-opt-in-name" class="required" type="text" name="affwp_first_name" title="<?php esc_attr_e( 'First Name', 'affiliate-wp' ); ?>" />
		</p>

		<p>
			<label for="affwp-opt-in-name"><?php _e( 'Last Name', 'affiliate-wp' ); ?></label>
			<input id="affwp-opt-in-name" class="required" type="text" name="affwp_last_name" title="<?php esc_attr_e( 'Last Name', 'affiliate-wp' ); ?>" />
		</p>

		<p>
			<label for="affwp-opt-in-email"><?php _e( 'Email Address', 'affiliate-wp' ); ?></label>
			<input id="affwp-opt-in-email" class="required" type="text" name="affwp_email" title="<?php esc_attr_e( 'Email Address', 'affiliate-wp' ); ?>" />
		</p>

		<p>
			<input type="hidden" name="affwp_redirect" value="<?php echo esc_url( $affwp_opt_in_redirect ); ?>"/>
			<input type="hidden" name="affwp_opt_in_nonce" value="<?php echo wp_create_nonce( 'affwp-opt-in-nonce' ); ?>" />
			<input type="hidden" name="affwp_action" value="opt_in" />
			<input type="submit" class="button" value="<?php esc_attr_e( 'Subscribe', 'affiliate-wp' ); ?>" />
		</p>

		<?php do_action( 'affwp_opt_in_fields_after' ); ?>
	</fieldset>

	<?php do_action( 'affwp_affiliate_opt_in_form_bottom' ); ?>
</form>

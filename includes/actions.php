<?php

/**
 * Hooks AffiliateWP actions, when present in the $_REQUEST superglobal. Every affwp_action
 * present in $_REQUEST is called using WordPress's do_action function. These
 * functions are called on init.
 *
 * @since 1.0
 * @return void
*/
function affwp_do_actions() {
	if ( isset( $_REQUEST['affwp_action'] ) ) {
		do_action( 'affwp_' . $_REQUEST['affwp_action'], $_REQUEST );
	}
}
add_action( 'init', 'affwp_do_actions' );

// Process affiliate notification settings
add_action( 'affwp_update_profile_settings', 'affwp_update_profile_settings' );

/**
 * Removes single-use query args derived from executed actions in the admin.
 *
 * @since 1.8.6
 *
 * @param array $query_args Removable query arguments.
 * @return array Filtered list of removable query arguments.
 */
function affwp_remove_query_args( $query_args ) {
	$to_remove = array(
		// General
		'affwp_notice',
		'action2',
		'settings-updated',

		// Affiliates
		'affiliate_activated',

		// Core
		'_wpnonce'
	);

	$edit_actions = array(
		'edit_affiliate',
		'edit_referral',
		'edit_creative',
	);

	// Only remove 'action' if not editing an affiliate, referral, or creative.
	if ( isset( $_GET['action'] ) && ! in_array( sanitize_key( $_GET['action'] ), $edit_actions, true ) ) {
		$to_remove[] = 'action';
	}

	return array_merge( $query_args, $to_remove );
}
add_action( 'removable_query_args', 'affwp_remove_query_args' );

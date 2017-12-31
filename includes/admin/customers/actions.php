<?php

/**
 * Process the add customer request
 *
 * @since 2.2
 * @return void|false
 */
function affwp_process_add_customer( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_customers' ) ) {
		wp_die( __( 'You do not have permission to manage customers', 'affiliate-wp' ), __( 'Error', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_add_customer_nonce'], 'affwp_add_customer_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affwp_add_customer( $data ) ) {
		wp_safe_redirect( affwp_admin_url( 'customers', array( 'affwp_notice' => 'customer_added' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'customers', array( 'affwp_notice' => 'customer_add_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_add_customer', 'affwp_process_add_customer' );

/**
 * Process the update customer request
 *
 * @since 2.2
 * @return void
 */
function affwp_process_update_customer( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_customers' ) ) {
		wp_die( __( 'You do not have permission to manage customers', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['affwp_edit_customer_nonce'], 'affwp_edit_customer_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affiliate_wp()->customers->update( $data['customer_id'], $data ) ) {
		wp_safe_redirect( affwp_admin_url( 'customers', array( 'affwp_notice' => 'customer_updated' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'customers', array( 'affwp_notice' => 'customer_update_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_process_update_customer', 'affwp_process_update_customer' );

/**
 * Process the delete customer request
 *
 * @since 2.2
 * @return void
 */
function affwp_process_delete_customer( $data ) {

	if ( ! is_admin() ) {
		return false;
	}

	if ( ! current_user_can( 'manage_customers' ) ) {
		wp_die( __( 'You do not have permission to manage customers', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( ! wp_verify_nonce( $data['_wpnonce'], 'affwp_delete_customer_nonce' ) ) {
		wp_die( __( 'Security check failed', 'affiliate-wp' ), array( 'response' => 403 ) );
	}

	if ( affwp_delete_customer( $data['customer_id'] ) ) {
		wp_safe_redirect( affwp_admin_url( 'customers', array( 'affwp_notice' => 'customer_deleted' ) ) );
		exit;
	} else {
		wp_safe_redirect( affwp_admin_url( 'customers', array( 'affwp_notice' => 'customer_delete_failed' ) ) );
		exit;
	}

}
add_action( 'affwp_process_delete_customer', 'affwp_process_delete_customer' );
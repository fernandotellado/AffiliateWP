<?php

class Affiliate_WP_Privacy_Tools {

	/**
	 * Get things started
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_filter( 'wp_privacy_personal_data_exporters', array( $this, 'register_privacy_exporters' ) );	
	}

	/**
	 * Registers our privacy exporters
	 *
	 * @since 2.2
	 * @param array $exporters Existing exporters
	 * @return array $exporters
	 */
	public function register_privacy_exporters( $exporters ) {
	
		$exporters[] = array(
			'exporter_friendly_name' => __( 'Affiliate Record', 'affiliate-wp' ),
			'callback'               => array( $this, 'affiliate_record_exporter' ),
		);

		return $exporters;

	}

	/**
	 * Retrieves the affiliate record for the Privacy Data Exporter
	 *
	 * @since 2.2
	 * @param string $email_address
	 * @param int    $page
	 *
	 * @return array
	 */
	public  function affiliate_record_exporter( $email_address = '', $page = 1 ) {

		$export_data = array();
		$user        = get_user_by( 'email', $email_address );

		if( ! $user ) {
			return $export_data;
		}

		$affiliate = affwp_get_affiliate( $user->user_login );

		if ( ! empty( $affiliate->affiliate_id ) ) {

			$export_data = array(
				'group_id'    => 'affwp-affiliate-record',
				'group_label' => __( 'Affiliate Record', 'affiliate-wp' ),
				'item_id'     => "affwp-affiliate-record-{$affiliate->affiliate_id}",
				'data'        => array(
					array(
						'name'  => __( 'Customer ID', 'affiliate-wp' ),
						'value' => $affiliate->affiliate_id
					),
					array(
						'name'  => __( 'Primary Email', 'affiliate-wp' ),
						'value' => $user->user_email
					),
					array(
						'name'  => __( 'Payment Email', 'affiliate-wp' ),
						'value' => $affiliate->payment_email
					),
					array(
						'name'  => __( 'Name', 'affiliate-wp' ),
						'value' => affwp_get_affiliate_name( $affiliate->affiliate_id )
					),
					array(
						'name'  => __( 'Date Created', 'affiliate-wp' ),
						'value' => $affiliate->date
					),
				)
			);
		}

		return array( 'data' => array( $export_data ), 'done' => true );
	}

}

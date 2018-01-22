<?php
namespace AffWP\Integrations\Opt_In;

use AffWP\Integrations\Opt_In;

/**
 * ActiveCampaign opt-in platform integration.
 *
 * @since 2.2
 * @abstract
 */
class ActiveCampaign extends Opt_In\Platform {

	public function init() {

		$this->platform_id = 'activecampaign';
		$this->api_key     = affiliate_wp()->settings->get( 'activecampaign_api_key' );
		$this->list_id     = affiliate_wp()->settings->get( 'activecampaign_list_id' );
		$this->api_url     = 'https://activecampaign.com/admin/api.php?api_action=contact_add&api_key=' . $this->api_key;
	}

	public function subscribe_contact() {

		$body = array(
			'api_output'                     => 'json',
			'email'                          => $this->contact['email'],
		    'first_name'                     => $this->contact['first_name'],
		    'last_name'                      => $this->contact['last_name'],
		    'ip4'                            => affiliate_wp()->tracking->get_ip(),
		    'status[' . $this->list_id . ']' => 1,
		);

		return $this->call_api( $this->api_url, $body );

	}

	public function settings( $settings ) {

		$settings['activecampaign_api_key'] = array(
			'name' => __( 'ActiveCampaign API Key', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter your ActiveCampaign API key.', 'affiliate-wp' ),
		);

		$settings['activecampaign_list_id'] = array(
			'name' => __( 'ActiveCampaign Form ID', 'affiliate-wp' ),
			'type' => 'text',
			'desc' => __( 'Enter the ID of the list you wish to subscribe contacts to.', 'affiliate-wp' ),
		);

		return $settings;
	}

}	
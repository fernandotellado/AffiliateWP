<?php
namespace AffWP\Tests\Factory;

class Customer extends \WP_UnitTest_Factory_For_Thing {

	function __construct( $factory = null ) {
		parent::__construct( $factory );
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param array $args
	 * @param null  $generation_definitions
	 *
	 * @return \AffWP\Customer|false
	 */
	function create_and_get( $args = array(), $generation_definitions = null ) {
		return parent::create_and_get( $args, $generation_definitions );
	}

	function create_object( $args ) {
		$user = new \WP_UnitTest_Factory_For_User();

		// Only create the associated user if one wasn't supplied.
		if ( empty( $args['user_id'] ) ) {
			$args['user_id'] = $user->create();
		}

		return affiliate_wp()->customers->add( $args );
	}

	function update_object( $customer_id, $fields ) {
		return affiliate_wp()->customers->update( $customer_id, $fields, '', 'customer' );
	}

	public function delete( $customer ) {
		affwp_delete_customer( $customer );
	}

	public function delete_many( $customers ) {
		foreach ( $customers as $customer ) {
			$this->delete( $customer );
		}
	}

	/**
	 * Stub out copy of parent method for IDE type hinting purposes.
	 *
	 * @param $customer_id
	 *
	 * @return \AffWP\customer|false
	 */
	function get_object_by_id( $customer_id ) {
		return affwp_get_customer( $customer_id );
	}
}

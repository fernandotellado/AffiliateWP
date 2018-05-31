<?php
namespace AffWP\Customer\Object;

use AffWP\Tests\UnitTestCase;
use AffWP\Customer;

/**
 * Tests for AffWP\Customer
 *
 * @covers AffWP\Customer
 * @covers AffWP\Base_Object
 *
 * @group Customers
 * @group objects
 */
class Tests extends UnitTestCase {

	/**
	 * @covers AffWP\Base_Object::get_instance()
	 */
	public function test_get_instance_with_invalid_customer_id_should_return_false() {
		$this->assertFalse( Customer::get_instance( 0 ) );
	}

	/**
	 * @covers AffWP\Base_Object::get_instance()
	 */
	public function test_get_instance_with_customer_id_should_return_customer_object() {
		$customer_id = affiliate_wp()->customers->add( array(
			'email' => 'customer@affiliatewp.dev'
		) );

		$this->assertInstanceOf( 'AffWP\Customer', Customer::get_instance( $customer_id ) );
	}
}

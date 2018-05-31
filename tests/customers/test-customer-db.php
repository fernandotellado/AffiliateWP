<?php
namespace AffWP\Customer\Database;

use AffWP\Tests\UnitTestCase;

/**
 * Tests for Affiliate_WP_DB_Customers class
 *
 * @coversDefaultClass Affiliate_WP_DB_Customers
 *
 * @group database
 * @group customers
 */
class Tests extends UnitTestCase {

	/**
	 * Users fixture.
	 *
	 * @access protected
	 * @var array
	 * @static
	 */
	public static $users = array();

	/**
	 * Customers fixture.
	 *
	 * @access protected
	 * @var array
	 * @static
	 */
	public static $customers = array();

	/**
	 * Set up fixtures once.
	 */
	public static function wpSetUpBeforeClass() {
		update_option( 'gmt_offset', -5 );
		affiliate_wp()->utils->_refresh_wp_offset();

		self::$users = parent::affwp()->user->create_many( 4 );

		foreach ( self::$users as $user_id ) {
			$user = get_user_by( 'id', $user_id );

			self::$customers[] = parent::affwp()->customer->create( array(
				'user_id' => $user_id,
				'email'   => $user->user_email
			) );
		}
	}

	/**
	 * @covers ::$cache_group
	 */
	public function test_cache_group_should_be_customers() {
		$this->assertSame( 'customers', affiliate_wp()->customers->cache_group );
	}

	/**
	 * @covers ::$query_object_type
	 */
	public function test_query_object_type_should_be_AffWP_Customer() {
		$this->assertSame( 'AffWP\Customer', affiliate_wp()->customers->query_object_type );
	}

	/**
	 * @covers ::$primary_key
	 */
	public function test_primary_key_should_be_customer_id() {
		$this->assertSame( 'customer_id', affiliate_wp()->customers->primary_key );
	}

	/**
	 * @covers ::$version
	 */
	public function test_db_version_is_10() {
		$this->assertSame( '1.0', affiliate_wp()->customers->version );
	}

	/**
	 * @covers ::get_object()
	 */
	public function test_get_object_should_return_valid_object_when_passed_a_valid_customer_id() {
		$object = affiliate_wp()->customers->get_object( self::$customers[0] );
		$this->assertEquals( 'AffWP\Customer', get_class( $object ) );
	}

	/**
	 * @covers ::get_object()
	 */
	public function test_get_object_should_return_false_when_passed_an_invalid_customer_id() {
		$this->assertFalse( affiliate_wp()->customers->get_object( 0 ) );
	}

	/**
	 * @covers ::get_object()
	 */
	public function test_get_object_should_return_valid_object_when_passed_a_valid_customer_object() {
		$object = affiliate_wp()->customers->get_object( affwp_get_customer( self::$customers[0] ) );

		$this->assertSame( 'AffWP\Customer', get_class( $object ) );
	}

	/**
	 * @covers ::get_columns()
	 */
	public function test_get_columns_should_return_all_columns() {
		$columns = affiliate_wp()->customers->get_columns();

		$expected = array(
			'customer_id'  => '%d',
			'user_id'      => '%d',
			'email'        => '%s',
			'first_name'   => '%s',
			'last_name'    => '%s',
			'ip'           => '%s',
			'date_created' => '%s'
		);

		$this->assertEqualSetsWithIndex( $expected, $columns );
	}

	/**
	 * @covers ::get_column_defaults()
	 */
	public function test_get_column_defaults_should_return_column_defaults() {
		$expected = array(
			'date_created' => gmdate( 'Y-m-d H:i:s' ),
		);

		$this->assertEqualSetsWithIndex( $expected, affiliate_wp()->customers->get_column_defaults() );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_should_return_array_of_Customer_objects_if_not_count_query() {
		$results = affiliate_wp()->customers->get_customers();

		$this->assertContainsOnlyType( 'AffWP\Customer', $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_should_return_integer_if_count_query() {
		$results = affiliate_wp()->customers->get_customers( array(), $count = true );

		$this->assertTrue( is_numeric( $results ) );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_number_should_return_that_number_if_available() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => 'ids',
			'number' => 2
		) );

		$this->assertCount( 2, $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_number_all_should_return_all() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => 'ids',
			'number' => -1
		) );

		$this->assertCount( 4, $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_offset_should_offset_that_number() {
		$results = affiliate_wp()->customers->get_customers( array(
			'offset' => 2,
			'fields' => 'ids',
			'order'  => 'ASC',
		) );

		$customers = array( self::$customers[2], self::$customers[3] );

		$this->assertEqualSets( $customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_exclude_with_single_customer_id_should_exclude_that_customer() {
		$results = affiliate_wp()->customers->get_customers( array(
			'exclude' => self::$customers[0],
			'fields'  => 'ids',
		) );

		$this->assertFalse( in_array( self::$customers[0], $results, true ) );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_exclude_with_multiple_customer_ids_should_exclude_those_customers() {
		$results = affiliate_wp()->customers->get_customers( array(
			'exclude' => array( self::$customers[0], self::$customers[1] ),
			'fields'  => 'ids',
		) );

		$this->assertEqualSets( array( self::$customers[2], self::$customers[3] ), $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_default_orderby_should_order_by_customer_id() {
		$results = affiliate_wp()->customers->get_customers( array(
			'order'  => 'ASC',
			'fields' => 'ids',
		) );

		// Order should be as created, 0, 1, 2, 3.
		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_orderby_date_should_order_by_registered_date() {
		$customer1 = $this->factory->customer->create( array(
			'date_created' => ( time() - WEEK_IN_SECONDS ),
			'email'        => 'customer1@affiliatewp.dev'
		) );

		$customer2 = $this->factory->customer->create( array(
			'date_created' => ( time() + WEEK_IN_SECONDS ),
			'email'        => 'customer2@affiliatewp.dev'
		) );

		$results = affiliate_wp()->customers->get_customers( array(
			'orderby' => 'date', // Default 'order' is DESC
			'fields'  => 'ids',
		) );

		$new_order = array(
			self::$customers[3],
			self::$customers[2],
			self::$customers[1],
			self::$customers[0],
			$customer1,
			$customer2
		);

		// Order should be newest to oldest: 1, 2
		$this->assertEqualSets( $new_order, $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_orderby_invalid_column_should_default_to_order_by_primary_key() {
		$results = affiliate_wp()->customers->get_customers( array(
			'orderby' => rand_str( 15 ),
			'fields'  => 'ids',
		) );

		// With invalid orderby, should return ordered by customer_id, descending.
		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_order_ASC_should_order_ascending() {
		$results = affiliate_wp()->customers->get_customers( array(
			'order'  => 'ASC', // default 'DESC'
			'fields' => 'ids',
		) );

		$this->assertEqualSets( array_reverse( self::$customers ), $results );
	}

	/**
	 * @covers ::get_customers()
	 */
	public function test_get_customers_order_DESC_should_order_descending() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => 'ids',
		) );

		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_fields_ids_should_return_an_array_of_ids_only() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => 'ids'
		) );

		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_fields_ids_should_return_an_array_of_integer_ids() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => 'ids'
		) );

		$this->assertContainsOnlyType( 'integer', $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_with_no_fields_should_return_an_array_of_customer_objects() {
		$results = affiliate_wp()->customers->get_customers();

		$this->assertContainsOnlyType( 'AffWP\Customer', $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_with_multiple_valid_fields_should_return_an_array_of_stdClass_objects() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => array( 'customer_id', 'email' )
		) );

		$this->assertContainsOnlyType( 'stdClass', $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_fields_valid_field_should_return_array_of_that_field_only() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => 'customer_id'
		) );

		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_invalid_fields_arg_should_return_regular_customer_object_results() {
		$customers = array_map( 'affwp_get_customer', self::$customers );

		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => 'foo'
		) );

		$this->assertEqualSets( $customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_fields_array_with_only_one_valid_field_should_return_an_array_of_those_values() {
		$result = affiliate_wp()->customers->get_customers( array(
			'fields' => array( 'user_id', 'foo' )
		) );

		$this->assertEqualSets( self::$users, $result );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_fields_array_with_multiple_valid_fields_should_return_objects_with_those_fields_only() {
		$fields = array( 'user_id', 'date_created' );

		$result = affiliate_wp()->customers->get_customers( array(
			'fields' => $fields
		) );

		$object_vars = get_object_vars( $result[0] );

		$this->assertEqualSets( $fields, array_keys( $object_vars ) );
	}

	/**
	 * @covers ::get_customers()
	 * @group database-fields
	 */
	public function test_get_customers_fields_array_with_multiple_valid_fields_should_return_array_of_stdClass_objects() {
		$results = affiliate_wp()->customers->get_customers( array(
			'fields' => array( 'user_id', 'email' )
		) );

		$this->assertContainsOnlyType( 'stdClass', $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group dates
	 */
	public function test_get_customers_with_date_no_start_end_should_retrieve_customers_for_today() {
		$results = affiliate_wp()->customers->get_customers( array(
			'date_created' => 'today',
			'fields'       => 'ids',
		) );

		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group dates
	 */
	public function test_get_customers_with_today_customers_yesterday_date_no_start_end_should_return_empty() {
		$results = affiliate_wp()->customers->get_customers( array(
			'date_created' => 'yesterday',
			'fields'       => 'ids',
		) );

		$this->assertEqualSets( array(), $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group dates
	 */
	public function test_get_customers_date_start_should_only_retrieve_customers_created_after_that_date() {
		for ( $i = 1; $i <= 3; $i++ ) {
			$user_id = $this->factory->user->create();

			$user    = get_user_by( 'id', $user_id );

			$customers[] = affiliate_wp()->customers->add( array(
				'user_id'      => $user_id,
				'date_created' => '2016-01-01',
				'email'        => $user->user_email
			) );
		}

		$results = affiliate_wp()->customers->get_customers( array(
			'fields'       => 'ids',
			'date_created' => array(
				'start' => '2016-01-02'
			),
		) );

		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::get_customers()
	 * @group dates
	 */
	public function test_get_customers_date_end_should_only_retrieve_customers_created_before_that_date() {
		$customer = $this->factory->customer->create( array(
			'date_created' => '+1 day',
			'email'        => 'customer@affiliatewp.dev'
		) );

		$results = affiliate_wp()->customers->get_customers( array(
			'fields'       => 'ids',
			'date_created' => array(
				'end' => 'today'
			),
		) );

		// Should catch all but the one just created +1 day.
		$this->assertEqualSets( self::$customers, $results );
	}

	/**
	 * @covers ::count()
	 */
	public function test_count_should_count_based_on_query_args() {
		$this->assertSame( 4, affiliate_wp()->customers->count() );
	}

	/**
	 * @covers ::add()
	 */
	public function test_add_successful_should_return_id_of_the_new_customer() {
		$customer = affiliate_wp()->customers->add( array(
			'user_id' => $this->factory->user->create( array(
				'user_email' => 'customer@affiliatewp.dev'
			) ),
			'email'   => 'customer@affiliatewp.dev'
		) );

		$results = affiliate_wp()->customers->get_customers( array(
			'fields'  => 'ids',
			'number'  => 1,
			'orderby' => 'customer_id'
		) );

		$this->assertEquals( $customer, $results[0] );
	}

	/**
	 * @covers ::add()
	 * @group dates
	 */
	public function test_add_without_date_created_should_use_current_date_and_time() {
		$customer_id = affiliate_wp()->customers->add( array(
			'user_id' => $this->factory->user->create( array(
				'user_email' => 'customer@affiliatewp.dev'
			) ),
			'email' => 'customer@affiliatewp.dev'
		) );

		$customer = affwp_get_customer( $customer_id );

		// Explicitly dropping seconds from the date strings for comparison.
		$expected = gmdate( 'Y-m-d H:i' );
		$actual   = gmdate( 'Y-m-d H:i', strtotime( $customer->date() ) );

		$this->assertSame( $expected, $actual );
	}

	/**
	 * @covers ::add()
	 * @group dates
	 */
	public function test_add_with_date_created_should_assume_local_time_and_remove_offset_on_add() {
		$customer_id = affiliate_wp()->customers->add( array(
			'user_id'      => $this->factory->user->create( array(
				'user_email' => 'customer@affiliatewp.dev'
			) ),
			'date_created' => '05/04/2017',
			'email'        => 'customer@affiliatewp.dev'
		) );

		$customer = affwp_get_customer( $customer_id );

		$expected_date = gmdate( 'Y-m-d H:i', strtotime( '05/04/2017' ) - affiliate_wp()->utils->wp_offset );
		$actual        = gmdate( 'Y-m-d H:i', strtotime( $customer->date() ) );

		$this->assertSame( $expected_date, $actual );
	}

	/**
	 * @covers ::add()
	 */
	public function test_add_should_return_false_if_email_is_empty() {
		$this->assertFalse( affiliate_wp()->customers->add( array(
			'user_id' => $this->factory->user->create(),
			'email'   => ''
		) ) );
	}

}

<?php
namespace AffWP\Utils\Referral_Types;

use AffWP\Tests\UnitTestCase;
use AffWP\Referral;


/**
 * Tests for AffWP\Utils\Referral_Types
 *
 * @covers AffWP\Utils\Referral_Types
 *
 * @group referrals
 * @group types
 */
class Tests extends UnitTestCase {

	/**
	 * Utilities object.
	 *
	 * @access protected
	 * @var    \Affiliate_WP_Utilities
	 */
	protected static $referrals_db;

	/**
	 * Test batch ID.
	 *
	 * @access protected
	 * @var    string
	 */
	protected static $type_id = 'ping';

	/**
	 * Set up fixtures once.
	 */
	public static function wpSetUpBeforeClass() {
		self::$referrals_db = affiliate_wp()->referrals;
	}

	/**
	 * Tear down run after each test.
	 */
	public function tearDown() {
		self::$referrals_db->types_registry->remove_type( self::$type_id );

		parent::tearDown();
	}

	/**
	 * @covers \AffWP\Utils\Referral_Types\Registry::register_process()
	 */
	public function test_register_type_with_label_args_should_register_the_type() {
		self::$referrals_db->types_registry->register_type( self::$type_id, array(
			'label' => 'Special',
		) );

		$this->assertNotEmpty( self::$referrals_db->types_registry->get( self::$type_id ) );
	}
}

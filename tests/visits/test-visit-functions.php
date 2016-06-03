<?php
/**
 * Tests for Visits functions in visit-functions.php.
 *
 * @group visits
 * @group functions
 */
class Visit_Functions_Tests extends WP_UnitTestCase {

	/**
	 * Test user ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_user_id = 0;

	/**
	 * Test affiliate ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_affiliate_id = 0;

	/**
	 * Test referral ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_referral_id = 0;

	/**
	 * Test visit ID.
	 *
	 * @access protected
	 * @var int
	 */
	protected $_visit_id = 0;

	/**
	 * Set up.
	 */
	public function setUp() {
		parent::setUp();

		$this->_user_id = self::factory()->user->create();

		$this->_affiliate_id = affiliate_wp()->affiliates->add( array(
			'user_id' => $this->_user_id
		) );

		$this->_visit_id = affiliate_wp()->visits->add( array(
			'affiliate_id' => $this->_affiliate_id
		) );

		$this->_referral_id = affiliate_wp()->referrals->add( array(
			'affiliate_id' => $this->_affiliate_id,
			'visit_id'     => $this->_visit_id
		) );
	}

	/**
	 * Tear down.
	 */
	public function tearDown() {
		affwp_delete_affiliate( $this->_affiliate_id );
		affwp_delete_referral( $this->_referral_id );
		affwp_delete_visit( $this->_visit_id );

		parent::tearDown();
	}

	/**
	 * @covers affwp_delete_visit()
	 */
	public function test_delete_visit_with_no_visit_should_return_false() {
		$this->assertFalse( affwp_delete_visit( null ) );
	}

	/**
	 * @covers affwp_delete_visit()
	 */
	public function test_delete_visit_with_invalid_visit_id_should_return_false() {
		$this->assertFalse( affwp_delete_visit( 0 ) );
	}

	/**
	 * @covers affwp_delete_visit()
	 */
	public function test_delete_visit_with_valid_visit_id_should_return_true() {
		$this->assertTrue( affwp_delete_visit( $this->_visit_id ) );
	}

	/**
	 * @covers affwp_delete_visit()
	 */
	public function test_delete_visit_with_invalid_visit_object_should_return_false() {
		$this->assertFalse( affwp_delete_visit( new stdClass() ) );
	}

	/**
	 * @covers affwp_delete_visit()
	 */
	public function test_delete_visit_with_valid_visit_object_should_return_true() {
		$visit = affiliate_wp()->visits->get( $this->_visit_id );

		$this->assertTrue( affwp_delete_visit( $visit ) );
	}

	/**
	 * @covers affwp_delete_visit()
	 */
	public function test_delete_visit_should_decrease_affiliate_visit_count() {
		// Inflate visit count.
		affwp_increase_affiliate_visit_count( $this->_affiliate_id );
		affwp_increase_affiliate_visit_count( $this->_affiliate_id );

		$old_count = affwp_get_affiliate_visit_count( $this->_affiliate_id );

		affwp_delete_visit( $this->_visit_id );

		$new_count = affwp_get_affiliate_visit_count( $this->_affiliate_id );

		$this->assertEquals( $old_count--, $new_count );
	}

	public function test_sanitize_visit_url() {
		$referral_var = affiliate_wp()->tracking->get_referral_var();

		$this->assertEquals( affwp_sanitize_visit_url( 'https://affiliatewp.com/' . $referral_var . '/pippin/query_var' ), 'https://affiliatewp.com/query_var' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://affiliatewp.com/sample-page/' . $referral_var . '/pippin/query_var/1' ), 'https://affiliatewp.com/sample-page/query_var/1' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://affiliatewp.com/sample-page/' . $referral_var . '/pippin/query_var/1/query_var2/2' ), 'https://affiliatewp.com/sample-page/query_var/1/query_var2/2' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://affiliatewp.com/' . $referral_var . '/pippin?query_var=1' ), 'https://affiliatewp.com?query_var=1' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://affiliatewp.com/sample-page/' . $referral_var . '/pippin?query_var=1' ), 'https://affiliatewp.com/sample-page?query_var=1' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://affiliatewp.com/sample-page/' . $referral_var . '/pippin?query_var=1&query_var2=2' ), 'https://affiliatewp.com/sample-page?query_var=1&query_var2=2' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://www.affiliatewp.com/' . $referral_var . '/pippin/query_var' ), 'https://www.affiliatewp.com/query_var' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://www.affiliatewp.com/sample-page/' . $referral_var . '/pippin/query_var/1' ), 'https://www.affiliatewp.com/sample-page/query_var/1' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://www.affiliatewp.com/sample-page/' . $referral_var . '/pippin/query_var/1/query_var2/2' ), 'https://www.affiliatewp.com/sample-page/query_var/1/query_var2/2' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://www.affiliatewp.com/' . $referral_var . '/pippin?query_var=1' ), 'https://www.affiliatewp.com?query_var=1' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://www.affiliatewp.com/sample-page/' . $referral_var . '/pippin?query_var=1' ), 'https://www.affiliatewp.com/sample-page?query_var=1' );
		$this->assertEquals( affwp_sanitize_visit_url( 'https://www.affiliatewp.com/sample-page/' . $referral_var . '/pippin?query_var=1&query_var2=2' ), 'https://www.affiliatewp.com/sample-page?query_var=1&query_var2=2' );
	}


}
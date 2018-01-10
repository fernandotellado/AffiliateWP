<?php
/**
 * Objects: Customer
 *
 * @package AffiliateWP
 * @category Core
 *
 * @since 2.2
 */

namespace AffWP;

/**
 * Implements an customer object.
 *
 * @since 2.2
 *
 * @see AffWP\Base_Object
 * @see affwp_get_affiliate()
 *
 * @property-read int      $ID   Alias for `$affiliate_id`.
 * @property      stdClass $user User object.
 * @property      array    $meta Meta array.
 * @property-read string   $date Alias for `$date_registered`.
 */
final class Customer extends Base_Object {

	/**
	 * Customer ID.
	 *
	 * @since 2.2
	 * @access public
	 * @var int
	 */
	public $customer_id = 0;

	/**
	 * Affiliate IDs associated with the customer.
	 *
	 * @since 2.2
	 * @access public
	 * @var array
	 */
	public $affiliate_ids = array();

	/**
	 * Affiliate user ID.
	 *
	 * @since 2.2
	 * @access public
	 * @var int
	 */
	public $user_id = 0;

	/**
	 * Affiliate first name.
	 *
	 * @since 2.2
	 * @access public
	 * @var string
	 */
	public $first_name;

	/**
	 * Customer last name.
	 *
	 * @since 2.2
	 * @access public
	 * @var string
	 */
	public $last_name;

	/**
	 * Customer email.
	 *
	 * @since 2.2
	 * @access public
	 * @var string
	 */
	public $email;

	/**
	 * Customer IP.
	 *
	 * @since 2.2
	 * @access public
	 * @var string
	 */
	public $ip;

	/**
	 * Customer creation date.
	 *
	 * @since 2.2
	 * @access public
	 * @var string
	 */
	public $date_created;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @since 2.2
	 * @access public
	 * @static
	 * @var string
	 *
	 * @see AffWP\Base_Object::get_cache_key()
	 */
	public static $cache_token = 'affwp_customers';

	/**
	 * Database group.
	 *
	 * Used in \AffWP\Base_Object for accessing the customers DB class methods.
	 *
	 * @since 2.2
	 * @access public
	 * @var string
	 */
	public static $db_group = 'customers';

	/**
	 * Object type.
	 *
	 * Used as the cache group and for accessing object DB classes in the parent.
	 *
	 * @since 2.2
	 * @access public
	 * @static
	 * @var string
	 */
	public static $object_type = 'customer';

	/**
	 * Retrieves the values of the given key.
	 *
	 * @since 2.2
	 * @access public
	 *
	 * @param string $key Key to retrieve the value for.
	 * @return mixed|\WP_User Value.
	 */
	public function __get( $key ) {

		if ( 'date' === $key ) {
			return $this->date_created;
		}

		return parent::__get( $key );
	}
}

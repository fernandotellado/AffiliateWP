<?php

final class AffWP_Visit extends AffWP_Object {

	/**
	 * Visit ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $visit_id = 0;

	/**
	 * Affiliate ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $affiliate_id = 0;

	/**
	 * Referral ID.
	 *
	 * @since 1.9
	 * @access public
	 * @var int
	 */
	public $referral_id = 0;

	/**
	 * Visit URL.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $url;

	/**
	 * Visit referrer.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $referrer;

	/**
	 * Referral campaign name.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $campaign;

	/**
	 * Visit IP address (IPv4).
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $ip;

	/**
	 * Date the visit was created.
	 *
	 * @since 1.9
	 * @access public
	 * @var string
	 */
	public $date;

	/**
	 * Token to use for generating cache keys.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $cache_token = 'affwp_visits';

	/**
	 * Object type.
	 *
	 * Used as the cache group and for accessing object DB classes in the parent.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 * @var string
	 */
	public static $object_type = 'visit';

	/**
	 * Sanitizes a visit object field.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param string $field        Object field.
	 * @param mixed  $value        Field value.
	 * @return mixed Sanitized field value.
	 */
	public static function sanitize_field( $field, $value ) {
		if ( in_array( $field, array( 'visit_id', 'affiliate_id', 'referral_id' ) ) ) {
			$value = (int) $value;
		}
		return $value;
	}

	/**
	 * Retrieves the object instance.
	 *
	 * @since 1.9
	 * @access public
	 * @static
	 *
	 * @param int $object Object ID.
	 * @return object|false Object instance or false.
	 */
	public static function get_instance( $object_id ) {
		self::$object_group = affiliate_wp()->visits->cache_group;

		return parent::get_instance( $object_id );
	}
}
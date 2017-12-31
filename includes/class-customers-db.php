<?php

/**
 * Class Affiliate_WP_Customers_DB
 *
 * @property-read \AffWP\Affiliate\REST\v1\Endpoints $REST Affiliates REST endpoints.
 */
class Affiliate_WP_Customers_DB extends Affiliate_WP_DB {

	/**
	 * Cache group for queries.
	 *
	 * @internal DO NOT change. This is used externally both as a cache group and shortcut
	 *           for accessing db class instances via affiliate_wp()->{$cache_group}->*.
	 *
	 * @access public
	 * @since  2.2
	 * @var    string
	 */
	public $cache_group = 'customers';

	/**
	 * Object type to query for.
	 *
	 * @access public
	 * @since  2.2
	 * @var    string
	 */
	public $query_object_type = 'AffWP\Customer';

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  2.2
	*/
	public function __construct() {
		global $wpdb, $wp_version;

		if( defined( 'AFFILIATE_WP_NETWORK_WIDE' ) && AFFILIATE_WP_NETWORK_WIDE ) {
			// Allows a single affiliate table for the whole network
			$this->table_name  = 'affiliate_wp_customers';
		} else {
			$this->table_name  = $wpdb->prefix . 'affiliate_wp_customers';
		}
		$this->primary_key = 'customemr_id';
		$this->version     = '2.2';

		// REST endpoints.
		if ( version_compare( $wp_version, '4.4', '>=' ) ) {
			$this->REST = new \AffWP\Affiliate\REST\v1\Endpoints;
		}
	}

	/**
	 * Retrieves an affiliate object.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @see Affiliate_WP_DB::get_core_object()
	 *
	 * @param int|AffWP\Affiliate $affiliate Affiliate ID or object.
	 * @return AffWP\Affiliate|false Affiliate object, otherwise false.
	 */
	public function get_object( $affiliate ) {
		return $this->get_core_object( $affiliate, $this->query_object_type );
	}

	/**
	 * Get table columns and date types
	 *
	 * @access public
	 * @since  2.2
	*/
	public function get_columns() {
		return array(
			'customer_id'  => '%d',
			'user_id'      => '%d',
			'email'        => '%s',
			'first_name'   => '%s',
			'last_name'    => '%s',
			'ip'           => '%s',
			'date_created' => '%s',
		);
	}

	/**
	 * Get default column values
	 *
	 * @access public
	 * @since  2.2
	*/
	public function get_column_defaults() {
		return array(
			'date_created' => gmdate( 'Y-m-d H:i:s' ),
		);
	}

	/**
	 * Retrieve customers from the database
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param array $args {
	 *     Optional. Arguments for querying customers. Default empty array.
	 *
	 *     @type int          $number       Number of customers to query for. Default 20.
	 *     @type int          $offset       Number of customers to offset the query for. Default 0.
	 *     @type int|array    $exclude      Affiliate ID or array of IDs to explicitly exclude.
	 *     @type int|array    $user_id      User ID or array of user IDs that correspond to the affiliate user.
	 *     @type int|array    $affiliate_id Affiliate ID or array of affiliate IDs to retrieve.
	 *     @type string       $order        How to order returned affiliate results. Accepts 'ASC' or 'DESC'.
	 *                                      Default 'DESC'.
	 *     @type string       $orderby      Affiliates table column to order results by. Also accepts 'paid',
	 *                                      'unpaid', 'rejected', or 'pending' referral statuses, 'name'
	 *                                      (user display_name), or 'username' (user user_login). Default 'affiliate_id'.
	 *     @type string|array $fields       Specific fields to retrieve. Accepts 'ids', a single affiliate field, or an
	 *                                      array of fields. Default '*' (all).
	 * }
	 * @param bool  $count Optional. Whether to return only the total number of results found. Default false.
	 * @return array|int Array of affiliate objects or field(s) (if found), int if `$count` is true.
	 */
	public function get_customers( $args = array(), $count = false ) {
		global $wpdb;

		$defaults = array(
			'number'       => 20,
			'offset'       => 0,
			'exclude'      => array(),
			'user_id'      => 0,
			'affiliate_id' => 0,
			'status'       => '',
			'order'        => 'DESC',
			'orderby'      => 'customer_id',
			'fields'       => '',
		);

		$args = wp_parse_args( $args, $defaults );

		if( ! empty( $args['date_created'] ) ) {
			$args['date'] = $args['date_created'];
			unset( $args['date_created'] );
		}

		if( $args['number'] < 1 ) {
			$args['number'] = 999999999999;
		}

		$where = '';

		if ( ! empty( $args['exclude'] ) ) {
			$where .= empty( $where ) ? "WHERE " : "AND ";

			if ( is_array( $args['exclude'] ) ) {
				$exclude = implode( ',', array_map( 'intval', $args['exclude'] ) );
			} else {
				$exclude = intval( $args['exclude'] );
			}

			$where .= "`affiliate_id` NOT IN( {$exclude} )";
		}

		// affiliates for specific users
		if ( ! empty( $args['user_id'] ) ) {

			if ( is_array( $args['user_id'] ) ) {
				$user_ids = implode( ',', array_map( 'intval', $args['user_id'] ) );
			} else {
				$user_ids = intval( $args['user_id'] );
			}

			$where .= "WHERE `user_id` IN( {$user_ids} ) ";

		}		/*

		/* TODO: this has to be a meta join */

		/*
		// Specific affiliates.
		if ( ! empty( $args['affiliate_id'] ) ) {
			if ( is_array( $args['affiliate_id'] ) ) {
				$affiliates = implode( ',', array_map( 'intval', $args['affiliate_id'] ) );
			} else {
				$affiliates = intval( $args['affiliate_id'] );
			}

			if ( empty( $args['user_id'] ) ) {
				$where .= "WHERE `affiliate_id` IN( {$affiliates} )";
			} else {
				$where .= "AND `affiliate_id` IN( {$affiliates} )";
			}
		}*/

		if ( ! empty( $args['search'] ) ) {
			$search_value = $args['search'];

			if ( is_numeric( $search_value ) ) {
				$search = "`customer_id` IN( {$search_value} )";
			} elseif ( is_string( $search_value ) ) {

				// Searching by an affiliate's name or email
				if ( is_email( $search_value ) ) {

					$user    = get_user_by( 'email', $search_value );
					$user_id = $user ? absint( $user->ID ) : 0;
					$search  = "`user_id` = '" . $user_id . "'";

				} else {

					$search = "`first_name` LIKE( '{$search_value}' ) OR `last_name` LIKE( '{$search_value}' )";

				}
			}

			if ( ! empty( $search ) ) {

				if( ! empty( $where ) ) {
					$search = "AND " . $search;
				} else {
					$search = "WHERE " . $search;
				}

				$where .= $search;
			}

		}

		// Affiliates registered on a date or date range
		if( ! empty( $args['date'] ) ) {
			$where = $this->prepare_date_query( $where, $args['date'], 'date_created' );
		}

		if ( 'DESC' === strtoupper( $args['order'] ) ) {
			$order = 'DESC';
		} else {
			$order = 'ASC';
		}

		$join = '';

		// Orderby.
		switch( $args['orderby'] ) {
			case 'date':
				// Registered date.
				$orderby = 'date_created';
				break;

			default:
				// Check against the columns whitelist. If no match, default to $primary_key.
				$orderby = array_key_exists( $args['orderby'], $this->get_columns() ) ? $args['orderby'] : $this->primary_key;
				break;
		}

		// Overload args values for the benefit of the cache.
		$args['orderby'] = $orderby;
		$args['order']   = $order;

		$callback = '';

		if ( 'ids' === $args['fields'] ) {
			$fields   = "$this->primary_key";
			$callback = 'intval';
		} else {
			$fields = $this->parse_fields( $args['fields'] );

			if ( '*' === $fields ) {
				$callback = 'affwp_get_customer';
			}
		}

		$key = ( true === $count ) ? md5( 'affwp_customers_count' . serialize( $args ) ) : md5( 'affwp_customers_' . serialize( $args ) );

		$last_changed = wp_cache_get( 'last_changed', $this->cache_group );
		if ( ! $last_changed ) {
			$last_changed = microtime();
			wp_cache_set( 'last_changed', $last_changed, $this->cache_group );
		}

		$cache_key = "{$key}:{$last_changed}";

		$results = wp_cache_get( $cache_key, $this->cache_group );

		if ( false === $results ) {

			$clauses = compact( 'fields', 'join', 'where', 'orderby', 'order', 'count' );

			$results = $this->get_results( $clauses, $args, $callback );
		}

		wp_cache_add( $cache_key, $results, $this->cache_group, HOUR_IN_SECONDS );

		return $results;

	}

	/**
	 * Retrieves the number of results found for a given query.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param array $args Optional. Any valid get_customers() arguments. Default empty array.
	 * @return int Number of affiliates found for the given arguments.
	 */
	public function count( $args = array() ) {
		return $this->get_customers( $args, true );
	}


	/**
	 * Add a new customer
	 *
	 * @since 2.2
	 * @access public
	 *
	 * @param array $args {
	 *     Optional. Array of arguments for adding a new customer. Default empty array.
	 *
	 *     @type string $date_created Date the customer was registered. Default is the current time.
	 *     @type int    $user_id         User ID used to correspond to the customer.
	 *
	 * @return int|false Customer ID if successfully added, otherwise false.
	*/
	public function add( $data = array() ) {

		$defaults = array(
			'user_id' => 0
		);

		$args = wp_parse_args( $data, $defaults );

		if ( isset( $args['date_created'] ) ) {

			if ( empty( $args['date_created'] ) ) {
				unset( $args['date_created'] );
			} else {
				$time = strtotime( $args['date_created'] );

				$args['date_created'] = gmdate( 'Y-m-d H:i:s', $time - affiliate_wp()->utils->wp_offset );
			}
		}

		$add = $this->insert( $args, 'customer' );

		if ( $add ) {

			/**
			 * Fires immediately after an customer has been added to the database.
			 *
			 * @param int   $add  The new customer ID.
			 * @param array $args The arguments passed to the insert method.
			 */
			do_action( 'affwp_insert_customer', $add, $args );

			return $add;
		}

		return false;

	}

	/**
	 * Create the table
	 *
	 * @access public
	 * @since  2.2
	*/
	public function create_table() {
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		$sql = "CREATE TABLE {$this->table_name} (
			customer_id bigint(20) NOT NULL AUTO_INCREMENT,
			user_id bigint(20) NOT NULL,
			email varchar(100) NOT NULL,
			first_name varchar(250) NOT NULL,
			last_name varchar(250) NOT NULL,
			ip varchar(250) NOT NULL,
			date_created datetime NOT NULL,
			PRIMARY KEY  (customer_id),
			KEY user_id (user_id),
			KEY email (email)
			) CHARACTER SET utf8 COLLATE utf8_general_ci;";

		dbDelta( $sql );

		update_option( $this->table_name . '_db_version', $this->version );
	}

}

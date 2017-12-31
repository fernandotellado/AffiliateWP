<?php
namespace AffWP\Customer\CLI;

use \AffWP\CLI\Sub_Commands\Base;
use \WP_CLI\Utils as Utils;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * WP-CLI sub-commands for managing customers.
 *
 * @since 2.2
 * 
 * @see \AffWP\CLI\Sub_Commands\Base
 */
class Sub_Commands extends Base {

	/**
	 * Customer display fields.
	 *
	 * @since 2.2
	 * @access protected
	 * @var array
	 */
	protected $obj_fields = array(
		'ID',
		'first_name',
		'last_name',
		'email',
		'ip',
		'date'
	);

	/**
	 * Sets up the fetcher for sanity-checking.
	 *
	 * @since 2.2
	 * @access public
	 *
	 * @see \AffWP\Customer\CLI\Fetcher
	 */
	public function __construct() {
		$this->fetcher = new Fetcher();
	}

	/**
	 * Retrieves a customer object or field(s) by ID.
	 *
	 * ## OPTIONS
	 *
	 * <id>
	 * : The customer ID to retrieve.
	 *
	 * [--field=<field>]
	 * : Instead of returning the whole customer object, returns the value of a single field.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific fields. Defaults to all fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, json, csv, yaml. Default: table
	 *
	 * ## EXAMPLES
	 *
	 *     # Retrieve customer ID 12.
	 *     wp affwp customer get 12
	 */
	public function get( $args, $assoc_args ) {
		parent::get( $args, $assoc_args );
	}

	/**
	 * Adds a customer.
	 *
	 * ## OPTIONS
	 *
	 * [--name=<name>]
	 * : Required. Name identifier for the customer.
	 *
	 * [--description=<description>]
	 * : Description for the customer.
	 *
	 * [--link=<URL>]
	 * : URL the customer should link to.
	 *
	 * [--text=<text>]
	 * : Text for the customer.
	 *
	 * [--image=<URL>]
	 * : Image URL (local or external) to use for the customer.
	 *
	 * [--status=<status>]
	 * : Status for the customer. Accepts 'active' or 'inactive'. Default 'active'.
	 *
	 * ## EXAMPLES
	 *
	 *     # Creates a customer linking to http://affiliatewp.com
	 *     wp affwp customer create --name=AffiliateWP --link=http://affiliatewp.com
	 *
	 *     # Creates a customer using a locally-hosted image.
	 *     wp affwp customer create --name='Special Case' --image=https://example.org/my-image.jpg
	 *
	 *     # Create a customer with a status of 'inactive'
	 *     wp affwp customer create --name='My customer' --status=inactive
	 *
	 * @since 2.2
	 * @access public
	 *
	 * @internal The --link flag maps to 'url' because --url is a global WP CLI flag.
	 *
	 * @param array $_          Top-level arguments (unused).
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function create( $_, $assoc_args ) {
		$email = Utils\get_flag_value(  $assoc_args, 'email', '' );

		if ( empty( $email ) ) {
			\WP_CLI::error( __( 'A --email value must be specified to add a new customer.', 'affiliate-wp' ) );
		}

		$data['email']      = $email;
		$data['first_name'] = Utils\get_flag_value(  $assoc_args, 'first_name', ''       );
		$data['last_name']  = Utils\get_flag_value(  $assoc_args, 'last_name', ''       );
		$data['ip']         = Utils\get_flag_value(  $assoc_args, 'ip',        ''       );

		$created = affwp_add_customer( $data );

		if ( $created ) {
			$customer = affiliate_wp()->customers->get_by( 'name', $data['name'] );
			\WP_CLI::success( sprintf( __( 'A customer with the ID %d has been successfully created.', 'affiliate-wp' ), $customer->customer_id ) );
		} else {
			\WP_CLI::error( __( 'The customer could not be created.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Updates a customer.
	 *
	 * ## OPTIONS
	 *
	 * [--email=<email>]
	 * : Email identifier for the customer.
	 *
	 * [--first_name=<first_name>]
	 * : First Name for the customer.
	 *
	 * [--ip=<ip>]
	 * : URL the customer should link to.
	 *
	 * ## EXAMPLES
	 *
	 *     # Updates customer ID 300 with a new name 'New Name'
	 *     wp affwp customer update 300 --first_name='New Name'
	 *
	 *     # Updates customer ID 53 with a new email.
	 *     wp affwp customer update 53 --image='soso@so.com'
	 *
	 * @since 2.2
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function update( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid customer ID is required to proceed.', 'affiliate-wp' ) );
		}

		if ( ! $customer = affwp_get_customer( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid customer ID is required to proceed.', 'affiliate-wp' ) );
		}

		$data['first_name']  = Utils\get_flag_value(  $assoc_args, 'first_name', ''       );
		$data['last_name']   = Utils\get_flag_value(  $assoc_args, 'last_name', ''       );
		$data['ip']          = Utils\get_flag_value(  $assoc_args, 'ip',        ''       );
		$data['customer_id'] = $customer->customer_id;

		$updated = affwp_update_customer( $data );

		if ( $updated ) {
			\WP_CLI::success( __( 'The customer was successfully updated.', 'affiliate-wp' ) );
		} else {
			\WP_CLI::error( __( 'The customer could not be updated.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Deletes a customer.
	 *
	 * ## OPTIONS
	 *
	 * <customer_id>
	 * : customer ID.
	 *
	 * ## EXAMPLES
	 *
	 *     # Deletes the customer with ID 20
	 *     wp affwp customer delete 20
	 *
	 * @since 2.2
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function delete( $args, $assoc_args ) {
		if ( empty( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid customer ID is required to proceed.', 'affiliate-wp' ) );
		}

		if ( ! $customer = affwp_get_customer( $args[0] ) ) {
			\WP_CLI::error( __( 'A valid customer ID is required to proceed.', 'affiliate-wp' ) );
		}

		$deleted = affwp_delete_customer( $customer );

		if ( $deleted ) {
			\WP_CLI::success( __( 'The customer was successfully deleted.', 'affiliate-wp' ) );
		} else {
			\WP_CLI::error( __( 'The customer could not be deleted.', 'affiliate-wp' ) );
		}
	}

	/**
	 * Displays a list of customers.
	 *
	 * ## OPTIONS
	 *
	 * [--<field>=<value>]
	 * : One or more args to pass to get_customers().
	 *
	 * [--field=<field>]
	 * : Prints the value of a single field for each customer.
	 *
	 * [--fields=<fields>]
	 * : Limit the output to specific customer fields.
	 *
	 * [--format=<format>]
	 * : Accepted values: table, csv, json, count, ids, yaml. Default: table
	 *
	 * ## AVAILABLE FIELDS
	 *
	 * These fields will be displayed by default for each customer:
	 *
	 * * ID (alias for customer_id)
	 * * email
	 * * user_id
	 * * first_name
	 * * last_name
	 * * ip
	 * * date_created
	 *
	 * ## EXAMPLES
	 *
	 *     # List all customers by name.
	 *     wp affwp customer list --field=first_name
	 *
	 *     # List all customer IDs with an 'jo@test.com' email.
	 *     wp affwp customer list --email=jo@test.com --format=ids
	 *
	 *     # List all customers and display only the ID and image fields.
	 *     wp affwp customer list --fields=ID,ip
	 *
	 * @subcommand list
	 *
	 * @since 2.2
	 * @access public
	 *
	 * @param array $args       Top-level arguments.
	 * @param array $assoc_args Associated arguments (flags).
	 */
	public function list_( $_, $assoc_args ) {
		$formatter = $this->get_formatter( $assoc_args );

		$fields = $this->get_fields( $assoc_args );

		$defaults = array(
			'order' => 'ASC',
		);

		// Handle ID alias.
		if ( isset( $assoc_args['ID'] ) ) {
			$assoc_args['customer_id'] = $assoc_args['ID'];
			unset( $assoc_args['ID'] );
		}

		$args = array_merge( $defaults, $assoc_args );

		if ( 'count' == $formatter->format ) {
			$customers = affiliate_wp()->customers->count( $args );

			\WP_CLI::line( sprintf( __( 'Number of customers: %d', 'affiliate-wp' ), $customers ) );
		} else {
			$customers = affiliate_wp()->customers->get_customers( $args );
			$customers = $this->process_extra_fields( $fields, $customers );

			if ( 'ids' == $formatter->format ) {
				$customers = wp_list_pluck( $customers, 'customer_id' );
			} else {
				$customers = array_map( function( $customer ) {
					$customer->ID = $customer->customer_id;

					return $customer;
				}, $customers );
			}

			$formatter->display_items( $customers );
		}
	}

	/**
	 * Handler for the 'date' field.
	 *
	 * Reformats the date for display.
	 *
	 * @since 2.2
	 * @access protected
	 *
	 * @param \AffWP\customer $item Affiliate object (passed by reference).
	 */
	protected function date_field( &$item ) {
		$item->date = mysql2date( 'M j, Y', $item->date, false );
	}

}

\WP_CLI::add_command( 'affwp customer', 'AffWP\Customer\CLI\Sub_Commands' );

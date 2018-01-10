<?php
/**
 * Customers Admin List Table
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Customers
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.9
 */

use AffWP\Admin\List_Table;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * AffWP_Customers_Table Class
 *
 * Renders the Customers table on the Customers page
 *
 * @since 2.2
 *
 * @see \AffWP\Admin\List_Table
 */
class AffWP_Customers_Table extends List_Table {

	/**
	 * Default number of items to show per page
	 *
	 * @var int
	 * @since 2.2
	 */
	public $per_page = 30;

	/**
	 * Total number of referrals found
	 *
	 * @var int
	 * @since 2.2
	 */
	public $total_count;

	/**
	 * Get things started
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @see WP_List_Table::__construct()
	 *
	 * @param array $args Optional. Arbitrary display and query arguments to pass through
	 *                    the list table. Default empty array.
	 */
	public function __construct( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'singular' => 'customer',
			'plural'   => 'customers',
		) );

		parent::__construct( $args );

		$this->get_customer_counts();
	}

	/**
	 * Show the search field
	 *
	 * @access public
	 * @since 2.2
	 *
	 * @param string $text Label for the search box
	 * @param string $input_id ID of the search box
	 *
	 * @return svoid
	 */
	public function search_box( $text, $input_id ) {
		if ( empty( $_REQUEST['s'] ) && !$this->has_items() )
			return;

		$input_id = $input_id . '-search-input';

		if ( ! empty( $_REQUEST['orderby'] ) )
			echo '<input type="hidden" name="orderby" value="' . esc_attr( $_REQUEST['orderby'] ) . '" />';
		if ( ! empty( $_REQUEST['order'] ) )
			echo '<input type="hidden" name="order" value="' . esc_attr( $_REQUEST['order'] ) . '" />';
		?>
		<p class="search-box">
			<label class="screen-reader-text" for="<?php echo $input_id ?>"><?php echo $text; ?>:</label>
			<input type="search" id="<?php echo $input_id ?>" name="s" value="<?php _admin_search_query(); ?>" />
			<?php submit_button( $text, 'button', false, false, array( 'ID' => 'search-submit' ) ); ?>
		</p>
	<?php
	}

	/**
	 * Retrieve the view types
	 *
	 * @access public
	 * @since 2.2
	 * @return array $views All the views available
	 */
	public function get_views() {

		$affiliate_id   = isset( $_GET['affiliate_id'] ) ? absint( $_GET['affiliate_id'] ) : '';
		$base           = affwp_admin_url( 'referrals' );
		$base           = $affiliate_id ? add_query_arg( 'affiliate_id', $affiliate_id, $base ) : $base;
		$current        = isset( $_GET['status'] ) ? $_GET['status'] : '';
		$total_count    = '&nbsp;<span class="count">(' . $this->total_count    . ')</span>';

		$views = array(
			'all'      => sprintf( '<a href="%s"%s>%s</a>', esc_url( remove_query_arg( 'status', $base ) ), $current === 'all' || $current == '' ? ' class="current"' : '', __( 'All', 'affiliate-wp' ) . $total_count ),
		);

		return $views;
	}

	/**
	 * Retrieve the table columns
	 *
	 * @access public
	 * @since 2.2
	 * @return array $columns Array of all the list table columns
	 */
	public function get_columns() {
		$columns = array(
			'cb'           => '<input type="checkbox" />',
			'first_name'   => __( 'First Name', 'affiliate-wp' ),
			'last_name'    => __( 'Last Name', 'affiliate-wp' ),
			'email'        => __( 'Email', 'affiliate-wp' ),
			'ip'           => __( 'IP Address(es)', 'affiliate-wp' ),
			'date_created' => __( 'Date Created', 'affiliate-wp' ),
		);

		/**
		 * Filters the referrals list table columns.
		 *
		 * @param function               $prepared_columns Prepared columns.
		 * @param array                  $columns          The columns for this list table.
		 * @param \AffWP_Customers_Table $this             List table instance.
		 */
		return apply_filters( 'affwp_customers_table_columns', $this->prepare_columns( $columns ), $columns, $this );
	}

	/**
	 * Retrieve the table's sortable columns
	 *
	 * @access public
	 * @since 2.2
	 * @return array Array of all the sortable columns
	 */
	public function get_sortable_columns() {
		return array(
			'customer'  => array( 'customer_id', false ),
			'affiliate' => array( 'affiliate_id', false ),
			'user'      => array( 'user_id', false ),
			'date'      => array( 'date_creatd', false ),
		);
	}

	/**
	 * This function renders most of the columns in the list table.
	 *
	 * @access public
	 * @since 2.2
	 *
	 * @param \AffWP\Customer $customer    Contains all the data of the affiliate
	 * @param string          $column_name The name of the column
	 *
	 * @return string Column Name
	 */
	public function column_default( $customer, $column_name ) {
		switch( $column_name ) {

			case 'date' :
				$value = $customer->date_i18n();
				break;

			default:
				$value = isset( $customer->$column_name ) ? $customer->$column_name : '';
				break;
		}

		/**
		 * Filters the default value for each column in the customers list table.
		 *
		 * This dynamic filter is appended with a suffix of the column name, for example:
		 *
		 *     `affwp_customer_table_user_id`
		 *
		 * @param string $value    Column data to show.
		 * @param array  $customer Referral data.
		 *
		 */
		return apply_filters( 'affwp_customer_table_' . $column_name, $value, $customer );
	}

	/**
	 * Render the checkbox column
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param \AffWP\Customer $customer Contains all the data for the checkbox column
	 * @return string Displays a checkbox
	 */
	public function column_cb( $customer ) {
		return '<input type="checkbox" name="customer_id[]" value="' . absint( $customer->customer_id ) . '" />';
	}

	/**
	 * Renders the affiliate column.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param \AffWP\Customer $customer Contains all the data for the checkbox column
	 * @return string The affiliate
	 */
	public function column_affiliate( $customer ) {

		$value = affwp_admin_link(
			'customers',
			affiliate_wp()->affiliates->get_affiliate_name( $customer->affiliate_id ),
			array( 'affiliate_id' => $customer->affiliate_id )
		);

		/**
		 * Filters the customer's affiliate column data in the customers list table.
		 *
		 * You'll also need to specify the wrapping html for this value (defaults to
		 * an anchor to the referral admin screen for this referral).
		 *
		 * @param string          $value    Data shown in the Affiliate column.
		 * @param \AffWP\Customer $customer The referral data.
		 */
		$value = apply_filters( 'affwp_customer_affiliate_column', $value, $customer );

		/**
		 * Filters the referring affiliate column data in the customers list table.
		 *
		 * @param string          $value    Data shown in the Affiliate column.
		 * @param \AffWP\Customer $customer The referral data.
		 */
		return apply_filters( 'affwp_customer_table_affiliate', $value, $customer );
	}

	/**
	 * Render the actions column
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param \AffWP\Customer $customer Contains all the data for the actions column.
	 * @return string The actions HTML.
	 */
	public function column_actions( $customer ) {

		$row_actions = array();

		$base_query_args = array(
			'customer_id' => $customer->ID
		);

		// Edit.
		$row_actions['edit'] = $this->get_row_action_link(
			__( 'Edit', 'affiliate-wp' ),
			array_merge( $base_query_args, array(
				'action' => 'edit_customer'
			) ),
			array( 'class' => 'edit' )
		);

		// Delete.
		$row_actions['delete'] = $this->get_row_action_link(
			__( 'Delete', 'affiliate-wp' ),
			array_merge( $base_query_args, array(
				'affwp_action' => 'process_delete_customer'
			) ),
			array(
				'nonce' => 'affwp_delete_customer_nonce',
				'class' => 'delete'
			)
		);
		$row_actions['delete'] = '<span class="trash">' . $row_actions['delete'] . '</span>';

		/**
		 * Filters the row actions array for the Customers list table.
		 *
		 * Retained only for back-compat. Use {@see 'affwp_customer_row_actions'} instead.
		 *
		 * @since 1.2
		 *
		 * @param array           $row_actions Row actions array.
		 * @param \AffWP\Customer $customer    Current referral.
		 */
		$row_actions = apply_filters( 'affwp_customer_action_links', $row_actions, $customer );

		/**
		 * Filters the row actions array for the Customers list table.
		 *
		 * @since 1.9
		 *
		 * @param array           $row_actions Row actions array.
		 * @param \AffWP\Customer $customer    Current referral.
		 */
		$row_actions = apply_filters( 'affwp_customer_row_actions', $row_actions, $customer );

		return $this->row_actions( $row_actions, true );
	}

	/**
	 * Renders the message to be displayed when there are no customers.
	 *
	 * @since 1.7.2
	 * @access public
	 */
	public function no_items() {
		_e( 'No customers found.', 'affiliate-wp' );
	}

	/**
	 * Outputs the reporting views.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @param string $which Optional. Whether the bulk actions are being displayed at
	 *                      the top or bottom of the list table. Accepts either 'top'
	 *                      or bottom. Default empty.
	 */
	public function bulk_actions( $which = '' ) {

		if ( is_null( $this->_actions ) ) {
			$no_new_actions = $this->_actions = $this->get_bulk_actions();
			$this->_actions = array_intersect_assoc( $this->_actions, $no_new_actions );
			$two = '';
		} else {
			$two = '2';
		}

		if ( empty( $this->_actions ) )
			return;

		echo "<select name='action$two'>\n";
		echo "<option value='-1' selected='selected'>" . __( 'Bulk Actions', 'affiliate-wp' ) . "</option>\n";

		foreach ( $this->_actions as $name => $title ) {
			$class = 'edit' == $name ? ' class="hide-if-no-js"' : '';

			echo "\t<option value='$name'$class>$title</option>\n";
		}

		echo "</select>\n";

		/**
		 * Fires at the top and bottom of the customer bulk-actions admin screen
		 * (inside the form element).
		 *
		 * @param string $which Indicator for whether the bulk actions were rendered at the 'top'
		 *                      or 'bottom' of the referrals list table.
		 */
		do_action( 'affwp_customer_bulk_actions', $which );

		submit_button( __( 'Apply', 'affiliate-wp' ), 'action', false, false, array( 'id' => "doaction$two" ) );
		echo "\n";

		// Makes the filters only get output at the top of the page
		if( ! did_action( 'affwp_customer_filters' ) ) {
			$affiliate = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : false;

			if ( $affiliate && $affiliate = affwp_get_affiliate( $affiliate ) ) {
				$affiliate_name = affwp_get_affiliate_username( $affiliate );
			} else {
				$affiliate_name = '';
			}
			?>
			<span class="affwp-ajax-search-wrap">
				<input type="text" name="affiliate_id" id="user_name" class="affwp-user-search" value="<?php echo esc_attr( $affiliate_name ); ?>" data-affwp-status="any" autocomplete="off" placeholder="<?php _e( 'Affiliate name', 'affiliate-wp' ); ?>" />
			</span>
			<?php
			$from = ! empty( $_REQUEST['filter_from'] ) ? $_REQUEST['filter_from'] : '';
			$to   = ! empty( $_REQUEST['filter_to'] )   ? $_REQUEST['filter_to']   : '';

			echo "<input type='text' class='affwp-datepicker' autocomplete='off' name='filter_from' placeholder='" . __( 'From - mm/dd/yyyy', 'affiliate-wp' ) . "' value='" . esc_attr( $from ) . "'/>";
			echo "<input type='text' class='affwp-datepicker' autocomplete='off' name='filter_to' placeholder='" . __( 'To - mm/dd/yyyy', 'affiliate-wp' ) . "' value='" . esc_attr( $to ) . "'/>&nbsp;";

			/**
			 * Fires in the admin referrals screen, inside the search filters form area, prior to the submit button.
			 */
			do_action( 'affwp_customer_filters' );

			submit_button( __( 'Filter', 'affiliate-wp' ), 'action', false, false );
			echo "\n";

		}
	}

	/**
	 * Retrieves the bulk actions.
	 *
	 * @access public
	 * @since  2.2
	 *
	 * @return array $actions The array of bulk actions.
	 */
	public function get_bulk_actions() {
		$actions = array(
			'delete' => __( 'Delete', 'affiliate-wp' ),
		);

		/**
		 * Filters the bulk actions array for the referrals list table.
		 *
		 * @param array $actions List of bulk actions.
		 */
		return apply_filters( 'affwp_customers_bulk_actions', $actions );
	}

	/**
	 * Processes bulk actions for the customers list table.
	 *
	 * @access public
	 * @since  2.2
	 */
	public function process_bulk_action() {

		if( empty( $_REQUEST['_wpnonce'] ) ) {
			return;
		}

		if( ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'bulk-customers' ) && ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'customer-nonce' ) ) {
			return;
		}

		$ids = isset( $_GET['customer_id'] ) ? $_GET['customer_id'] : array();

		if ( ! is_array( $ids ) ) {
			$ids = array( $ids );
		}

		$ids    = array_map( 'absint', $ids );
		$action = ! empty( $_REQUEST['action'] ) ? $_REQUEST['action'] : false;

		if( empty( $ids ) || empty( $action ) ) {
			return;
		}

		foreach ( $ids as $id ) {

			if ( 'delete' === $this->current_action() ) {
				affwp_delete_customer( $id );
			}

			/**
			 * Fires after a customer bulk action is performed.
			 *
			 * The dynamic portion of the hook name, `$this->current_action()` refers
			 * to the current bulk action being performed.
			 *
			 * @param int $id The ID of the object.
			 */
			do_action( 'affwp_customers_do_bulk_action_' . $this->current_action(), $id );
		}

	}

	/**
	 * Retrieves the customer counts.
	 *
	 * @access public
	 * @since  2.2
	 */
	public function get_customer_counts() {

		$affiliate_id = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id'] : '';
		$customer_id  = isset( $_GET['customer_id'] ) ? $_GET['customer_id'] : '';

		if( is_array( $affiliate_id ) ) {
			$affiliate_id = array_map( 'absint', $affiliate_id );
		} else {
			$affiliate_id = absint( $affiliate_id );
		}

		if( is_array( $customer_id ) ) {
			$customer_id = array_map( 'absint', $customer_id );
		} else {
			$customer_id = absint( $customer_id );
		}

		$this->total_count = affiliate_wp()->customers->count(
			array_merge( $this->query_args, array(
				'affiliate_id' => $affiliate_id,
				'customer_id'  => $customer_id,
			) )
		);

	}

	/**
	 * Retrieve all the data for all the referrals
	 *
	 * @access public
	 * @since 2.2
	 * @return array $customers_data Array of all the data for the Affiliates
	 */
	public function customers_data() {

		$page        = isset( $_GET['paged'] )        ? absint( $_GET['paged'] ) : 1;
		$affiliate   = isset( $_GET['affiliate_id'] ) ? $_GET['affiliate_id']    : '';
		$user_id     = isset( $_GET['user_id'] )      ? $_GET['user_id']         : '';
		$customer    = isset( $_GET['customer_id'] )  ? $_GET['customer_id']     : '';
		$from        = isset( $_GET['filter_from'] )  ? $_GET['filter_from']     : '';
		$to          = isset( $_GET['filter_to'] )    ? $_GET['filter_to']       : '';
		$order       = isset( $_GET['order'] )        ? $_GET['order']           : 'DESC';
		$orderby     = isset( $_GET['orderby'] )      ? $_GET['orderby']         : 'customer_id';
		$is_search   = false;

		if ( $affiliate && $affiliate = affwp_get_affiliate( $affiliate ) ) {
			$affiliate = $affiliate->ID;
		} else {
			// Switch back to empty for the benefit of get_customers().
			$affiliate = '';
		}

		$date = array();
		if( ! empty( $from ) ) {
			$date['start'] = $from;
		}
		if( ! empty( $to ) ) {
			$date['end']   = $to . ' 23:59:59';;
		}

		if( ! empty( $_GET['s'] ) ) {

			$is_search = true;

			$search = sanitize_text_field( $_GET['s'] );

			if( is_numeric( $search ) ) {
				// This is an referral ID search
				$customer = absint( $search );
			} elseif ( strpos( $search, 'user_id:' ) !== false ) {
				$user_id = trim( str_replace( 'user:', '', $search ) );
			} elseif ( strpos( $search, 'context:' ) !== false ) {
				$context = trim( str_replace( 'context:', '', $search ) );
			} elseif ( strpos( $search, 'affiliate:' ) !== false ) {
				$affiliate = absint( trim( str_replace( 'affiliate:', '', $search ) ) );
			}

		}

		$per_page = $this->get_items_per_page( 'affwp_edit_customers_per_page', $this->per_page );

		$args = wp_parse_args( $this->query_args, array(
			'number'       => $per_page,
			'offset'       => $per_page * ( $page - 1 ),
			'customer_id'  => $customer,
			'user_id'      => $user_id,
			'affiliate_id' => $affiliate,
			'date_created' => $date,
			'search'       => $is_search,
			'orderby'      => sanitize_text_field( $orderby ),
			'order'        => sanitize_text_field( $order )
		) );

		$customers = affiliate_wp()->customers->get_customers( $args );

		// Retrieve the "current" total count for pagination purposes.
		$args['number']      = -1;
		$this->current_count = affiliate_wp()->customers->count( $args );

		return $customers;
	}

	/**
	 * Setup the final data for the table
	 *
	 * @access public
	 * @since 2.2
	 * @uses AffWP_Customers_Table::get_columns()
	 * @uses AffWP_Customers_Table::get_sortable_columns()
	 * @uses AffWP_Customers_Table::process_bulk_action()
	 * @uses AffWP_Customers_Table::customers_data()
	 * @uses WP_List_Table::get_pagenum()
	 * @uses WP_List_Table::set_pagination_args()
	 * @return void
	 */
	public function prepare_items() {
		$per_page = $this->get_items_per_page( 'affwp_edit_customers_per_page', $this->per_page );

		$this->get_column_info();

		$this->process_bulk_action();

		$this->items = $this->customers_data();

		$this->set_pagination_args( array(
			'total_items' => $this->total_count,
			'per_page'    => $per_page,
			'total_pages' => ceil( $this->total_count / $per_page )
		) );
	}
}

<?php
/**
 * Customers Admin
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Customers
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/customers/screen-options.php';
include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/customers/contextual-help.php';
require_once AFFILIATEWP_PLUGIN_DIR . 'includes/admin/customers/class-list-table.php';

function affwp_customers_admin() {

	if( isset( $_GET['action'] ) && 'add_customer' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/customers/new.php';

	} else if( isset( $_GET['action'] ) && 'edit_customer' == $_GET['action'] ) {

		include AFFILIATEWP_PLUGIN_DIR . 'includes/admin/customers/edit.php';

	} else {

		$customers_table = new AffWP_Customers_Table();
		$customers_table->prepare_items();
		?>
		<div class="wrap">
			<h1>
				<?php _e( 'Customers', 'affiliate-wp' ); ?>
				<a href="<?php echo esc_url( add_query_arg( 'action', 'add_customer' ) ); ?>" class="page-title-action"><?php _e( 'Add New', 'affiliate-wp' ); ?></a>
				<a href="<?php echo esc_url( add_query_arg( array( 'page' => 'affiliate-wp-reports', 'tab' => 'customers' ) ) ); ?>" class="page-title-action"><?php _ex( 'Reports', 'customers', 'affiliate-wp' ); ?></a>
				<button class="page-title-action affwp-customers-export-toggle" style="display:none"><?php _e( 'Close', 'affiliate-wp' ); ?></button>
			</h1>

			<?php
			/**
			 * Fires at the top of the customers list-table admin screen.
			 */
			do_action( 'affwp_customers_page_top' );
			?>

			<div id="affwp-customers-export-wrap">

				<?php
				/**
				 * Fires in the action buttons area of the customers list-table admin screen.
				 */
				do_action( 'affwp_customers_page_buttons' );
				?>

			</div>
			<form id="affwp-customers-filter-form" method="get" action="<?php echo esc_url( affwp_admin_url( 'customers' ) ); ?>">

				<?php $customers_table->search_box( __( 'Search', 'affiliate-wp' ), 'affwp-customers' ); ?>

				<input type="hidden" name="page" value="affiliate-wp-customers" />

				<?php $customers_table->views() ?>
				<?php $customers_table->display() ?>
			</form>
			<?php
			/**
			 * Fires at the bottom of the customers list table admin screen.
			 */
			do_action( 'affwp_customers_page_bottom' );
			?>
		</div>
	<?php
	}

}

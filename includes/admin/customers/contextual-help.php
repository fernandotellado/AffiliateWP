<?php
/**
 * Contextual Help
 *
 * @package     AffiliateWP
 * @subpackage  Admin/Customers
 * @copyright   Copyright (c) 2014, Pippin Williamson
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       2.2
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/**
 * Customers contextual help.
 *
 * @access      private
 * @since       2.2
 * @return      void
 */
function affwp_customers_contextual_help() {

	$screen = get_current_screen();

	if ( $screen->id != 'affiliates_page_affiliate-wp-customers' )
		return;

	$sidebar_text = '<p><strong>' . __( 'For more information:', 'affiliate-wp' ) . '</strong></p>';
	$sidebar_text .= '<p>' . sprintf( __( 'Visit the <a href="%s">documentation</a> on the AffiliateWP website.', 'affiliate-wp' ), esc_url( 'https://affiliatewp.com/documentation/' ) ) . '</p>';
	$sidebar_text .= '<p>' . sprintf( __( '<a href="%s">Post an issue</a> on <a href="%s">GitHub</a>.', 'affiliate-wp' ), esc_url( 'https://github.com/affiliatewp/AffiliateWP/issues' ), esc_url( 'https://github.com/affiliatewp/AffiliateWP' )  ) . '</p>';

	$screen->set_help_sidebar( $sidebar_text );

	$screen->add_help_tab( array(
		'id'	    => 'affwp-customers-overview',
		'title'	    => __( 'Overview', 'affiliate-wp' ),
		'content'	=>
			'<p>' . __( "This screen provides access to your affiliates&#8217;s customers. These are customers that have been referred to your site by your affiliates.", 'affiliate-wp' ) . '</p>'
	) );

	$screen->add_help_tab( array(
		'id'	    => 'affwp-customers-search',
		'title'	    => __( 'Searching customers', 'affiliate-wp' ),
		'content'	=>
			'<p>' . __( 'Customers can be searched in several different ways:', 'affiliate-wp' ) . '</p>' .
			'<ul>
				<li>' . __( 'You can enter the customer&#8217;s ID number', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the customer&#8217;s affiliate ID number prefixed by &#8220;user_id:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the customer&#8217;s user ID number prefixed by &#8220;user_id:&#8221;', 'affiliate-wp' ) . '</li>
				<li>' . __( 'You can enter the customer&#8217;s first name', 'affiliate-wp' ) . '</li> 
				<li>' . __( 'You can enter the customer&#8217;s last name', 'affiliate-wp' ) . '</li>
			</ul>'
	) );

	/**
	 * Fires in the contextual help area of the referral admin screen.
	 *
	 * @param string $screen The current screen.
	 */
	do_action( 'affwp_customers_contextual_help', $screen );
}
add_action( 'load-affiliates_page_affiliate-wp-customemrs', 'affwp_customers_contextual_help' );

<?php
namespace AffWP\Admin\Overview\Meta_Box;

use AffWP\Admin\Meta_Box;

/**
 * Implements a Highest Converting URLs meta box for the Overview screen.
 *
 * The meta box displays highest converting urls.
 *
 * @since 2.1.12
 * @see   \AffWP\Admin\Meta_Box
 */
class Highest_Converting_URLs extends Meta_Box implements Meta_Box\Base {

	/**
	 * Initialize.
	 *
	 * Define the meta box name, meta box id,
	 * and the action on which to hook the meta box here.
	 *
	 * Example:
	 *
	 * $this->action        = 'affwp_overview_meta_boxes';
	 * $this->meta_box_name = __( 'Name of the meta box', 'affiliate-wp' );
	 *
	 * @access  public
	 * @return  void
	 * @since   1.9
	 */
	public function init() {
		$this->action        = 'affwp_overview_meta_boxes';
		$this->meta_box_name = __( 'Highest Converting URLs', 'affiliate-wp' );
		$this->meta_box_id   = 'overview-highest-converting-urls';
		$this->context       = 'primary';
	}

	/**
	 * Defines the content of the metabox.
	 *
	 * @return mixed content  The metabox content.
	 * @since  1.9
	 */
	public function content() {

		$referrers = affiliate_wp()->visits->get_visits( array(
			'number'       => -1,
			'fields'       => array( 'referrer', 'referral_id' )
		) ); ?>

		<table class="affwp_table">

			<thead>

				<tr>
					<th><?php _ex( 'URL', 'URL column table header', 'affiliate-wp' ); ?></th>
					<th><?php _ex( 'Conversions', 'Conversions column table header', 'affiliate-wp' ); ?></th>
				</tr>

			</thead>

			<tbody>
				<?php if( $referrers ) : ?>

					<?php

						$referrers = array_filter( $referrers, function( $referrer ) {
						    return ! empty( $referrer->referral_id );
						});

						$referrers = wp_list_pluck( array_values( $referrers ), 'referrer' );

						$referrers = array_count_values( array_map( function( $referrer ) {
							if ( empty( $referrer ) ) {
								$referrer = 'direct';
							}
							return $referrer;
						}, $referrers ) );

						arsort( $referrers );

						/**
						 * Filters the number of highest converting urls to display
						 *
						 * @since 2.1.12
						 *
						 * @param integer $count The number of highest converting urls to display
						 *
						 */
						$count = apply_filters( 'affwp_overview_highest_converting_urls_count', 5 );

						$referrers = array_slice( $referrers, 0, $count, true );

					?>

					<?php if( $referrers ) : ?>
						<?php foreach( $referrers as $url => $conversions ) : ?>
							<tr>
								<td>
									<?php if( 'direct' == $url ): ?>

										<?php _e( 'Direct traffic', 'affiliate-wp' ); ?>

									<?php else: ?>

										<a href="<?php echo esc_url( $url ); ?>"><?php echo esc_html( $url ); ?></a>

									<?php endif; ?>
								</td>
								<td><?php echo affwp_format_amount( absint( $conversions ), false ); ?></td>
							</tr>
						<?php endforeach; ?>
					<?php else: ?>
						<tr>
							<td colspan="2"><?php _e( 'No highest converting urls recorded yet', 'affiliate-wp' ); ?></td>
						</tr>
					<?php endif; ?>

				<?php else: ?>
					<tr>
						<td colspan="2"><?php _e( 'No highest converting urls recorded yet', 'affiliate-wp' ); ?></td>
					</tr>
				<?php endif; ?>
			</tbody>

		</table>
	<?php }
}

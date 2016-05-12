<?php


class AffWP_Metabox_Affiliate_Leaderboard extends AffWP_Metabox_Base {


    /**
    * The id of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.8
    */
    public $meta_box_id = 'overview-affiliate-leaderboard';

    /**
    * The name of the meta box. Must be unique.
    *
    * @access  public
    * @since   1.8
    */
    public $meta_box_name;

    /**
     * The position in which the meta box will be loaded
     * Either 'normal' side'.
     *
     * @access  public
     * @var     $context
     * @since   1.8
     */
    public $context = 'side';

    /**
     * Initialize
     *
     * @access  public
     * @return  void
     * @since   1.8
     */
    public function init() {
        add_action( 'add_meta_box',     array( $this, 'add_meta_box' ) );
        add_action( 'affwp_reports_meta_boxes', array( $this, 'add_meta_box' ) );
        $this->meta_box_name = __( 'Top Affiliates', 'affiliate-wp' );
    }

    /**
     * Displays an overview of earnings in 3 different tables
     *
     * @return mixed content An overview of referrals and earnings
     * @since  1.8
     */
    public function content() {

        $affiliates = affiliate_wp()->affiliates->get_affiliates( apply_filters( 'affwp_overview_most_valuable_affiliates', array( 'number' => 5, 'orderby' => 'earnings', 'order' => 'DESC' ) ) );

        ?>
        <h3>
            <?php echo __( 'The top-earning affiliates.', 'affiliate-wp' ); ?>
        </h3>
        <table class="affwp_table affwp-reports-table">

            <thead>

                <tr>
                    <th><?php _e( 'Affiliate', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Earnings', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Referrals', 'affiliate-wp' ); ?></th>
                    <th><?php _e( 'Visits', 'affiliate-wp' ); ?></th>
                </tr>

            </thead>

            <tbody>
            <?php if( $affiliates ) : ?>
                <?php foreach( $affiliates as $affiliate  ) : ?>
                    <tr>
                        <td class="leader"><?php echo affiliate_wp()->affiliates->get_affiliate_name( $affiliate->affiliate_id ); ?></td>
                        <td><?php echo affwp_currency_filter( $affiliate->earnings ); ?></td>
                        <td><?php echo absint( $affiliate->referrals ); ?></td>
                        <td><?php echo absint( $affiliate->visits ); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="4"><?php _e( 'No registered affiliates', 'affiliate-wp' ); ?></td>
                </tr>
            <?php endif; ?>
            </tbody>

        </table>

    <?php }
}

new AffWP_Metabox_Affiliate_Leaderboard;

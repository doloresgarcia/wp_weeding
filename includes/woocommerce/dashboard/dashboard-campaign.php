<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

$page_numb = max( 1, get_query_var('paged') );
$posts_per_page = get_option( 'posts_per_page',10 );
$args = array(
    'post_type' 		=> 'product',
    'post_status'		=> array('publish', 'draft'),
    'author'    		=> get_current_user_id(),
    'tax_query' 		=> array(
        array(
            'taxonomy' => 'product_type',
            'field'    => 'slug',
            'terms'    => 'crowdfunding',
        ),
    ),
    'posts_per_page'    => 4,
    'paged'             => $page_numb
);

$current_page = get_permalink();

$html .= '<div class="wpneo-row wp-dashboard-row">';
$the_query = new WP_Query( $args );
if ( $the_query->have_posts() ) :
    global $post;
    $i = 1;
    while ( $the_query->have_posts() ) : $the_query->the_post();
        ob_start(); 
        ?>

        <div class="wpneo-col6">
            <div class="wpcrowd-listing">
                <a href="<?php echo get_permalink(); ?>" title="<?php  echo get_the_title(); ?>"> <?php echo woocommerce_get_product_thumbnail(); ?></a>
            </div>
            <div class="wpcrowd-listing-content">
                <div class="wpcrowd-admin-title">
                    <!-- title -->
                    <h3><a href="<?php  echo get_permalink(); ?> "><?php echo get_the_title(); ?></a></h3>
                </div>
                    <div class="wpcrowd-admin-metadata">
                        <div class="wpcrowd-admin-meta-info">
                            <!--  Days to go -->
                            <span class="wpneo-meta-wrap">
                                <?php $days_remaining = apply_filters('date_expired_msg', __('0', 'wp-crowdfunding'));
                                if (WPNEOCF()->dateRemaining()){
                                    $days_remaining = apply_filters('date_remaining_msg', __(WPNEOCF()->dateRemaining(), 'wp-crowdfunding'));
                                }
                                $wpneo_campaign_end_method = get_post_meta(get_the_ID(), 'wpneo_campaign_end_method', true);
                                if ($wpneo_campaign_end_method != 'never_end'){ ?>
                                    <?php if (WPNEOCF()->is_campaign_started()){ ?>
                                        <p class="funding-amount"><?php echo WPNEOCF()->dateRemaining(); ?></p>
                                        <span class="info-text"><?php _e( 'Days to go','wp-crowdfunding' ); ?></span>
                                    <?php } else { ?>
                                        <p class="funding-amount"><?php echo WPNEOCF()->days_until_launch(); ?></p>
                                        <span class="info-text"><?php _e( 'Days Until Launch','wp-crowdfunding' ); ?></span>
                                    <?php } ?>
                                <?php } ?>
                            </span>
                            <!-- author -->
                            <?php $author_name = wpneo_crowdfunding_get_author_name(); ?>
                            <span class="wpneo-meta-wrap">
                                <span class="wpneo-meta-name"><?php _e('by','wp-crowdfunding'); ?> </span>
                                <a href="<?php echo wpneo_crowdfunding_campaign_listing_by_author_url( get_the_author_meta( 'user_login' ) ); ?>"><?php echo $author_name; ?></a>
                            </span>

                            <!-- fund-raised -->
                            <?php 
                            $raised_percent = WPNEOCF()->getFundRaisedPercentFormat();
                            $raised = 0;
                            $total_raised = WPNEOCF()->totalFundRaisedByCampaign();
                            if ($total_raised){
                                $raised = $total_raised;
                            }
                            ?>
                            <span class="wpneo-meta-wrap">
                                <span class="wpneo-meta-name"><?php _e('Total', 'wp-crowdfunding'); ?> </span>
                                <?php echo wc_price($raised); ?>
                            </span>
                            <span class="wpneo-meta-wrap">
                                <!-- Prix -->
                                <?php $funding_goal = get_post_meta($post->ID, '_nf_funding_goal', true); ?>
                                <span class="wpneo-meta-name"><?php _e('Goal', 'wp-crowdfunding'); ?></span>
                                <?php echo wc_price( $funding_goal ); ?>
                            </span>   

                        </div><!--wpcrowd-admin-meta-info -->
                    </div><!-- wpcrowd-admin-metadata -->
            </div><!-- wpneo-listing-content -->
            <?php do_action('wpneo_dashboard_campaign_loop_item_after_content'); ?>
            <div style="clear: both"></div>
        </div>
        <?php $i++;
        $html .= ob_get_clean();
    endwhile;
    wp_reset_postdata();
else :
    $html .= "<p>".__( 'Sorry, no Campaign Found.','wp-crowdfunding' )."</p>";
endif;
$html .= '</div>';
$html .= wpneo_crowdfunding_pagination( $page_numb , $the_query->max_num_pages );


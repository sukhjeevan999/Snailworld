<?php
/**
 * No posts found.
 *
 * @package SnailWorld
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="sw-404">
	<?php snailworld_icon( 'snail' ); ?>
	<h1><?php esc_html_e( 'Nothing here yet', 'snailworld' ); ?></h1>
	<?php if ( is_search() ) : ?>
		<p>
			<?php
			/* translators: %s: search query. */
			printf( esc_html__( 'No results found for "%s". Try a different search term.', 'snailworld' ), esc_html( get_search_query() ) );
			?>
		</p>
		<div style="max-width:420px;margin:1.5rem auto 0;"><?php get_search_form(); ?></div>
	<?php else : ?>
		<p><?php esc_html_e( 'It looks like nothing was found. Try browsing a category instead.', 'snailworld' ); ?></p>
	<?php endif; ?>
</div>

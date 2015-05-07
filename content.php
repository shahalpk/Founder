<div <?php post_class(); ?>>
	<?php hybrid_do_atomic( 'post_before' ); ?>
	<article>
		<div class='post-header'>
			<h1 class='post-title'><?php the_title(); ?></h1>
			<?php get_template_part('content/post-meta'); ?>
		</div>
		<?php ct_founder_featured_image(); ?>
		<div class="post-content">
	        <?php the_content(); ?>
	        <?php wp_link_pages(array('before' => '<p class="singular-pagination">' . __('Pages:','founder'), 'after' => '</p>', ) ); ?>
	    </div>
		<div class="post-after">
			<?php hybrid_do_atomic( 'post_after' ); ?>
			<?php get_template_part('content/post-categories'); ?>
			<?php get_template_part('content/post-tags'); ?>
			<?php get_template_part('content/post-nav'); ?>
		</div>
	</article>
	<?php comments_template(); ?>
</div>
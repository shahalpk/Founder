<?php

if ( is_home() ) {
	echo '<h1 class="screen-reader-text">' . esc_html( get_bloginfo("name") ) . ' ' . esc_html__('Posts', 'founder') . '</h1>';
}
if ( ! is_archive() ) {
	return;
}

$icon_class = 'folder-open';

if ( is_tag() ) {
	$icon_class = 'tag';
} elseif ( is_author() ) {
	$icon_class = 'user';
} elseif ( is_date() ) {
	$icon_class = 'calendar';
}
?>

<div class='archive-header'>
	<i class="fas fa-<?php echo esc_attr( $icon_class ); ?>"></i>
	<h1>
		<?php the_archive_title(); ?>
	</h1>
	<?php the_archive_description(); ?>
</div>
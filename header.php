<!DOCTYPE html>

<!--[if IE 8 ]><html class="ie8" <?php language_attributes(); ?>> <![endif]-->
<!--[if IE 9 ]><html class="ie9" <?php language_attributes(); ?>> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html <?php language_attributes(); ?>> <!--<![endif]-->

<head>

	<!--[if IE 8 ]>
	<script src="<?php echo trailingslashit( get_template_directory_uri() ) . 'js/build/html5shiv.min.js'; ?>"></script>
	<![endif]-->

    <?php wp_head(); ?>

</head>

<body id="<?php print get_stylesheet(); ?>" <?php body_class(); ?>>

<!--skip to content link-->
<a class="skip-content" href="#main"><?php _e('Skip to content', 'founder'); ?></a>

<div id="overflow-container" class="overflow-container">

<header class="site-header" id="site-header" role="banner">

	<?php ct_founder_social_icons_output('header'); ?>
	<?php get_template_part('content/search-bar'); ?>

	<div id="title-container" class="title-container">
		<?php get_template_part('logo')  ?>
		<p class="site-description"><?php bloginfo('description'); ?></p>
	</div>
	
	<?php get_template_part( 'menu', 'primary' ); ?>

	<button id="toggle-navigation" class="toggle-navigation">
		<i class="fa fa-bars"></i>
	</button>

</header>
<section id="main" class="main" role="main">
<?php

// Load the core theme framework.
require_once( trailingslashit( get_template_directory() ) . 'library/hybrid.php' );
new Hybrid();

// theme setup
function ct_founder_theme_setup() {
	
    /* Get action/filter hook prefix. */
	$prefix = hybrid_get_prefix();
    
	// add Hybrid core functionality
    add_theme_support( 'hybrid-core-template-hierarchy' );
    add_theme_support( 'loop-pagination' );
	add_theme_support( 'cleaner-gallery' );

    // add functionality from WordPress core
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );

	// load theme options page
	require_once( trailingslashit( get_template_directory() ) . 'theme-options.php' );

	// add inc folder files
	foreach (glob(trailingslashit( get_template_directory() ) . 'inc/*') as $filename)
	{
		include $filename;
	}

	// load text domain
	load_theme_textdomain('founder', get_template_directory() . '/languages');

	// register Primary menu
    register_nav_menus(array(
        'primary' => __('Primary', 'founder')
    ));
}
add_action( 'after_setup_theme', 'ct_founder_theme_setup', 10 );

// remove filters adding partial micro-data due to validation issues
function founder_remove_hybrid_filters() {
    remove_filter( 'the_author_posts_link', 'hybrid_the_author_posts_link', 5 );
    remove_filter( 'get_comment_author_link', 'hybrid_get_comment_author_link', 5 );
    remove_filter( 'get_comment_author_url_link', 'hybrid_get_comment_author_url_link', 5 );
    remove_filter( 'comment_reply_link', 'hybrid_comment_reply_link_filter', 5 );
    remove_filter( 'get_avatar', 'hybrid_get_avatar', 5 );
    remove_filter( 'post_thumbnail_html', 'hybrid_post_thumbnail_html', 5 );
    remove_filter( 'comments_popup_link_attributes', 'hybrid_comments_popup_link_attributes', 5 );
}
add_action('after_setup_theme', 'founder_remove_hybrid_filters');

// turn off cleaner gallery if Jetpack gallery functions being used
function ct_founder_remove_cleaner_gallery() {

	if( class_exists( 'Jetpack' ) && ( Jetpack::is_module_active( 'carousel' ) || Jetpack::is_module_active( 'tiled-gallery' ) ) ) {
		remove_theme_support( 'cleaner-gallery' );
	}
}
add_action( 'after_setup_theme', 'ct_founder_remove_cleaner_gallery', 11 );

// register widget areas
function ct_founder_register_widget_areas(){

    /* register after post content widget area */
    hybrid_register_sidebar( array(
        'name'         => __( 'Primary Sidebar', 'founder' ),
        'id'           => 'sidebar',
        'description'  => __( 'Widgets in this area will be shown in the sidebar next to the main post content', 'founder' )
    ) );
}
add_action('widgets_init','ct_founder_register_widget_areas');

/* added to customize the comments. Same as default except -> added use of gravatar images for comment authors */
function ct_founder_customize_comments( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    global $post;
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment">
            <div class="comment-author">
                <?php
                // if is post author
                if( $comment->user_id === $post->post_author ) {
                    ct_founder_profile_image_output();
                } else {
                    echo get_avatar( get_comment_author_email(), 48 );
                }
                ?>
                <div>
                    <div class="author-name"><?php comment_author_link(); ?></div>
                    <div class="comment-date"><?php comment_date('n/j/Y'); ?></div>
                    <?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( 'Reply', 'founder' ), 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
                    <?php edit_comment_link( 'edit' ); ?>
                </div>    
            </div>
            <div class="comment-content">
                <?php if ($comment->comment_approved == '0') : ?>
                    <em><?php _e('Your comment is awaiting moderation.', 'founder') ?></em>
                    <br />
                <?php endif; ?>
                <?php comment_text(); ?>
            </div>
        </article>
    <?php
}

/* added HTML5 placeholders for each default field and aria-required to required */
if( ! function_exists( 'founder_update_fields' ) ) {
    function founder_update_fields( $fields ) {

        // get commenter object
        $commenter = wp_get_current_commenter();

        // are name and email required?
        $req = get_option( 'require_name_email' );

        // required or optional label to be added
        if ( $req == 1 ) {
            $label = '*';
        } else {
            $label = ' ' . __("optional", "founder");
        }

        // adds aria required tag if required
        $aria_req = ( $req ? " aria-required='true'" : '' );

        $fields['author'] =
            '<p class="comment-form-author">
	            <label>' . __( "Name", "founder" ) . $label . '</label>
	            <input placeholder="' . __( "John Doe", "founder" ) . '" id="author" name="author" type="text" value="' . esc_attr( $commenter['comment_author'] ) .
            '" size="30" ' . $aria_req . ' />
	        </p>';

        $fields['email'] =
            '<p class="comment-form-email">
	            <label>' . __( "Email", "founder" ) . $label . '</label>
	            <input placeholder="' . __( "name@email.com", "founder" ) . '" id="email" name="email" type="email" value="' . esc_attr( $commenter['comment_author_email'] ) .
            '" size="30" ' . $aria_req . ' />
	        </p>';

        $fields['url'] =
            '<p class="comment-form-url">
	            <label>' . __( "Website", "founder" ) . '</label>
	            <input placeholder="' . __( "http://example.com", "founder" ) . '" id="url" name="url" type="url" value="' . esc_attr( $commenter['comment_author_url'] ) .
            '" size="30" />
	            </p>';

        return $fields;
    }
}
add_filter('comment_form_default_fields','founder_update_fields');

if( ! function_exists( 'founder_update_comment_field' ) ) {
    function founder_update_comment_field( $comment_field ) {

        $comment_field =
            '<p class="comment-form-comment">
	            <label>' . __( "Comment", "founder" ) . '</label>
	            <textarea required placeholder="' . __( "Enter Your Comment", "founder" ) . '&#8230;" id="comment" name="comment" cols="45" rows="8" aria-required="true"></textarea>
	        </p>';

        return $comment_field;
    }
}
add_filter('comment_form_field_comment','founder_update_comment_field');

// remove allowed tags text after comment form
function ct_founder_remove_comments_notes_after($defaults){

    $defaults['comment_notes_after']='';
    return $defaults;
}

add_action('comment_form_defaults', 'ct_founder_remove_comments_notes_after');

// excerpt handling
function ct_founder_excerpt() {

    // make post variable available
    global $post;

    // make 'read more' setting available
    global $more;

    // check for the more tag
    $ismore = strpos( $post->post_content, '<!--more-->');

    // use the read more link if present
    if($ismore) {
        the_content( __('Read More', 'founder') . "<span class='screen-reader-text'>" . get_the_title() . "</span>");
    }
    // otherwise the excerpt is automatic, so output it
    else {
        the_excerpt();
    }
}

// filter the link on excerpts
function ct_founder_excerpt_read_more_link($output) {
	global $post;
	return $output . "<p><a class='more-link' href='". get_permalink() ."'>" . __('Read More', 'founder') . "<span class='screen-reader-text'>" . get_the_title() . "</span></a></p>";
}

add_filter('the_excerpt', 'ct_founder_excerpt_read_more_link');

// change the length of the excerpts
function founder_custom_excerpt_length( $length ) {
    return 25;
}
add_filter( 'excerpt_length', 'founder_custom_excerpt_length', 99 );

// switch [...] to ellipsis on automatic excerpt
function ct_founder_new_excerpt_more( $more ) {
	return '&#8230;';
}
add_filter('excerpt_more', 'ct_founder_new_excerpt_more');

// turns of the automatic scrolling to the read more link 
function ct_founder_remove_more_link_scroll( $link ) {
	$link = preg_replace( '|#more-[0-9]+|', '', $link );
	return $link;
}
add_filter( 'the_content_more_link', 'ct_founder_remove_more_link_scroll' );

// for displaying featured images
function ct_founder_featured_image() {

	// get post object
	global $post;
	// default to no featured image
	$has_image = false;

	// if post has an image
	if (has_post_thumbnail( $post->ID ) ) {
		// get the full-size version of the image
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
		// set $image = the url
		$image = $image[0];
		$has_image = true;
	}
	if ($has_image == true) {

		// on posts/pages display the featued image
		if(is_singular()){
			echo "<div class='featured-image' style=\"background-image: url('".$image."')\"></div>";
		}
		// on blog/archives display with a link
		else {
			echo "
                <div class='featured-image' style=\"background-image: url('".$image."')\">
                    <a href='" . get_permalink() ."'>" . get_the_title() . "</a>
                </div>
                ";
		}
	}
}

// fix for bug with Disqus saying comments are closed
if ( function_exists( 'dsq_options' ) ) {
    remove_filter( 'comments_template', 'dsq_comments_template' );
    add_filter( 'comments_template', 'dsq_comments_template', 99 ); // You can use any priority higher than '10'
}

// associative array of social media sites
function ct_founder_social_array(){

	$social_sites = array(
		'twitter' => 'founder_twitter_profile',
		'facebook' => 'founder_facebook_profile',
		'google-plus' => 'founder_googleplus_profile',
		'pinterest' => 'founder_pinterest_profile',
		'linkedin' => 'founder_linkedin_profile',
		'youtube' => 'founder_youtube_profile',
		'vimeo' => 'founder_vimeo_profile',
		'tumblr' => 'founder_tumblr_profile',
		'instagram' => 'founder_instagram_profile',
		'flickr' => 'founder_flickr_profile',
		'dribbble' => 'founder_dribbble_profile',
		'rss' => 'founder_rss_profile',
		'reddit' => 'founder_reddit_profile',
		'soundcloud' => 'founder_soundcloud_profile',
		'spotify' => 'founder_spotify_profile',
		'vine' => 'founder_vine_profile',
		'yahoo' => 'founder_yahoo_profile',
		'behance' => 'founder_behance_profile',
		'codepen' => 'founder_codepen_profile',
		'delicious' => 'founder_delicious_profile',
		'stumbleupon' => 'founder_stumbleupon_profile',
		'deviantart' => 'founder_deviantart_profile',
		'digg' => 'founder_digg_profile',
		'git' => 'founder_git_profile',
		'hacker-news' => 'founder_hacker-news_profile',
		'steam' => 'founder_steam_profile',
		'vk' => 'founder_vk_profile',
		'weibo' => 'founder_weibo_profile',
		'tencent-weibo' => 'founder_tencent_weibo_profile',
		'email' => 'founder_email_profile'
	);
	return $social_sites;
}

// used in ct_founder_social_icons_output to return urls
function ct_founder_get_social_url($source, $site){

    if( $source == 'header' ) {
        return get_theme_mod($site);
    } elseif( $source == 'author' ) {
        return get_the_author_meta($site);
    }
}

// output social icons
if( ! function_exists('ct_founder_social_icons_output') ) {
    function ct_founder_social_icons_output($source) {

        // get social sites array
        $social_sites = ct_founder_social_array();

        // store the site name and url
        foreach ( $social_sites as $social_site => $profile ) {

            if( $source == 'header') {

                if ( strlen( get_theme_mod( $social_site ) ) > 0 ) {
                    $active_sites[$social_site] = $social_site;
                }
            }
            elseif( $source == 'author' ) {

                if ( strlen( get_the_author_meta( $profile ) ) > 0 ) {
                    $active_sites[$profile] = $social_site;
                }
            }
        }

        // for each active social site, add it as a list item
        if ( ! empty( $active_sites ) ) {

            echo "<ul class='social-media-icons'>";

            foreach ( $active_sites as $key => $active_site ) {

                if ( $active_site == 'email' ) {
                    ?>
                    <li>
                        <a class="email" target="_blank" href="mailto:<?php echo antispambot( is_email( ct_founder_get_social_url( $source, $key ) ) ); ?>">
                            <span class="screen-reader-text">email icon</span>
                            <i class="fa fa-envelope"></i>
                        </a>
                    </li>
                <?php } elseif ( $active_site == "flickr" || $active_site == "dribbble" || $active_site == "instagram" || $active_site == "soundcloud" || $active_site == "spotify" || $active_site == "vine" || $active_site == "yahoo" || $active_site == "codepen" || $active_site == "delicious" || $active_site == "stumbleupon" || $active_site == "deviantart" || $active_site == "digg" || $active_site == "hacker-news" || $active_site == "vk" || $active_site == 'weibo' || $active_site == 'tencent-weibo' ) { ?>
                    <li>
                        <a class="<?php echo $active_site; ?>" target="_blank" href="<?php echo esc_url( ct_founder_get_social_url( $source, $key ) ); ?>">
                            <span class="screen-reader-text"><?php echo $active_site; ?> icon</span>
                            <i class="fa fa-<?php echo esc_attr( $active_site ); ?>"></i>
                        </a>
                    </li>
                <?php } else { ?>
                    <li>
                        <a class="<?php echo $active_site; ?>" target="_blank" href="<?php echo esc_url( ct_founder_get_social_url( $source, $key ) ); ?>">
                            <span class="screen-reader-text"><?php echo $active_site; ?> icon</span>
                            <i class="fa fa-<?php echo esc_attr( $active_site ); ?>-square"></i>
                        </a>
                    </li>
                <?php
                }
            }
            echo "</ul>";
        }
    }
}

// retrieves the attachment ID from the file URL
function ct_founder_get_image_id($url) {

    // Split the $url into two parts with the wp-content directory as the separator
    $parsed_url  = explode( parse_url( WP_CONTENT_URL, PHP_URL_PATH ), $url );

    // Get the host of the current site and the host of the $url, ignoring www
    $this_host = str_ireplace( 'www.', '', parse_url( home_url(), PHP_URL_HOST ) );
    $file_host = str_ireplace( 'www.', '', parse_url( $url, PHP_URL_HOST ) );

    // Return nothing if there aren't any $url parts or if the current host and $url host do not match
    if ( ! isset( $parsed_url[1] ) || empty( $parsed_url[1] ) || ( $this_host != $file_host ) ) {
        return;
    }

    // Now we're going to quickly search the DB for any attachment GUID with a partial path match
    // Example: /uploads/2013/05/test-image.jpg
    global $wpdb;

    $attachment = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->prefix}posts WHERE guid RLIKE %s;", $parsed_url[1] ) );

    // Returns null if no attachment is found
    return $attachment[0];
}

function ct_founder_profile_image_output(){

    // use User's profile image, else default to their Gravatar
    if(get_the_author_meta('founder_user_profile_image')){

        // get the id based on the image's URL
        $image_id = ct_founder_get_image_id(get_the_author_meta('founder_user_profile_image'));

        // retrieve the thumbnail size of profile image
        $image_thumb = wp_get_attachment_image($image_id, 'thumbnail');

        // display the image
        echo $image_thumb;

    } else {
        echo get_avatar( get_the_author_meta( 'ID' ), 60 );
    }
}

/*
 * WP will apply the ".menu-primary-items" class & id to the containing <div> instead of <ul>
 * making styling difficult and confusing. Using this wrapper to add a unique class to make styling easier.
 */
function ct_founder_wp_page_menu() {
    wp_page_menu(array(
            "menu_class" => "menu-unset"
        )
    );
}

function ct_founder_wp_backwards_compatibility() {

	// not using this function, simply remove it so use of "has_image_size" doesn't break < 3.9
	if( get_bloginfo('version') < 3.9 ) {
		remove_filter( 'image_size_names_choose', 'hybrid_image_size_names_choose' );
	}
}
add_action('init', 'ct_founder_wp_backwards_compatibility');

/*
 * Set the date format for new users.
 * Needs to be done this way so that the date defaults to the right format, but can
 * still be changed from the Settings menu
 */
function ct_founder_set_date_format() {

	// if the date format has never been set by Founder, set it
	if( get_option('ct_founder_date_format_origin') != 'updated' ) {
		update_option('date_format', 'F j, Y');

		// add option so never updates date format again. Allows users to change format.
		add_option('ct_founder_date_format_origin', 'updated');
	}
}
add_action( 'init', 'ct_founder_set_date_format' );

if ( ! function_exists( '_wp_render_title_tag' ) ) :
    function founder_add_title_tag() {
        ?>
        <title><?php wp_title( ' | ' ); ?></title>
    <?php
    }
    add_action( 'wp_head', 'founder_add_title_tag' );
endif;
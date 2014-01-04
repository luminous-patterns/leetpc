<?php
/*
 *  Author: Todd Motto | @toddmotto
 *  URL: html5blank.com | @html5blank
 *  Custom functions, support, custom post types and more.
 */

/*------------------------------------*\
	External Modules/Files
\*------------------------------------*/

// Load any external files you have here

/*------------------------------------*\
	Theme Support
\*------------------------------------*/

if ( !isset( $content_width ) ) $content_width = 900;

if ( function_exists( 'add_theme_support' ) ) {

    add_theme_support( 'menus' );

    add_theme_support( 'post-thumbnails' );
    add_image_size( 'large', 700, '', true );
    add_image_size( 'medium', 250, '', true );
    add_image_size( 'small', 120, '', true );
    add_image_size( 'custom-size', 700, 200, true );

    // Enables post and comment RSS feed links to head
    // add_theme_support( 'automatic-feed-links' );

    // Localisation Support
    load_theme_textdomain('html5blank', get_template_directory() . '/languages');

}

/*------------------------------------*\
	Functions
\*------------------------------------*/

function sort_components( $a, $b ) {
    if ( $a['price_diff'] == $b['price_diff'] ) {
        return 0;
    }
    return ( $a['price_diff'] < $b['price_diff'] ) ? -1 : 1;
}

function component_has_value( $type, $components ) {
    return count( $components[$type] ) > 0;
}

function print_component_options( $type, $components, $defaults, $attrs ) {

    $d = $defaults[$type];

    $cs = array();

    ?>

        <div class="selected <?php if ( count( $components[$type] ) > 1 ) echo 'has-options'; ?>">
            <label>
                <?php echo $defaults[$type]->post_title; ?>
            </label>
            <?php if ( count( $components[$type] ) > 1 ) : ?>
                <button class="secondary change-selection">Change</button>
            <?php else : ?>
                <input type="hidden" name="<?php echo $type; ?>" value="component-<?php echo $d->ID; ?>" data-price-diff="0" />
            <?php endif; ?>
        </div>

    <?php

    if ( count( $components[$type] ) < 2 ) {
        return;
    }

    echo '<div class="options">';

    foreach ( $components[$type] as $c ) : 

        $def_price = intval( $attrs[$d->ID]['price'][0] );
        $price = intval( $attrs[$c->ID]['price'][0] );

        $cs[] = array( 
            'c' => $c,
            'price_diff' => $price - $def_price
        );

    endforeach;

    usort( $cs, 'sort_components' );

    $variable_components = array();

    foreach ( $cs as $x ) : 

        $c = $x['c'];
        $price_diff = $x['price_diff'];

        $checked = $c->ID == $d->ID ? 'checked="checked"' : '';

        ?>

            <div>
                <label>
                    <input type="radio" name="<?php echo $type; ?>" value="component-<?php echo $c->ID; ?>" data-price-diff="<?php echo $price_diff; ?>" <?php echo $checked; ?> />
                    <?php echo $c->post_title; ?>
                <?php if ( $price_diff ) : ?>
                    <span class="price-diff"><?php echo ( $price_diff > 0 ? '+' : '-' ) . '&dollar;' . number_format( abs( $price_diff ), 2 ); ?></span>
                <?php endif; ?>
                </label>
            </div>

        <?php

    endforeach;

    echo '</div>';

}

// Get the product customize form
function customize_product_form( $product_id ) {
    setup_postdata( $GLOBALS['post'] =& get_post( $product_id ) );
    include( 'form-customize.php' );
}

// HTML5 Blank navigation
function html5blank_nav() {
	wp_nav_menu( array(
		'theme_location'  => 'header-menu',
		'menu'            => '', 
		'container'       => 'div', 
		'container_class' => 'menu-{menu slug}-container', 
		'container_id'    => '',
		'menu_class'      => 'menu', 
		'menu_id'         => '',
		'echo'            => true,
		'fallback_cb'     => 'wp_page_menu',
		'before'          => '',
		'after'           => '',
		'link_before'     => '',
		'link_after'      => '',
		'items_wrap'      => '<ul>%3$s</ul>',
		'depth'           => 0,
		'walker'          => ''
	) );
}

// Load HTML5 Blank scripts (header.php)
function html5blank_header_scripts() {

    if ( is_admin() ) return;
    
    wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', '//ajax.googleapis.com/ajax/libs/jquery/1.10.1/jquery.min.js', array(), '1.9.1' );
    wp_enqueue_script( 'jquery' );

    wp_register_script( 'conditionizr', '//cdnjs.cloudflare.com/ajax/libs/conditionizr.js/2.2.0/conditionizr.min.js', array(), '2.2.0' );
    wp_enqueue_script( 'conditionizr' );

    wp_register_script( 'modernizr', '//cdnjs.cloudflare.com/ajax/libs/modernizr/2.6.2/modernizr.min.js', array(), '2.6.2' );
    wp_enqueue_script( 'modernizr' );

    wp_register_script( 'html5blankscripts', get_template_directory_uri() . '/js/scripts.js', array(), '1.0.0' );
    wp_enqueue_script( 'html5blankscripts' );

}

// Load HTML5 Blank styles
function html5blank_styles() {
    wp_register_style( 'normalize', get_template_directory_uri() . '/normalize.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'normalize' );
    wp_register_style( 'leetpcv1main', get_template_directory_uri() . '/css/main.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'leetpcv1main' );
    wp_register_style( 'html5blank', get_template_directory_uri() . '/style.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'html5blank' );
    wp_register_style( 'iwacontactlpc', get_template_directory_uri() . '/css/iwacontact.css', array(), '1.0', 'all' );
    wp_enqueue_style( 'iwacontactlpc' );
}

// Register HTML5 Blank Navigation
function register_html5_menu() {
    register_nav_menus( array(
        'header-menu' => __( 'Header Menu', 'html5blank' ),
        'sidebar-menu' => __( 'Sidebar Menu', 'html5blank' ),
        'extra-menu' => __( 'Extra Menu', 'html5blank' )
    ) );
}

// Remove the <div> surrounding the dynamic navigation to cleanup markup
function my_wp_nav_menu_args( $args = '' ) {
    $args['container'] = false;
    return $args;
}

// Remove Injected classes, ID's and Page ID's from Navigation <li> items
function my_css_attributes_filter( $var ) {
    return is_array( $var ) ? array() : '';
}

// Remove invalid rel attribute values in the categorylist
function remove_category_rel_from_category_list( $thelist ) {
    return str_replace( 'rel="category tag"', 'rel="tag"', $thelist );
}

// Add page slug to body class, love this - Credit: Starkers Wordpress Theme
function add_slug_to_body_class( $classes ) {
    global $post;
    if ( is_home() ) {
        $key = array_search( 'blog', $classes );
        if ($key > -1) {
            unset( $classes[$key] );
        }
    } elseif ( is_page() ) {
        $classes[] = sanitize_html_class( $post->post_name );
    } elseif ( is_singular() ) {
        $classes[] = sanitize_html_class( $post->post_name );
    }

    return $classes;
}

// If Dynamic Sidebar Exists
if ( function_exists( 'register_sidebar' ) ) {

    // Define Sidebar Widget Area 1
    register_sidebar( array(
        'name' => __( 'Widget Area 1', 'html5blank' ),
        'description' => __( 'Description for this widget-area...', 'html5blank' ),
        'id' => 'widget-area-1',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ) );

    // Define Sidebar Widget Area 2
    register_sidebar( array(
        'name' => __( 'Widget Area 2', 'html5blank' ),
        'description' => __( 'Description for this widget-area...', 'html5blank' ),
        'id' => 'widget-area-2',
        'before_widget' => '<div id="%1$s" class="%2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3>',
        'after_title' => '</h3>'
    ) );

}

// Remove wp_head() injected Recent Comment styles
function my_remove_recent_comments_style() {
    global $wp_widget_factory;
    remove_action( 'wp_head', array(
        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
        'recent_comments_style'
    ) );
}

// Pagination for paged posts, Page 1, Page 2, Page 3, with Next and Previous Links, No plugin
function html5wp_pagination() {
    global $wp_query;
    $big = 999999999;
    echo paginate_links( array(
        'base' => str_replace( $big, '%#%', get_pagenum_link( $big ) ),
        'format' => '?paged=%#%',
        'current' => max( 1, get_query_var( 'paged' ) ),
        'total' => $wp_query->max_num_pages
    ) );
}

// Custom Excerpts
function html5wp_index( $length ) {
    return 20;
}

// Create 40 Word Callback for Custom Post Excerpts, call using html5wp_excerpt('html5wp_custom_post');
function html5wp_custom_post( $length ) {
    return 40;
}

// Create the Custom Excerpts callback
function html5wp_excerpt( $length_callback = '', $more_callback = '' ) {
    global $post;
    if ( function_exists( $length_callback ) ) add_filter( 'excerpt_length', $length_callback );
    if ( function_exists( $more_callback ) ) add_filter( 'excerpt_more', $more_callback );
    $output = get_the_excerpt();
    $output = apply_filters( 'wptexturize', $output );
    $output = apply_filters( 'convert_chars', $output );
    $output = '<p>' . $output . '</p>';
    echo $output;
}

// Custom View Article link to Post
function html5_blank_view_article( $more ) {
    global $post;
    return '... <a class="view-article" href="' . get_permalink($post->ID) . '">' . __('View Article', 'html5blank') . '</a>';
}

// Remove Admin bar
function remove_admin_bar() {
    return false;
}

// Remove 'text/css' from our enqueued stylesheet
function html5_style_remove( $tag ) {
    return preg_replace('~\s+type=["\'][^"\']++["\']~', '', $tag);
}

// Remove thumbnail width and height dimensions that prevent fluid images in the_thumbnail
function remove_thumbnail_dimensions( $html ) {
    $html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
    return $html;
}

// Threaded Comments
function enable_threaded_comments() {
    if ( !is_admin() && is_singular() && comments_open() && get_option( 'thread_comments' ) == 1 ) {
        wp_enqueue_script('comment-reply');
    }
}

// Custom Comments Callback
function html5blankcomments( $comment, $args, $depth ) {
	
    $GLOBALS['comment'] = $comment;
	extract( $args, EXTR_SKIP );
	
	if ( 'div' == $args['style'] ) {
		$tag = 'div';
		$add_below = 'comment';
	} else {
		$tag = 'li';
		$add_below = 'div-comment';
	}
?>
    <!-- heads up: starting < for the html tag (li or div) in the next line: -->
    <<?php echo $tag ?> <?php comment_class(empty( $args['has_children'] ) ? '' : 'parent') ?> id="comment-<?php comment_ID() ?>">
	<?php if ( 'div' != $args['style'] ) : ?>
	<div id="div-comment-<?php comment_ID() ?>" class="comment-body">
	<?php endif; ?>
	<div class="comment-author vcard">
	<?php if ($args['avatar_size'] != 0) echo get_avatar( $comment, $args['180'] ); ?>
	<?php printf(__('<cite class="fn">%s</cite> <span class="says">says:</span>'), get_comment_author_link()) ?>
	</div>
<?php if ($comment->comment_approved == '0') : ?>
	<em class="comment-awaiting-moderation"><?php _e('Your comment is awaiting moderation.') ?></em>
	<br />
<?php endif; ?>

	<div class="comment-meta commentmetadata"><a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
		<?php
			printf( __('%1$s at %2$s'), get_comment_date(),  get_comment_time()) ?></a><?php edit_comment_link(__('(Edit)'),'  ','' );
		?>
	</div>

	<?php comment_text() ?>

	<div class="reply">
	<?php comment_reply_link(array_merge( $args, array('add_below' => $add_below, 'depth' => $depth, 'max_depth' => $args['max_depth']))) ?>
	</div>
	<?php if ( 'div' != $args['style'] ) : ?>
	</div>
	<?php endif; ?>
<?php }

/*------------------------------------*\
	Actions + Filters + ShortCodes
\*------------------------------------*/

// Add Actions
add_action( 'init',                  'html5blank_header_scripts' );
add_action( 'get_header',            'enable_threaded_comments' );
add_action( 'wp_enqueue_scripts',    'html5blank_styles' );
add_action( 'init',                  'register_html5_menu' );
add_action( 'widgets_init',          'my_remove_recent_comments_style' );
add_action( 'init',                  'html5wp_pagination' );

// Remove Actions
remove_action( 'wp_head', 'feed_links_extra', 3 );
remove_action( 'wp_head', 'feed_links', 2 );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'index_rel_link' );
remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link', 10, 0 );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'start_post_rel_link', 10, 0 );
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );
remove_action( 'wp_head', 'rel_canonical' );
remove_action( 'wp_head', 'wp_shortlink_wp_head', 10, 0 );

// Add Filters
add_filter( 'body_class',                'add_slug_to_body_class' );
add_filter( 'widget_text',               'do_shortcode' );
add_filter( 'widget_text',               'shortcode_unautop' );
add_filter( 'wp_nav_menu_args',          'my_wp_nav_menu_args' );
add_filter( 'the_category',              'remove_category_rel_from_category_list' );
add_filter( 'the_excerpt',               'shortcode_unautop' );
add_filter( 'the_excerpt',               'do_shortcode' );
add_filter( 'excerpt_more',              'html5_blank_view_article' );
add_filter( 'show_admin_bar',            'remove_admin_bar' );
add_filter( 'style_loader_tag',          'html5_style_remove' );
add_filter( 'post_thumbnail_html',       'remove_thumbnail_dimensions', 10 );
add_filter( 'image_send_to_editor',      'remove_thumbnail_dimensions', 10 );

// Remove Filters
remove_filter( 'the_excerpt', 'wpautop' );

?>

<?php // Opening PHP tag - do not insert any code or spaces before opening tag

// Your php code starts here
function wh_redirect_to_home()
{
    if (! current_user_can('administrator') ) {
    	if (is_user_logged_in() && (is_home() || is_page() || is_single()) && !(count_user_posts( get_current_user_id(), "ait-item"  ) > 0) )
	    {
	        wp_redirect(esc_url(home_url('/wp-admin/post-new.php?post_type=ait-item')));
	        exit();
	    }
    }
    
}

add_action('template_redirect', 'wh_redirect_to_home');

/********************* Include Child Styles and Scripts **************************************/
function my_enqueue_assets() {
	global $wp_query;

	if (is_home() || is_front_page()) {
		wp_enqueue_script( 'contact-owner-homepage',  get_stylesheet_directory_uri() . '/js/contact-owner-homepage.js',array( 'jquery' ), '1.0', true );
	}
    

}

add_action( 'wp_enqueue_scripts', 'my_enqueue_assets' );

// Function to change sender name
function wpb_sender_name( $original_email_from ) {
    return 'Tim Smith';
}
 
// Hooking up our functions to WordPress filters 
add_filter( 'wp_mail_from_name', 'wpb_sender_name' );
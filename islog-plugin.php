<?php
/*
 * Plugin Name: Issue Logger WordPress Plug-in
 * Description: Simplify issue tracking for your company's WordPress website with the Simple Issue Logger WordPress Plugin. This lightweight yet effective tool allows users to effortlessly report concerns directly through your site, helping you address and resolve issues promptly. Whether you run a small business website or a corporate platform, managing user feedback has never been more straightforward.
 * Author: Ian Too
 * Author URI: 'https://www.iantoo.co.ke/'
 * Requires PHP: 8.2
 * Version: 0.0.1-beta
 * 
 * 
 * 
 * {Plugin Name} is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

{Plugin Name} is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with {Plugin Name}. If not, see {URI to Plugin License}.
*/

require plugin_dir_path(__FILE__) . '/vendor/autoload.php';
use Carbon\Carbon;

/* Register custom post types */
function islog_setup_post_types(){
    
    // Add a custom 'issue' post type
    register_post_type('issue', [
        'public'        => true,
        'label'         => 'issue',
        'description'   => 'Record Issues',
        'menu_icon'     => 'dashicons-chart-pie',
        'supports'      => [
            'title', 'editor', 'author', 'excerpt', 'post-formats',
        ],
    ]);

}
// Run the islog_setup_types() hook
add_action('init','islog_setup_post_types');


// Activate the plugin
function islog_activate_plugin(){
    islog_setup_post_types();
    flush_rewrite_rules();

}
// Register the activation hook
register_activation_hook(__FILE__, 'islog_activate_plugin');


// Reverse the process and unregister the custom post type on deactivate
function islog_deactivate_plugin(){
    unregister_post_type('issue');
    flush_rewrite_rules();


}
// Register the deactication hook
register_deactivation_hook(__FILE__, 'islog_deactivate_plugin');

function islog_options_page_html(){
    ?>
    <div class="wrap">
        <h2>IsLog Options Page</h2>
        <p>
            Lorem ipsum dolor sit amet consectetur adipisicing elit. 
            Deserunt id provident reiciendis atque soluta, a sit amet 
            fugiat incidunt! Provident eligendi ullam ratione, est 
            temporibus laudantium impedit quis totam error.
            <?php echo Carbon::now();?>
        </p>

        <form action="options.php" method="post">
            <p>Settings will appear here.</p>
        </form>
    </div>
    <?php
}

function islog_options_page(){
    $hookname = add_menu_page(
        'IsLog',
        'Issue Log',
        'manage_options',
        'islog',
        'islog_options_page_html',
        null,
        20
    );
    add_action( 'load-' . $hookname, 'islog_options_page_submit' );
}

function islog_options_page_submit(){
    // Do something
}

add_action( 'admin_menu', 'islog_options_page' );

// Form in your islog plugin file
function islog_form() {
    ob_start(); 
    
    wp_enqueue_style(plugins_url( '/public/css/style.css', __FILE__ ));
    ?>


    <form id="islog-form" method="post" action="">
        <?php wp_nonce_field('islog_nonce', 'islog_nonce'); ?>
        <!-- Your form fields go here -->
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="submit" value="Submit">
    </form>

    <?php
    return ob_get_clean();
}


// Shortcode to display the form
function islog_shortcode() {
    return islog_form();
}

add_shortcode('islog_form', 'islog_shortcode');

// Form submission handling
function islog_handle_form() {
    if (isset($_POST['islog_nonce']) && wp_verify_nonce($_POST['islog_nonce'], 'islog_nonce')) {
        // Process form data
        $username = sanitize_text_field($_POST['username']);
        $password = sanitize_text_field($_POST['password']);
        // Additional processing...

        // Redirect or display a success message
        wp_redirect(home_url('/success-page'));
        exit;
    }
}

add_action('init', 'islog_handle_form');


class BookCustomPostType {

    /**
     * Constructor. Hooks into WordPress actions to initialize the plugin.
     */
    public function __construct() {
        // Register the custom post type and taxonomy
        add_action('init', array($this, 'register_book_post_type'));
        add_action('init', array($this, 'register_book_taxonomy'));

        // Add shortcode for displaying the latest 5 books
        add_shortcode('latest_books', array($this, 'latest_books_shortcode'));
    }

    /**
     * Register the custom post type 'book'.
     */
    public function register_book_post_type() {
        $labels = array(
            'name'               => _x('Books', 'post type general name', 'your-text-domain'),
            'singular_name'      => _x('Book', 'post type singular name', 'your-text-domain'),
            'menu_name'          => _x('Books', 'admin menu', 'your-text-domain'),
            'name_admin_bar'     => _x('Book', 'add new on admin bar', 'your-text-domain'),
            'add_new'            => _x('Add New', 'book', 'your-text-domain'),
            'add_new_item'       => __('Add New Book', 'your-text-domain'),
            'new_item'           => __('New Book', 'your-text-domain'),
            'edit_item'          => __('Edit Book', 'your-text-domain'),
            'view_item'          => __('View Book', 'your-text-domain'),
            'all_items'          => __('All Books', 'your-text-domain'),
            'search_items'       => __('Search Books', 'your-text-domain'),
            'parent_item_colon'  => __('Parent Books:', 'your-text-domain'),
            'not_found'          => __('No books found.', 'your-text-domain'),
            'not_found_in_trash' => __('No books found in Trash.', 'your-text-domain'),
        );

        $args = array(
            'labels'             => $labels,
            'public'             => true,
            'publicly_queryable' => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'query_var'          => true,
            'rewrite'            => array('slug' => 'book'),
            'capability_type'    => 'post',
            'has_archive'        => true,
            'hierarchical'       => false,
            'menu_position'      => null,
            'supports'           => array('title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments'),
        );

        register_post_type('book', $args);
    }

    /**
     * Register the custom taxonomy 'genre'.
     */
    public function register_book_taxonomy() {
        $labels = array(
            'name'                       => _x('Genres', 'taxonomy general name', 'your-text-domain'),
            'singular_name'              => _x('Genre', 'taxonomy singular name', 'your-text-domain'),
            'search_items'               => __('Search Genres', 'your-text-domain'),
            'popular_items'              => __('Popular Genres', 'your-text-domain'),
            'all_items'                  => __('All Genres', 'your-text-domain'),
            'parent_item'                => null,
            'parent_item_colon'          => null,
            'edit_item'                  => __('Edit Genre', 'your-text-domain'),
            'update_item'                => __('Update Genre', 'your-text-domain'),
            'add_new_item'               => __('Add New Genre', 'your-text-domain'),
            'new_item_name'              => __('New Genre Name', 'your-text-domain'),
            'separate_items_with_commas' => __('Separate genres with commas', 'your-text-domain'),
            'add_or_remove_items'        => __('Add or remove genres', 'your-text-domain'),
            'choose_from_most_used'      => __('Choose from the most used genres', 'your-text-domain'),
            'menu_name'                  => __('Genres', 'your-text-domain'),
        );

        $args = array(
            'hierarchical'          => true,
            'labels'                => $labels,
            'show_ui'               => true,
            'show_admin_column'     => true,
            'query_var'             => true,
            'rewrite'               => array('slug' => 'genre'),
        );

        register_taxonomy('genre', 'book', $args);
    }

    /**
     * Shortcode callback to display the latest 5 books.
     */
    public function latest_books_shortcode() {
        // Query the latest 5 books
        $latest_books_query = new WP_Query(array(
            'post_type'      => 'book',
            'posts_per_page' => 5,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ));

        // Start output buffering
        ob_start();

        // Display the latest books
        if ($latest_books_query->have_posts()) {
            echo '<ul>';
            while ($latest_books_query->have_posts()) {
                $latest_books_query->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>';
            }
            echo '</ul>';
            wp_reset_postdata();
        } else {
            echo 'No books found.';
        }

        // End output buffering and return the content
        return ob_get_clean();
    }
}

// Instantiate the class
$book_custom_post_type = new BookCustomPostType();


<?php 

require get_theme_file_path('/includes/search-route.php');

function starterkit_custom_rest() {
    register_rest_field('post', 'authorName', array(
        'get_callback' => function() {
            return get_the_author();
        }
    ));
}

add_action('rest_api_init', 'starterkit_custom_rest');

function hero($args = NULL) {
    if(!isset($args['title'])) {
        $args['title'] = get_the_title();
    }
    if(!isset($args['subtitle'])) {
        $args['subtitle'] = get_field('hero_subtitle');
    }
    if(!isset($args['image'])) {
        if(get_field('hero_bg_image') AND !is_archive() AND !is_home()) {
            $args['image'] = get_field('hero_bg_image')['sizes']['hero_bg_size'];
        } else {
            $args['image'] = get_theme_file_uri('/images/ocean.jpg');
        }
    }
    ?>
    <div class="page-banner">
        <div class="page-banner__bg-image" 
                style="background-image: url(<?php echo $args['image'] ?>);">
        </div>

        <div class="page-banner__content container container--narrow">
            <h1 class="page-banner__title"><?php echo $args['title'] ?></h1>

            <div class="page-banner__intro">
                <p><?php echo $args['subtitle']; ?></p>
            </div>
        </div>
    </div>
<?php }

// CSS and JS file includes
function starterkit_files() {
    wp_enqueue_script('main-starterkit_js', get_theme_file_uri('/dist/main.min.js'), array('jquery'), 1.0, true);
    wp_enqueue_script('main-starterkit_js', get_theme_file_uri('/build/index.js'), array('jquery'), 1.0, true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('starterkit_main_files', get_theme_file_uri('/dist/main.css'));
    // wp_enqueue_style('starterkit_extra_files', get_theme_file_uri('/build/index.css'));

    wp_localize_script('main-starterkit_js', 'starterkitData', array(
        'root_url' => get_site_url(),
        'nonce' => wp_create_nonce('wp_rest')
    ));
}

add_action('wp_enqueue_scripts', 'starterkit_files');


function starterkit_features() {
    register_nav_menu('headerMenu', 'Header Menu');
    register_nav_menu('footerColOneMenu', 'Footer Column 1 Menu');
    register_nav_menu('footerColTwoMenu', 'Footer Column 2 Menu');
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_image_size('landscape_small', 400, 260, true);
    add_image_size('portrait_medium', 480, 650, true);
    add_image_size('hero_bg_size', 1500, 350, true);
}

add_action('after_setup_theme', 'starterkit_features');


function starterkit_adjust_queries($query) {
    if(!is_admin() AND is_post_type_archive('event') AND $query->is_main_query()) {
        $today = date('Ymd');
        $query->set('meta_key', 'event_date');
        $query->set('orderby', 'meta_value_nam');
        $query->set('order', 'ASC');
        $query->set('meta_query', array(
            array(
                'key'     => 'event_date',
                'compare' => '>=',
                'value'   => $today,
                'type'    => 'numeric'

            )
        ));
    }

    if(!is_admin() AND is_post_type_archive('program') AND $query->is_main_query()) {
        $query->set('orderby', 'title');
        $query->set('order', 'ASC');
        $query->set('posts_per_page', -1);
    }
}

add_action('pre_get_posts', 'starterkit_adjust_queries');

/**
 * Add CSS to ACF admin
 */
function my_acf_admin_enqueue_scripts() {
    // register style
    wp_register_style( 'my-acf-input-css', get_stylesheet_directory_uri() . '/static/css/admin.css', false, '1.0.0' );
    wp_enqueue_style( 'my-acf-input-css' );
}
add_action( 'acf/input/admin_enqueue_scripts', 'my_acf_admin_enqueue_scripts' );


// Redirect subscriber accounts out of admin and onto homepage
add_action('admin_init', 'redirectSubsToFrontend');

function redirectSubsToFrontend() {
    $ourCurrentUser = wp_get_current_user();
    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        wp_redirect(site_url('/'));
        exit;
    }
}

// Remove admin bar for subscribers when logged in
add_action('wp_loaded', 'noSubsAdminBar');

function noSubsAdminBar() {
    $ourCurrentUser = wp_get_current_user();
    if(count($ourCurrentUser->roles) == 1 AND $ourCurrentUser->roles[0] == 'subscriber') {
        show_admin_bar(false);
    }
}

// Customize login screen
add_filter('login_headerurl', 'ourHeaderUrl');

function ourHeaderUrl() {
    return esc_url(site_url('/'));
}

// Add custom CSS to login screen
add_action('login_enqueue_scripts', 'ourLoginCSS');

function ourLoginCSS() {
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('starterkit_main_files', get_theme_file_uri('/dist/main.css'));
    // wp_enqueue_style('starterkit_extra_files', get_theme_file_uri('/build/index.css'));
}

// Change login screen title
add_filter('login_headertitle', 'ourLoginTitle');

function ourLoginTitle() {
    return get_bloginfo('name');
}

// Force note posts to be private
add_filter('wp_insert_post_data', 'makeNotePrivate');

function makeNotePrivate($data) {
    if($data['post_type'] == 'note' AND $data['post_status'] != 'trash') {
        $data['post_status'] = 'private';
    }
    return $data;
}
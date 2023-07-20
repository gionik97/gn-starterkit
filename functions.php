<?php 

// CSS and JS file includes
function starterkit_files() {
    wp_enqueue_script('main-starterkit_js', get_theme_file_uri('/dist/main.min.js'), array('jquery'), 1.0, true);
    wp_enqueue_script('main-starterkit_js', get_theme_file_uri('/build/index.js'), array('jquery'), 1.0, true);
    wp_enqueue_style('custom-google-fonts', '//fonts.googleapis.com/css?family=Roboto+Condensed:300,300i,400,400i,700,700i|Roboto:100,300,400,400i,700,700i');
    wp_enqueue_style('font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
    wp_enqueue_style('starterkit_main_files', get_theme_file_uri('/dist/main.css'));
    // wp_enqueue_style('starterkit_extra_files', get_theme_file_uri('/build/index.css'));
}

add_action('wp_enqueue_scripts', 'starterkit_files');


function starterkit_features() {
    register_nav_menu('headerMenu', 'Header Menu');
    register_nav_menu('footerColOneMenu', 'Footer Column 1 Menu');
    register_nav_menu('footerColTwoMenu', 'Footer Column 2 Menu');
    add_theme_support('title-tag');
}

add_action('after_setup_theme', 'starterkit_features');


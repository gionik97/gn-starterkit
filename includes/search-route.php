<?php

function starterkit_register_search() {
    register_rest_route('gn97/v1', 'search', array(
        'methods' => WP_REST_Server::READABLE,
        'callback' => 'starterkit_search_results'
    ));
}

add_action('rest_api_init', 'starterkit_register_search');

function starterkit_search_results() {
    return 'congrats, you created a route';
}
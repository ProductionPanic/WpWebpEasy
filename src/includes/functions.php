<?php 
/**
 * Actions and filters
 */
function pp_action(string $action, int $priority = 10, int $accepted_args = 1) {
    $action = panic_webp_action_prefix . $action;
    add_action($action, $action, $priority, $accepted_args);   
}

function pp_filter(string $filter, int $priority = 10, int $accepted_args = 1) {
    $filter = panic_webp_action_prefix . $filter;
    add_filter($filter, $filter, $priority, $accepted_args);   
}

function pp_do_action(string $action, ...$args) {
    $action = panic_webp_action_prefix . $action;
    do_action($action, ...$args);
}

function pp_apply_filters(string $filter, ...$args) {
    $filter = panic_webp_action_prefix . $filter;
    apply_filters($filter, ...$args);
}

/**
 * Get plugin metadata
 */
function pp_data(): WebpEasyInfo {
    return new WebpEasyInfo();
}

function pp_is_rest_api_request() {
    if ( empty( $_SERVER['REQUEST_URI'] ) ) {
        // Probably a CLI request
        return false;
    }

    $rest_prefix         = trailingslashit( rest_get_url_prefix() );
    $is_rest_api_request = strpos( $_SERVER['REQUEST_URI'], $rest_prefix ) !== false;

    return apply_filters( 'is_rest_api_request', $is_rest_api_request );
}

function pp_log($data) {
    if(!function_exists('is_user_logged_in')) {
        require_once(ABSPATH . 'wp-includes/pluggable.php');
    }

    if(!is_admin() || !is_user_logged_in() || !current_user_can('manage_options') || pp_is_rest_api_request()) {
        return;
    }

    echo "<script>console.log(" . json_encode($data) . ")</script>";
}
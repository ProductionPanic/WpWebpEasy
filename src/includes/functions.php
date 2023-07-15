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


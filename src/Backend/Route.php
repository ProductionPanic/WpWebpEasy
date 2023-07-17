<?php

namespace ProductionPanic\WebpEasy\Backend;

class Route {
    public static function get($route, $callback, $auth=true) {
        self::register($route, $callback, 'GET', $auth);
    }

    public static function post($route, $callback, $auth=true) {
        self::register($route, $callback, 'POST', $auth);
    }

    public static function register($route, $callback, $methods = 'POST', $auth=true) {
        if(!str_starts_with($route, '/')) {
            $route = '/' . $route;
        }
        add_action('rest_api_init', function() use ($route, $callback, $methods, $auth) {
            register_rest_route('webp-easy/v1', $route, [
                'methods' => $methods,
                'callback' => $callback,
                'permission_callback' => function() use ($auth) {
                    if(!$auth) {
                        return true;
                    }
                    return current_user_can('manage_options');
                },
            ]);
        });
    }

    private static array $localizeData = [];
    private static bool $initialized = false;
    public static function localize($data = []) {
        self::$localizeData = array_merge(self::$localizeData, $data);
        if(self::$initialized) return;
        add_action('admin_enqueue_scripts', function() {
            wp_localize_script('webp-easy', 'WebpEasy', [
                'restUrl' => rest_url('webp-easy/v1'),
                'nonce' => wp_create_nonce('wp_rest'),
                'data' => self::$localizeData,
            ]);
        });
    }
}
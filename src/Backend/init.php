<?php

// add menu item
add_action('admin_menu', function () {
    add_menu_page(
        'Webp Easy',
        'Webp Easy',
        'manage_options',
        'webp-easy',
        function () {
            echo '<div id="webp-easy"></div>';
        },
        'dashicons-admin-generic',
        100
    );

    add_submenu_page(
        'webp-easy',
        'Settings',
        'Settings',
        'manage_options',
        'webp-easy-settings',
        function () {
            echo '<div id="webp-easy" class="webp-easy-settings"></div>';
        }
    );
});

// add js
$buildpath = panic_webp_easy_plugin_dir . 'dist/backend';
$buildurl = panic_webp_easy_plugin_dir_url . 'dist/backend/';
$manifest_path = $buildpath . '/manifest.json';
$manifest = json_decode(file_get_contents($manifest_path), true);
// find entry point
$entryPoints = array_filter($manifest,fn($i) => isset($i['isEntry']));
$entryFiles = array_map(fn($i) => $i['file'], $entryPoints);

// find css
$cssFiles = array_filter($manifest,fn($i) => str_ends_with($i['file'], '.css'));
$cssFiles = array_map(fn($i) => $i['file'], $cssFiles);

add_action('admin_enqueue_scripts', function () use ($entryFiles, $buildurl, $cssFiles) {
    $page = get_current_screen();
    if ($page->base !== 'toplevel_page_webp-easy' && $page->base  !== 'webp-easy_page_webp-easy-settings') {
        return;
    }

    foreach ($entryFiles as $entryFile) {
        $path = $buildurl . $entryFile;
        wp_enqueue_script('webp-easy', $path, [], '1.0.0', true);
    }

    foreach ($cssFiles as $cssFile) {
        $path = $buildurl . $cssFile;
        wp_enqueue_style('webp-easy', $path, [], '1.0.0', 'all');
    }
});

require_once __DIR__ . '/init-rest.php';
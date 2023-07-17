<?php
/**
 * Set up REST API routes. for the wp-admin svelte app
 */
use ProductionPanic\WebpEasy\Backend\Route;
use ProductionPanic\WebpEasy\Modules\WebpData;

Route::get('welcome-message', function () {
    return get_option('webp-easy-welcome-message', 'Welcome to Webp Easy!');
}, auth: false);

Route::get('stats', function() {
    $wp_count_attachments = wp_count_attachments();
    $wp_count_attachments = json_decode(json_encode($wp_count_attachments), true);

    $take = ['image/png', 'image/jpeg', 'image/gif'];
    $total_image_count = 0;
    foreach($take as $mime) {
        $total_image_count += $wp_count_attachments[$mime];
    }

    $all_data = WebpData::get_all();
    return [
        'total_image_count' => $total_image_count,
        'total_webp_count' => $all_data->get_count(),
        'total_webp_compressed' => $all_data->filter(fn(WebpData $i) => $i->compressed)->get_count(),
    ];
});

Route::get('settings', function() {
    return get_option('webp-easy-settings', [
        'webp_enabled' => false,
        'compression_enabled' => false,
        'compression_quality' => 90,
        'autoconvert_new_images' => false,
    ]);
});

Route::post('settings', function(WP_REST_Request $request) {
    $settings = $request->get_json_params();
    $settings_whitelist = [
        'webp_enabled',
        'compression_enabled',
        'compression_quality',
        'autoconvert_new_images',
    ];
    foreach($settings_whitelist as $key) {
        if(!isset($settings[$key])) {
            unset($settings[$key]);
        }
    }
    update_option('webp-easy-settings', $settings);
    return $settings;
});

if(is_admin()) {
    Route::localize([
        'links' => [
            'home' => admin_url('admin.php?page=webp-easy'),
            'settings' => admin_url('admin.php?page=webp-easy-settings'),
        ]
    ]);
}

pp_log(WebpData::get_count());
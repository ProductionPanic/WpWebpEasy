<?php
/**
 * Set up REST API routes. for the wp-admin svelte app
 */
use ProductionPanic\WebpEasy\Backend\Route;
use ProductionPanic\WebpEasy\Backend\WebpMediaLibrary;
use ProductionPanic\WebpEasy\Modules\Image;
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
        'total_image_count' => ['Site images',$total_image_count],
        'total_webp_count' => ['Webp images',$all_data->get_count()],
        'total_webp_compressed' => ['Compressed images',$all_data->filter(fn(WebpData $i) => $i->compressed)->get_count()],
    ];
});

Route::get('settings', function() {
    return pp_get_settings();
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

Route::get('images', function() {
    $images = Image::get_non_converted_images();
    wp_send_json($images);
});

Route::get('image/(?P<id>\d+)', function(WP_REST_Request $request) {
    $id = $request->get_param('id');
    $image = Image::get_non_converted($id);
    if(!$image) {
        return new WP_Error('not_found', 'Image not found', ['status' => 404]);
    }
    return $image;
});

Route::get('images/max/(?P<limit>\d+)/skip/(?P<skip>\d+)', function(WP_REST_Request $request) {
    $limit = $request->get_param('limit');
    $skip = $request->get_param('skip');
    return Image::get_non_converted_images($limit, $skip);
}, auth: false);

Route::post('images/convert', function(WP_REST_Request $request) {
    $ids = $request->get_param('ids');

    if(!is_array($ids)) {
        return new WP_Error('invalid_ids', 'Invalid ids', ['status' => 400]);
    }

    $images = Image::get_non_converted_by_ids($ids);
    foreach($images as $image) {
        $image->process();
    }
});

Route::get('image/convert/(?P<id>\d+)', function(WP_REST_Request $request) {
    $id = $request->get_param('id');
    $image = Image::get_non_converted($id);
    if(!$image) {
        if(WebpMediaLibrary::is_converted($id)) {
            return new WP_Error('already_converted', 'Image already converted', ['status' => 400]);
        }
        return new WP_Error('not_found', 'Image not found', ['status' => 404]);
    }
    $image->process();
    return $image;
});

if(is_admin()) {
    Route::localize([
        'links' => [
            'home' => admin_url('admin.php?page=webp-easy'),
            'settings' => admin_url('admin.php?page=webp-easy-settings'),
        ]
    ]);
}

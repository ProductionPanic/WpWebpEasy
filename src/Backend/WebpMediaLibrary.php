<?php 

namespace ProductionPanic\WebpEasy\Backend;

class WebpMediaLibrary {
    public static function get_image_urls($attachment_id) {
        // get alle image sizes for the attachment
        $image_sizes = get_intermediate_image_sizes();
        // add the full size to the array
        array_unshift($image_sizes, 'full');
        
        $image_urls = array();

        foreach($image_sizes as $image_size) {
            $image_urls[$image_size] = wp_get_attachment_image_url($attachment_id, $image_size);
        }

        return $image_urls;
    }

    public static function get_id_from_url($url) {
        return attachment_url_to_postid($url);
    }
    
    public static function is_converted($attachment_id) {
        $meta_key = 'webp-easy-converted';
        $meta_value = get_post_meta($attachment_id, $meta_key, true);

        return $meta_value === '1';
    }

    public static function set_converted($attachment_id) {
        $meta_key = 'webp-easy-converted';
        $meta_value = '1';

        update_post_meta($attachment_id, $meta_key, $meta_value);
    }

    public static function get_webp_url($attachment_id, $image_size) {
        $image_urls = self::get_image_urls($attachment_id);
        $image_url = $image_urls[$image_size];

        $webp_url = self::get_webp_url_from_image_url($image_url);

        return $webp_url;
    }

    private static function get_webp_url_from_image_url($image_url) {
        $webp_url = preg_replace('/\.(jpg|jpeg|png)$/', '.webp', $image_url);

        $wp_upload_dir = wp_upload_dir();
        $replacement = panic_webp_easy_webp_dir_url;
        $webp_url = str_replace($wp_upload_dir['baseurl'], $replacement, $webp_url);

        return $webp_url;
    }

    public static function get_webp_urls($attachment_id) {
        $image_urls = self::get_image_urls($attachment_id);

        $webp_urls = array();

        foreach($image_urls as $image_size => $image_url) {
            $webp_urls[$image_size] = self::get_webp_url_from_image_url($image_url);
        }

        return $webp_urls;
    }

    public static function get_webp_urls_from_url($url) {
        $attachment_id = self::get_id_from_url($url);

        return self::get_webp_urls($attachment_id);
    }
}
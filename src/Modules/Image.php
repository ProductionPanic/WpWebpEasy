<?php 

namespace ProductionPanic\WebpEasy\Modules;
use CodesVault\Howdyqb\DB;

class Image {
    private function __construct(
        public int $id,
        public string $mime_type,
        public string $url,
        public string $thumbnail_url = '',
        public string $name = '',
    ) {
    }
    private static function parse_row($row) {
        $img = new Image($row->id, $row->mime_type, $row->url);
        $img->thumbnail_url = wp_get_attachment_image_url($row->id, 'thumbnail');
        $img->name = get_the_title($row->id);
        return $img;
    }
    public static function get_non_converted_images($max = -1, $skip=0) {
        global $wpdb;

        $query = "SELECT post_mime_type as mime_type, ID as id, guid as url FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type IN ('image/png', 'image/jpeg', 'image/gif') AND ID NOT IN (SELECT attachment_id FROM {$wpdb->prefix}webp_easy_webpdata) ORDER BY ID ASC";

        if($max > 0) {
            $query .= " LIMIT {$max}";
        }

        if($skip > 0) {
            $query .= " OFFSET {$skip}";
        }

        $images = $wpdb->get_results($query);
        return array_map(fn($i) => self::parse_row($i), $images);
    }

    public static function get_non_converted(int $attachment_id) {
        global $wpdb;
        $query = "SELECT post_mime_type as mime_type, ID as id, guid as url FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type IN ('image/png', 'image/jpeg', 'image/gif') AND ID = {$attachment_id} AND ID NOT IN (SELECT attachment_id FROM {$wpdb->prefix}webp_easy_webpdata) ORDER BY ID ASC";
        $image = $wpdb->get_row($query);
        if(!$image) {
            return null;
        }
        return self::parse_row($image);
    }

    public static function get_non_converted_by_ids(array $attachment_ids) {
        global $wpdb;
        $query = "SELECT post_mime_type as mime_type, ID as id, guid as url FROM {$wpdb->prefix}posts WHERE post_type = 'attachment' AND post_mime_type IN ('image/png', 'image/jpeg', 'image/gif') AND ID IN (".implode(',', $attachment_ids).") AND ID NOT IN (SELECT attachment_id FROM {$wpdb->prefix}webp_easy_webpdata) ORDER BY ID ASC";
        $images = $wpdb->get_results($query);
        return array_map(fn($i) => self::parse_row($i), $images);
    }

    public function process() {
        return WebpData::create($this->id);
    }
}
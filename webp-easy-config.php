<?php 

class WebpEasyInfo {
    public string $name="Name";
    public string $version="Version";
    public string $title="Title";
    public string $requires_php="RequiresPHP";
    public string $requires_wp="RequiresWP";
    
    private function __construct() {
        $metadata = get_plugin_metadata( __FILE__ );
        $keys = array_keys(get_object_vars($this));
        array_map(fn($key) => $this->$key = $metadata[$key], $keys);
    }

    public static function get() {
        return new WebpEasyInfo();
    }
}
/**
 * Define constants.
 */
define('panic_webp_easy_version', WebpEasyInfo::get()->version);
// upload dir
define('panic_webp_easy_webp_dir', wp_upload_dir() . '/webp-easy');
define('panic_webp_easy_webp_dir_url', wp_upload_dir()['baseurl'] . '/webp-easy');
// plugin dir
define('panic_webp_easy_plugin_dir', plugin_dir_path( __FILE__ ));
define('panic_webp_easy_plugin_dir_url', plugin_dir_url( __FILE__ ));
// settings
define('panic_webp_easy_quality', 'panic_webp_easy_quality');
define('panic_webp_easy_quality_default', 80);
define('panic_webp_easy_active', 'panic_webp_easy_active');
define('panic_webp_easy_active_default', true);

define('panic_webp_action_prefix', 'panic_webp_easy_');




<?php

namespace ProductionPanic\WebpEasy\Modules;
use CodesVault\Howdyqb\DB;
use ProductionPanic\WebpEasy\Backend\Logging;
use ProductionPanic\WebpEasy\Backend\WebpConverter;
use ProductionPanic\WebpEasy\Backend\WebpMediaLibrary;

class WebpData {
    public static string $db_name = 'webp_easy_webpdata';
    private static DB $db;

    public readonly int $attachment_id;
    public bool $compressed;
    public string $webp_url;
    public string $size_urls;
    public string $created_at;
    public string $updated_at;
    

    private function __construct() {
    }

    public static function init() {
        self::init_db();
    }
    private static function init_db() {
        if(isset(self::$db)) {
           return;
        }

        DB::create(self::$db_name)
            ->column('attachment_id')->bigInt()->unsigned()->required()->primary()
            ->column('compressed')->boolean()->required()
            ->column('webp_url')->text()->required()
            ->column('size_urls')->text()->required()
            ->column('created_at')->dateTime()->required()
            ->column('updated_at')->dateTime()->required()
            ->index(['attachment_id'])
            ->execute();

        self::$db = new DB();
    }
    
    private static function db() {
        self::init_db();
        return self::$db;
    }

    private static function create_entry(int $attachment_id, bool $compressed, string $webp_url, string $size_urls) {
        self::db()::insert(self::$db_name, [[
            'attachment_id' => $attachment_id,
            'compressed' => $compressed,
            'webp_url' => $webp_url,
            'size_urls' => $size_urls,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]]);
        pp_do_action('webp-easy/webp-data/created', $attachment_id, $compressed, $webp_url);

        return self::get($attachment_id);
    }

    public static function create(int $attachment_id) {
        $settings = pp_get_settings();
        $converter = new WebpConverter();

        if(WebpMediaLibrary::is_converted($attachment_id)) {
            Logging::error('Webp data already exists for attachment', ['attachment_id' => $attachment_id]	);
            return;
        }

        $inputs = WebpMediaLibrary::get_paths($attachment_id);
        $outputs = array();

        foreach($inputs as $image_size => $item) {
            $input = $item[0];
            $output = $item[1];
            $o = $converter->convert($input, $output,$settings['compression_enabled'] ? $settings['compression_quality'] : 100);

            if ($o === null) {
                continue;
            }
            $outputs[$image_size] = WebpMediaLibrary::path_to_url($output);
        }

        if(count($outputs) === 0) {
            return;
        }

        $webp_url = $outputs['full'];

        return self::create_entry($attachment_id, $settings['compression_enabled'], $webp_url, implode(',', $outputs));       
    }

    public static function delete(int $attachment_id) {
        self::db()::delete(self::$db_name)
            ->where('attachment_id', '=', $attachment_id)
            ->execute();
    }

    public static function get(int $attachment_id) {
        $results = self::db()::select('*')
            ->from(self::$db_name)
            ->where('attachment_id', '=', $attachment_id)
            ->limit(1)
            ->get();

        if(count($results) === 0) {
            return null;
        }

        $result = $results[0];
        $webp_data = new WebpData();
        $webp_data->attachment_id = $result['attachment_id'];
        $webp_data->compressed = $result['compressed'];
        $webp_data->webp_url = $result['webp_url'];
        $webp_data->size_urls = $result['size_urls'];
        $webp_data->created_at = $result['created_at'];
        $webp_data->updated_at = $result['updated_at'];
        return $webp_data;
    }

    public static function get_count() {
        return self::db()::select('COUNT(*)')
            ->from(self::$db_name)
            ->get()[0]['COUNT(*)'];
    }

    public static function where(array $conditions) {
        $query = self::db()::select('*')
            ->from(self::$db_name);

        foreach($conditions as $condition) {
            $query->where($condition[0], $condition[1], $condition[2]);
        }

        $results = $query->get();

        $webp_datas = [];
        foreach($results as $result) {
            $webp_data = new WebpData();
            $webp_data->attachment_id = $result['attachment_id'];
            $webp_data->compressed = $result['compressed'];
            $webp_data->webp_url = $result['webp_url'];
            $webp_data->created_at = $result['created_at'];
            $webp_data->updated_at = $result['updated_at'];
            $webp_datas[] = $webp_data;
        }
        return WebpDataCollection::get_instance($webp_datas);
    }

    public static function get_all() {
        $results = self::db()::select('*')
            ->from(self::$db_name)
            ->get();

        $webp_datas = [];
        foreach($results as $result) {
            $webp_data = new WebpData();
            $webp_data->attachment_id = $result['attachment_id'];
            $webp_data->compressed = $result['compressed'];
            $webp_data->webp_url = $result['webp_url'];
            $webp_data->created_at = $result['created_at'];
            $webp_data->updated_at = $result['updated_at'];
            $webp_datas[] = $webp_data;
        }
        return WebpDataCollection::get_instance($webp_datas);
    }
    
    public static function update(int $attachment_id, array $data) {
        if(isset($data['attachment_id'])) {
            unset($data['attachment_id']);
        }
        if(isset($data['created_at'])) {
            unset($data['created_at']);
        }
        // set updated_at
        $data['updated_at'] = date('Y-m-d H:i:s');
        self::db()::update(self::$db_name, $data)
            ->where('attachment_id', '=', $attachment_id)
            ->execute();
    }

    public function save() {
        $data = [
            'compressed' => $this->compressed,
            'webp_url' => $this->webp_url,
            'updated_at' => 'CURRENT_TIMESTAMP'
        ];
        self::update($this->attachment_id, $data);
    }

    public function remove() {
        self::delete($this->attachment_id);
    }



}
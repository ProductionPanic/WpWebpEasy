<?php

namespace ProductionPanic\WebpEasy\Modules;
use CodesVault\Howdyqb\DB;

class WebpData {
    private static string $db_name = 'webp_easy_webpdata';
    private static DB $db;

    public readonly int $attachment_id;
    public bool $compressed;
    public string $webp_url;
    public string $created_at;
    public string $updated_at;
    

    private function __construct() {
    }

    private static function init_db() {
        if(isset(self::$db)) {
           return;
        }

        DB::create(self::$db_name)
            ->column('attachment_id')->bigInt()->unsigned()->required()->primary()
            ->column('compressed')->boolean()->required()
            ->column('webp_url')->text()->required()
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

    public static function create(int $attachment_id, bool $compressed, string $webp_url) {
        self::db()::insert(self::$db_name, [[
            'attachment_id' => $attachment_id,
            'compressed' => $compressed,
            'webp_url' => $webp_url,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]]);
        pp_do_action('webp-easy/webp-data/created', $attachment_id, $compressed, $webp_url);
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
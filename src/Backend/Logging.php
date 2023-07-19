<?php

namespace ProductionPanic\WebpEasy\Backend;

use CodesVault\Howdyqb\DB;

class Logging {
    private function __construct() {
        DB::create('webp_easy_logging')
            ->column('id')->bigInt()->unsigned()->required()->primary()->autoIncrement()
            ->column('message')->text()->required()
            ->column('data')->text()->required()
            ->column('level')->text()->required()
            ->column('created_at')->dateTime()->required()
            ->index(['id'])
            ->execute();
    }

    public static function init() {
        new self();
    }

    public static function log(string $message, array $data = []) {
        DB::insert('webp_easy_logging', [[
            'message' => $message,
            'data' => json_encode($data),
            'level' => 'info',
            'created_at' => date('Y-m-d H:i:s'),
        ]]);
    }

    public static function error(string $message, array $data = []) {
        DB::insert('webp_easy_logging', [[
            'message' => $message,
            'data' => json_encode($data),
            'level' => 'error',
            'created_at' => date('Y-m-d H:i:s'),
        ]]);
    }

    public static function get_logs() {
        return DB::select('webp_easy_logging', ['*'])->get();
    }

    public static function get_log(int $id) {
        return DB::select('webp_easy_logging', ['*'])->where('id', $id)->first();
    }   

    public static function delete_log(int $id) {
        return DB::delete('webp_easy_logging')->where('id', $id)->execute();
    }

    public static function delete_logs() {
        return DB::delete('webp_easy_logging')->execute();
    }
}
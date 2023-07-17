<?php

namespace ProductionPanic\WebpEasy\Modules;

class WebpDataCollection {
    private array $webp_datas = [];

    /**
     * @param array $webp_datas
     */
    public function __construct(array $webp_datas) {
        $this->webp_datas = $webp_datas;
    }

    /**
     * @param array $webp_data
     * @return WebpDataCollection
     */
    public static function get_instance(array $webp_data) {
        return new WebpDataCollection($webp_data);
    }

    public function items() {
        return $this->webp_datas;
    }
    public function item(int $index) {
        if(!isset($this->webp_datas[$index])) {
            return null;
        }
        return $this->webp_datas[$index];
    }

    public function get(int $attachment_id) {
        foreach ($this->webp_datas as $webp_data) {
            if($webp_data->attachment_id === $attachment_id) {
                return $webp_data;
            }
        }
        return null;
    }
    
    public function get_count() {
        return count($this->webp_datas);
    }

    public function find(callable $callback) {
        foreach ($this->webp_datas as $webp_data) {
            if($callback($webp_data)) {
                return $webp_data;
            }
        }
        return null;
    }

    public function filter(callable $callback) {
        $filtered = [];
        foreach ($this->webp_datas as $webp_data) {
            if($callback($webp_data)) {
                $filtered[] = $webp_data;
            }
        }
        return new WebpDataCollection($filtered);
    }

    public function map(callable $callback) {
        $mapped = [];
        foreach ($this->webp_datas as $webp_data) {
            $mapped[] = $callback($webp_data);
        }
        return $mapped;
    }

    public function reduce(callable $callback, $initial = null) {
        $accumulator = $initial;
        foreach ($this->webp_datas as $webp_data) {
            $accumulator = $callback($accumulator, $webp_data);
        }
        return $accumulator;
    }

    public function sort(callable $callback) {
        $sorted = $this->webp_datas;
        usort($sorted, $callback);
        return new WebpDataCollection($sorted);
    }

    public function sort_by(string $key) {
        $sorted = $this->webp_datas;
        usort($sorted, function($a, $b) use ($key) {
            return $a->{$key} <=> $b->{$key};
        });
        return new WebpDataCollection($sorted);
    }
}
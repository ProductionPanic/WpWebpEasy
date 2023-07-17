<?php

namespace ProductionPanic\WebpEasy\Backend;

class WebpConverter {
    private function useFallback(): bool {
        // check if the exec function is disabled and if not check if the cwebp binary is available
        if(!function_exists('exec')) {
            return false;
        }
        
        $command = "which cwebp";

        exec($command, $output, $return_var);

        if($return_var !== 0) {
            return false;
        }

        return true;
    }

    public function convert($input_path, $output_path, $quality) {
        if(!$this->valid_input($input_path)) {
            return false;
        }
        if(!$this->useFallback()) {
            return $this->convertWithCwebp($input_path, $output_path, $quality);
        } else {
            return $this->convertWithImageFunction($input_path, $output_path, $quality);
        }
    }

    private function convertWithCwebp($input_path, $output_path, $quality) {
        $command = "cwebp -q $quality $input_path -o $output_path";

        exec($command, $output, $return_var);

        if($return_var !== 0) {
            return false;
        }

        return true;
    }

    private function convertWithImageFunction($input_path, $output_path, $quality=80) {
        $is_png = mime_content_type($input_path) === 'image/png';
        
        if($is_png) {
            $image = imagecreatefrompng($input_path);
            imagepalettetotruecolor( $image );
            imagealphablending( $image, true );
            imagesavealpha( $image, true );
        } else {
            $image = imagecreatefromjpeg($input_path);
        }

        $output_dir = dirname($output_path);
        // if the output directory does not exist, create it recursively
        if(!file_exists($output_dir)) {
            mkdir($output_dir, 0777, true);
        }


        $result = imagewebp($image, $output_path, $quality);

        imagedestroy($image);

        return $result;        
    }

    private function valid_input($input_path) {
        if(!file_exists($input_path)) {
            return false;
        }

        $mime_type = mime_content_type($input_path);

        if($mime_type !== 'image/jpeg' && $mime_type !== 'image/png') {
            return false;
        }

        return true;
    }
}
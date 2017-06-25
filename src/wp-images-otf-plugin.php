<?php
    /*
        *************************************************************************

        Plugin Name:  WP Images OTF Plugin
        Plugin URI:   https://github.com/adam-prohack/WP-Images-OTF-Plugin
        Description:  This plugin allow you to create attachment image in custom size, only when you need this size
        Version:      1.0.0
        Author:       Adam Brzozowski
        Text Domain:  wp-images-otf

        Copyright (C) 2017 Adam Brzozowski

        *************************************************************************
    */

    if(function_exists("get_otf_image_url")) return;

    function get_otf_image_url($image_id, $size_name, $width = 0, $height = 0, $crop_mode = ["center", "center"], $result_quality = 70){        
        try{
            $result_mime_type = "image/jpeg";
            $attachment_mime_type = get_post_mime_type($image_id);
            $attachment_metadata = wp_get_attachment_metadata($image_id);

            if(preg_match('/image\/svg/', $attachment_mime_type)) return wp_get_attachment_url($attachment_mime_type);
            else if(!preg_match('/image/', $attachment_mime_type)) return;
            else if(array_key_exists($size_name, $attachment_metadata["sizes"])){ return wp_get_attachment_image_url($image_id, $size_name); }
            
            $original_image_path = get_attached_file($image_id);
                    
            $image_editor = wp_get_image_editor($original_image_path);
            $image_editor->resize($width, $height, $crop_mode);
            $image_editor->set_quality($result_quality);
            $output_file_path = $image_editor->generate_filename(null, null, "jpg");
            $image_editor->save($output_file_path, $result_mime_type);
                
            $attachment_metadata["sizes"][$size_name] = [
                "file" => substr($output_file_path, strrpos($output_file_path, DIRECTORY_SEPARATOR)),
                "width" => $width,
                "height" => $height,
                "mime-type" => $result_mime_type,
                "quality" => $result_quality
            ];
            update_post_meta($image_id, "_wp_attachment_metadata", $attachment_metadata);
            return wp_get_attachment_image_url($image_id, $size_name);
        }
        catch(Exception $exception){ return wp_get_attachment_image_url($image_id, "full"); }
    }

?>
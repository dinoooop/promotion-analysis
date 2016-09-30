<?php

namespace App;

class Merge {

    private $shuffle_ads;

    function __construct() {
        
    }

    function get_ads($location) {
        $ads = Ad::where('ad_location', $location)->get();
        return $ads;
    }

    function set_shuffle($location) {
        Session::set('shuffle_ads_key', -1);
        $ads = Ad::where('ad_location', $location)->get()->toArray();
        shuffle($ads);
        Session::set('shuffle_ads', $ads);
    }

    function get_rand_ad($location) {

        $ads = Session::get('shuffle_ads');
        $shuffle_ads_key = Session::get('shuffle_ads_key');
        Session::set('shuffle_ads_key', ++$shuffle_ads_key);
        if (!isset($ads[$shuffle_ads_key])) {
            $ad_placeholders = Stock::get('ad_location_size');
            return array(
                'url' => '#',
                'ad_file' => $ad_placeholders[$location]['placeholder'],
            );
        }

        return $ads[$shuffle_ads_key];
    }

    function prepare_video_loop($record) {
        if(empty($record)){
            return false;
        }

        $pathinfo = pathinfo($record['file']);

        $video['src'] = Config::get('constants.default_upload_dir_url') . $this->get_video_thumb_file_name($record['file']);
        $video['video_file_url'] = Config::get('constants.default_upload_dir_url') . $record['file'];
        $video['url'] = url('watch/' . $record['slug']);
        $record['excerpt'] = ($record['description'] == '') ? '...' : $record['description'];
        $video['excerpt'] = str_limit($record['excerpt'], 28);
        return array_merge($record, $video);
    }

    function get_video_thumb_file_name($file) {
        $video_thumb_file_prefix = 'video-thumb-';
        $pathinfo = pathinfo($file);
        return $video_thumb_file_prefix . $pathinfo['filename'] . '.jpg';
    }

}

<?php
namespace App\Repositories;

use App\Models\Video;
use App\Models\VideoTemplate;


class VideoTemplateRepository
{
    public function get($vid)
    {
        $video = VideoTemplate::query()->where('id', $vid)->first();
        return $video;
    }

    public function delete($vid)
    {
        return VideoTemplate::query()->where('id', $vid)->delete();
    }

    public function update($vid, $attributes)
    {
        return VideoTemplate::query()->where('id', $vid)->update($attributes);
    }

//    public function fetchSettings($attributes, $vid)
//    {
//        $titles = explode("\n", array_get($attributes, 'titles', ''));
//        $images = explode("\n", array_get($attributes, 'images', ''));
//        $inserted = [];
//
//        if (count($titles) > count($images)) {
//            foreach ($titles as $idx => $title) {
//                $inserted[] = [
//                    'title' => $title,
//                    'image' => array_get($images, $idx, ''),
//                    'video_id' => $vid
//                ];
//            }
//        } else {
//            foreach ($images as $idx => $image) {
//                $inserted[] = [
//                    'image' => $image,
//                    'title' => array_get($titles, $idx, ''),
//                    'video_id' => $vid
//                ];
//            }
//        }
//        return $inserted;
//    }

    public function create($attributes)
    {
        $video = VideoTemplate::create($attributes);

        return $video;
    }

    public function all()
    {
        $video = VideoTemplate::query()->get()->toArray();
        return $video;
    }

}
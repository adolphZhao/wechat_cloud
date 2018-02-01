<?php
namespace App\Repositories;

use App\Models\Video;
use App\Models\VideoConfig;

/**
 * Created by PhpStorm.
 * User: bailiqiang
 * Date: 2018/1/17
 * Time: 下午6:13
 */
class VideoRepository
{
    public function get($vid)
    {
        $video = Video::query()->where('id', $vid)->first();
        return $video;
    }

    public function getByMapId($vid)
    {
        $video = Video::query()->where('map_id', $vid)->first();
        return $video;
    }

    public function delete($vid)
    {
        VideoConfig::query()->where('video_id', $vid)->delete();
        return Video::query()->where('id', $vid)->delete();
    }

    public function update($vid, $attributes)
    {
        return Video::query()->where('id', $vid)->update($attributes);
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
        $video = Video::create($attributes);

        return $video;
    }

    public function all()
    {
        $video = Video::query()->get()->toArray();
        return $video;
    }

}
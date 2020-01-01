<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Video;

class VideosTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $videos = [
            ["user_id" => 1, 
            "title"=> "Chuyện Người Anh Thương",
            "link" => "https://youtu.be/QMpDF4opxyQ",
            "image" => 
            "upload/video/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:26:33",
            "updated_at"=> "2019-08-28 01:36:33",
            ],
            ["user_id" => 1, 
            "title"=> "Có Ai Thương Em Như Anh",
            "link" => "https://youtu.be/QMpDF4opxyQ",
            "image" => 
            "upload/video/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:26:33",
            "updated_at"=> "2019-08-28 01:36:33",
            ],
            ["user_id" => 1, 
            "title"=> "NHỮNG BẢN ACOUSTIC",
            "link" => "https://youtu.be/QMpDF4opxyQ",
            "image" => 
            "upload/video/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:26:33",
            "updated_at"=> "2019-08-28 01:36:33",
            ],
            ["user_id" => 1, 
            "title"=> "Chỉ Hôm Nay Thôi...",
            "link" => "https://youtu.be/QMpDF4opxyQ",
            "image" => 
            "upload/video/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:26:33",
            "updated_at"=> "2019-08-28 01:36:33",
            ],
            ["user_id" => 1, 
            "title"=> "Bầu trời năm ấy không xanh mãi.",
            "link" => "https://youtu.be/QMpDF4opxyQ",
            "image" => 
            "upload/video/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:26:33",
            "updated_at"=> "2019-08-28 01:36:33",
            ],
        ];
            
        foreach ($videos as $key => $video) {
            Video::create($video);
        }
    }
}

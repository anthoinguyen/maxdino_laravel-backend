<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Comment;

class CommentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        $comments = [
            ["user_id" => 1, 
            "ask_id" => 1,
            "content"=> "Cái flow Authorization Code không đáp ứng được mong muốn của bác rồi. Bác tìm hiểu thử cái flow Implicit grant thử xem. Docs của cái flow này ở Laravel Passport này bác.",
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
            ["user_id" => 1, 
            "ask_id" => 1,
            "content"=> "ý bác là gì, là thuật toán để generate ra token ? Laravel Passport dùng thư viện này https://github.com/lcobucci/jwt.",
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
            ["user_id" => 1, 
            "ask_id" => 1,
            "content"=> "Họ quay màn hình xuất ra video, sau đó chuyển video sang gif.",
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
            ["user_id" => 1, 
            "ask_id" => 2,
            "content"=> "bạn thử search gif capture for window xem, có rất nhiều tool có thể quay lại màn hình và xuất ra gif nhé bạn",
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
        ];

        foreach ($comments as $key => $comment) {
            Comment::create($comment);
        }
    }
}

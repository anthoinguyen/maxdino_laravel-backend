<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Ask;

class AsksTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $posts = [
            ["user_id" => 1, 
            "content" => 
            "Mình đang sử dụng Passport để làm API gen ra access_token cho nhiều client (mục đích là để làm SSO). Mình có đọc được thì Passport nó có Authorization Code để gen ra access_token, nhưng cái Authorization Code này mình đang chưa tìm được cách custom để tự gen ra và trả kết quả qua API response.",
            "image" => 
            "upload/ask/image/default.png",
            "created_at"=> "2019-08-28 01:24:33",
            "updated_at"=> "2019-08-28 01:34:33",
            ],
            ["user_id" => 1, 
            "content" => 
            "Chào các bạn, Mình vào link sau: https://medium.com/@weehong/laravel-5-7-vue-vue-router-spa-5e07fd591981 Mình thấy tác giả làm ảnh (Installing Laravel) đẹp và mượt quá, bình thường mình chỉ sử dụng photoshop để làm. => Trong link họ làm bằng phần mềm gì vậy các bạn?",
            "image" => 
            "upload/ask/image/default.png",
            "created_at"=> "2019-08-28 01:24:34",
            "updated_at"=> "2019-08-28 01:34:34",
            ],
            ["user_id" => 1, 
            "content" => 
            "Hi everyone. Mình có làm việc với Android củ thể xử lý hình ảnh, đầu vào là một hình ảnh chứa ngôn ngữ tiếng việt (một đoạn văn, bài thơ)... Sau khi scan sẽ trả về một đoạn test (Có dấu hoặc không dấu). Mình có sử dụng api của google (google vision, ml kit), tess-two. Khi quét trả ra tiếng việt đều bị lỗi. Bạn nào có làm việc với các công cụ khác xử lý tiếng việt ok xíu mong giúp đỡ. Thanks all",
            "image" => 
            "upload/ask/image/default.png",
            "created_at"=> "2019-08-28 01:24:35",
            "updated_at"=> "2019-08-28 01:34:35",
            ],
            ["user_id" => 2, 
            "content" => 
            "Như tiêu đề, Em muốn hỏi về phần set IME ( Input method editor) cho textinput trong angular (Có thể setting ngôn ngữ theo ý muốn) . Nhờ mọi người giúp với ạ .

            Em cảm ơn !",
            "image" => 
            "upload/ask/image/default.png",
            "created_at"=> "2019-08-28 01:24:36",
            "updated_at"=> "2019-08-28 01:34:36",
            ],
            ["user_id" => 3, 
            "content" => 
            "Hi everyone. Mình có làm việc với Android củ thể xử lý hình ảnh, đầu vào là một hình ảnh chứa ngôn ngữ tiếng việt (một đoạn văn, bài thơ)... Sau khi scan sẽ trả về một đoạn test (Có dấu hoặc không dấu). Mình có sử dụng api của google (google vision, ml kit), tess-two. Khi quét trả ra tiếng việt đều bị lỗi. Bạn nào có làm việc với các công cụ khác xử lý tiếng việt ok xíu mong giúp đỡ. Thanks all
            ",
            "image" => 
            "upload/ask/image/default.png",
            "created_at"=> "2019-08-28 01:24:37",
            "updated_at"=> "2019-08-28 01:34:37",
            ],
            ["user_id" => 4, 
            "content" => 
            "Chào mọi người, mình là feshman tester. Mọi người có thể cung cấp cho mình một ví dụ đơn giản và dễ hiểu để thực hiện trên tool Jmeter không ạ",
            "created_at"=> "2019-08-28 01:24:38",
            "updated_at"=> "2019-08-28 01:34:38",
            ],
        ];
            
        foreach ($posts as $key => $post) {
            Ask::create($post);
        }
    }
}

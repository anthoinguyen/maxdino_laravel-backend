<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Learn;

class LearnsTableSeeder extends Seeder
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
            "title" => 
            "Phi hành gia robot đầu tiên lái tàu không gian",
            "content" => 
            "Skybot F850 tự mình lái tàu Soyuz cập bến Trạm Vũ trụ Quốc tế (ISS) vào ngày 27/8. Đây là phi hành gia robot đầu tiên trên thế giới làm được việc này. Ngày 22/8, tàu vũ trụ Soyuz được phóng đi từ Kazakhstan mang theo phi hành gia duy nhất, robot hình người có tên Skybot F850. Đây là chuyến bay thử nghiệm không cần phi hành đoàn, có nhiệm vụ ghép nối và đưa robot lên ISS.",
            "image" => 
            "upload/learn/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:24:12",
            "updated_at"=> "2019-08-28 01:34:12",
            ],
            ["user_id" => 1, 
            "title" => 
            "Thủ tướng Nguyễn Xuân Phúc chúc mừng chức vô địch",
            "content" => 
            "Ngay sau chiến thắng nghẹt thở của đội tuyển nữ Việt Nam trước tuyển Thái Lan trong trận chung kết giải vô địch bóng đá nữ 2019 vào tối qua (27.8), Thủ tướng Nguyễn Xuân Phúc đã nhắn gửi qua Phó Chủ tịch Liên đoàn Bóng đá Việt Nam (VFF) Trần Quốc Tuấn, lời chúc mừng tốt đẹp nhất của ông dành cho các cô gái vàng Việt Nam.",
            "image" => 
            "upload/learn/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:24:13",
            "updated_at"=> "2019-08-28 01:34:13",
            ],
            ["user_id" => 1, 
            "title" => 
            "Tổng Bí thư, Chủ tịch nước Nguyễn Phú Trọng tiếp Thủ tướng Malaysia",
            "content" => 
            "Sáng 28/8/2019, tại Trụ sở Trung ương Đảng, Tổng Bí thư, Chủ tịch nước Nguyễn Phú Trọng tiếp Thủ tướng Malaysia Mahathir Mohamad thăm chính thức Việt Nam.",
            "image" => 
            "upload/learn/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:24:14",
            "updated_at"=> "2019-08-28 01:34:14",
            ],
            ["user_id" => 1, 
            "title" => 
            "ReactJs - Viết một Validator class đơn giản dùng để validate form",
            "content" => 
            "Trước kia khi validate form bên phía client side mình thưởng sử dụng plugin jquery-validation, tuyên nhiên khi làm việc với ReactJs, dự liệu form thược được chứa trong state, vì vậy sử dụng jquery-validation không còn hợp lý nữa, việc sử dụng jQuery trong code ReactJs cũng không phải là một cách làm hay. Trong bài viết này mình sẽ hướng dẫn các bạn viết một class dùng để validate form trong ReactJs có thể sử dụng lại cho nhiều trường hợp.",
            "image" => 
            "upload/learn/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:24:15",
            "updated_at"=> "2019-08-28 01:34:15",
            ],
            ["user_id" => 1, 
            "title" => 
            "Xây dựng ứng dụng thêm sửa xóa với Vue, Vuex",
            "content" => 
            "Ai đã từng theo dõi các bài viết cũng mình có lẽ đã từng đọc qua bài viết Laravel 5.5 và React JS, hôm nay mình sẽ viết về một framework khác cũng thường được sử dụng với Laravel, đó là VueJs. Trước khi đọc bài viết này bạn cần có kiến thức cơ bản về Laravel và VueJS hoặc có thể tìm hiểu qua tại đây: Laravel - Frontend, VueJS - Get Started.",
            "image" => 
            "upload/learn/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:24:16",
            "updated_at"=> "2019-08-28 01:34:16",
            ],
            ["user_id" => 1, 
            "title" => 
            "Laravel 5.5 và React JS Phần 1: Cài đặt và Hiển thị ví dụ",
            "content" => 
            "Laravel 5.5 React Preset
            Laravel 5.5 có Frontend Preset mới là ReactJS và None. Trong bài viết này chúng ta sẽ sử dụng React Preset để sử dụng ReactJs trong Laravel app.
            Laravel 5.5 và ReactJS
            Cài đặt Laravel 5.5 cấu hình Database
            Tạo mới laravel project sử dụng composer command:
            ",
            "image" => 
            "upload/learn/image/default.png",
            'priority' => 1,
            "created_at"=> "2019-08-28 01:24:17",
            "updated_at"=> "2019-08-28 01:34:17",
            ],
        ];
            
        foreach ($posts as $key => $post) {
            Learn::create($post);
        }
    }
}

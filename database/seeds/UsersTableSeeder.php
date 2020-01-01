<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'username' => 'Admin',
                'email' => 'admin' . '@gmail.com',
                'password' => bcrypt('string'),
                'avatar' => 'upload/user/image/avatar/default.png',
                'active' => 1,
                'admin' => 1,
                "created_at"=> "2019-08-28 03:12:33",
                "updated_at"=> "2019-08-28 03:12:33"
            ],
            [
                "username"=> "An Thoi",
                "email"=> "anthoi.nguyen.dev@gmail.com",
                'password' => bcrypt('string'),
                "avatar"=> "upload/user/image/avatar/default.png",
                "admin"=> 1,
                "active"=> 1,
                "created_at"=> "2019-08-28 03:12:34",
                "updated_at"=> "2019-08-28 03:12:34"

            ],
            [
                "username" => "Hieu",
                "email" => "hieu@gmail.com",
                'password' => bcrypt('string'),
                "avatar"=> "upload/user/image/avatar/default.png",
                "admin" => 0,
                "active" => 1,
                "created_at"=> "2019-08-28 03:12:35",
                "updated_at"=> "2019-08-28 03:12:35"
            ],
            [
                "username" => "Phat",
                "email" => "phat@gmail.com",
                'password' => bcrypt('string'),
                "avatar"=> "upload/user/image/avatar/default.png",
                "admin" => 0,
                "active" => 1,
                "created_at"=> "2019-08-28 03:12:35",
                "updated_at"=> "2019-08-28 03:12:35"
            ],
            [
                "username" => "Anh",
                "email" => "anh@gmail.com",
                'password' => bcrypt('string'),
                "avatar"=> "upload/user/image/avatar/default.png",
                "admin" => 0,
                "active" => 1,
                "created_at"=> "2019-08-28 03:12:35",
                "updated_at"=> "2019-08-28 03:12:35"
            ]
        ];
        
        foreach ($users as $key => $user) {
            User::create($user);
        }
    }
}

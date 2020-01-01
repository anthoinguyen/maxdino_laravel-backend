<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use App\Reaction;

class ReactionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reactions = [
            ["user_id" => 1, 
            "ask_id" => 1,
            "status"=> 1,
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
            ["user_id" => 2, 
            "ask_id" => 1,
            "status"=> 1,
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
            ["user_id" => 1, 
            "ask_id" => 2,
            "status"=> 1,
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
            ["user_id" => 2, 
            "ask_id" => 2,
            "status"=> 1,
            "created_at"=> "2019-08-28 03:26:33",
            "updated_at"=> "2019-08-28 03:36:33",
            ],
        ];
            
        foreach ($reactions as $key => $reaction) {
            Reaction::create($reaction);
        }
    }
}

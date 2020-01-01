<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UsersTableSeeder::class);
        $this->call(AsksTableSeeder::class);
        $this->call(VideosTableSeeder::class);
        $this->call(LearnsTableSeeder::class);
        $this->call(ReactionsTableSeeder::class);
        $this->call(CommentsTableSeeder::class);
    }
}

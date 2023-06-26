<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

//        ADMINS:
        DB::table("admins")->insert([
            'id' => 1,
            'username' => 'admin1',
            'password' => 'hellouniverse1!',
            'registered_at' => date('y/m/d'),
            'last_login_at' => date('y/m/d'),
        ]);
        DB::table("admins")->insert([
            'id' => 2,
            'username' => 'admin2',
            'password' => 'hellouniverse2!',
            'registered_at' => date('y/m/d'),
            'last_login_at' => date('y/m/d'),
        ]);

//        USERS
        DB::table("users")->insert([
            'id' => 1,
            'username' => 'player1',
            'password' => bcrypt('helloworld1!'),
            'registered_at' => date('y/m/d'),
            'last_login_at' => date('y/m/d'),
        ]);
        DB::table("users")->insert([
            'id' => 2,
            'username' => 'player2',
            'password' => bcrypt('helloworld2!'),
            'registered_at' => date('y/m/d'),
            'last_login_at' => date('y/m/d'),
        ]);
        DB::table("users")->insert([
            'id' => 3,
            'username' => 'dev1',
            'password' => bcrypt('hellobyte1!'),
            'registered_at' => date('y/m/d'),
            'last_login_at' => date('y/m/d'),
        ]);
        DB::table("users")->insert([
            'id' => 4,
            'username' => 'dev2',
            'password' => bcrypt('hellobyte2!'),
            'registered_at' => date('y/m/d'),
            'last_login_at' => date('y/m/d'),
        ]);

//        GAMES
        DB::table("games")->insert([
            'id' => 1,
           'title' => 'Demo Game 1',
            'slug' => 'demo-game-1',
            'description' => 'This is a demo game 1',
            'author_id' => 3,
            'created_at' => '23/05/23',
            'thumbnail' => "https://mir-s3-cdn-cf.behance.net/project_modules/hd/82899741029217.57967ec6543e8.png"
        ]);
        DB::table("games")->insert([
            'id' => 2,
            'title' => 'Demo Game 2',
            'slug' => 'demo-game-2',
            'description' => 'This is a demo game 2',
            'author_id' => 4,
            'created_at' => '23/05/25',
            'thumbnail' => 'https://cdn1.epicgames.com/offer/0a9e3c5ab6684506bd624a849ca0cf39/EGS_DeathStrandingDirectorsCut_KOJIMAPRODUCTIONS_S4_1200x1600-5f99e16507795f9b497716b470cfd876'
        ]);

//        GAME VERSIONS
        DB::table('game_versions')->insert([
            'id' => 1,
            'game_id' => 1,
            'version' => 1,
            'path' => '/test/route',
            'created_at' => '23/05/20'
        ]);
        DB::table('game_versions')->insert([
            'id' => 2,
            'game_id' => 1,
            'version' => 2,
            'path' => '/test/route2',
            'created_at' => '23/05/21'
        ]);
        DB::table('game_versions')->insert([
            'id' => 3,
            'game_id' => 2,
            'version' => 1,
            'path' => '/test/route3',
            'created_at' => '23/05/23'
        ]);

//        SCORES
        DB::table('scores')->insert([
            'user_id' => 1,
            'game_version_id' => 1,
            'timestamp' => time(),
            'score' => 10.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 1,
            'game_version_id' => 1,
            'timestamp' => time(),
            'score' => 15.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 1,
            'game_version_id' => 2,
            'timestamp' => time(),
            'score' => 12.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 2,
            'game_version_id' => 2,
            'timestamp' => time(),
            'score' => 20.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 2,
            'game_version_id' => 3,
            'timestamp' => time(),
            'score' => 30.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 3,
            'game_version_id' => 2,
            'timestamp' => time(),
            'score' => 1000.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 3,
            'game_version_id' => 2,
            'timestamp' => time(),
            'score' => -300.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 4,
            'game_version_id' => 2,
            'timestamp' => time(),
            'score' => 5.0,
        ]);
        DB::table('scores')->insert([
            'user_id' => 4,
            'game_version_id' => 3,
            'timestamp' => time(),
            'score' => 200.0,
        ]);


    }
}

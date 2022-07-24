<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'firstname' => "Eugene",
            'lastname' => "Afeti",
            'msisdn' => "233541859113",
            'country_id' => 288,
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'api_token' => hash('sha256', Uuid::uuid4()->toString()),
            "star_account_number" => rand(1000000000,9999999999),
         'password' => Hash::make(Str::random()),
        ]);

        DB::table('users')->insert([
            'firstname' => "Kojo",
            'lastname' => "Mills",
            'msisdn' => "2202229683",
            "star_account_number" => rand(1000000000,9999999999),
            'country_id' => 270,
            'uuid' => \Ramsey\Uuid\Uuid::uuid4()->toString(),
            'api_token' => hash('sha256', Uuid::uuid4()->toString()),
         'password' => Hash::make(Str::random()),
        ]);
    }
}

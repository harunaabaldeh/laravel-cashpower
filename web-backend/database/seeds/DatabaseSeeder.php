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
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('users')->truncate();



        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->call(CountriesSeeder::class);

        $this->command->info('Seeded the countries!');

        $this->call([ UsersTableSeeder::class]);
        $this->call(RateSettingsTableSeeder::class);
//        $this->call(RatesTableSeeder::class);
        $this->call(BanksTableSeeder::class);
        $this->call(BeneficiariesTableSeeder::class);
        $this->call(TransactionsTableSeeder::class);
    }
}

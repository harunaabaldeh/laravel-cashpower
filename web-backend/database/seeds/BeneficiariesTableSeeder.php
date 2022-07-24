<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Arr;

class BeneficiariesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for($i = 0; $i < 70; $i++){

            $msisdn = $faker->e164PhoneNumber;

            $firstname = $faker->firstName;
            $beneficiaryAccountType = Arr::random(['PickUp', 'Wallet', 'Bank']);

            if ($beneficiaryAccountType == "Bank"){
                $account_number = $faker->bankAccountNumber;
                $bank = \App\Bank::inRandomOrder()->first();

                $routing_code = $bank->routing_code;
                $country = \Countries::where('iso_3166_3',$bank->country_code)->first();

                $bank_name = $bank->name;
            }else
            {
                $routing_code = "";
                $bank_name = "";
                $account_number = $faker->e164PhoneNumber;
                $country = \Countries::whereIn('iso_3166_2',explode(',',env('supportedCountryList')))->inRandomOrder()->first();

            }

            $otherNames = $faker->boolean ? $faker->firstName : "";

            $attributes = ['msisdn' => $msisdn,'bank_name' => $bank_name, 'account_type' => $beneficiaryAccountType, 'firstname' => $firstname, 'lastname' => $faker->lastName, 'nickname' => $firstname . ' - ' . $beneficiaryAccountType, 'othernames' => $otherNames, 'account_number' => $account_number, 'account_routing_number' => $routing_code, 'user_id' => Arr::random([1, 2]), 'country_id' => $country->id];

            \App\Beneficiary::create($attributes);
        }
    }
}

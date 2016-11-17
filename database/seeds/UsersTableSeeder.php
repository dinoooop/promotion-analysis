<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use App\User;

class UsersTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        $users = [
            [
                'name' => 'admin',
                'username' => 'admin',
                'email' => 'dinoop@sparksupport.com',
                'role' => 'admin',
                'password' => bcrypt('promo2016'),
            ]
        ];

        foreach ($users as $value) {
            User::create($value);
        }
    }

}

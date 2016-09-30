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
                'email' => 'admin@mail.com',
                'role' => 'admin',
                'password' => bcrypt('admin'),
            ],
            [
                'name' => 'John',
                'username' => 'john',
                'email' => 'john@mail.com',
                'role' => 'employee',
                'password' => bcrypt('admin'),
            ],
            [
                'name' => 'Ajmal',
                'username' => 'ajmal',
                'email' => 'ajmal@mail.com',
                'role' => 'employee',
                'password' => bcrypt('admin'),
            ],
        ];

        foreach ($users as $value) {
            User::create($value);
        }
    }

}

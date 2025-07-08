<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::create([
            'nama' => 'Rizkyana Kurniawati',
            'username' => 'rizkyana',
            'password' => bcrypt('rizky123'),
            'role' => 'nakes',
        ]);
    }
}

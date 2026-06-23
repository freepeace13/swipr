<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('user_interests')->delete();
        User::query()->delete();

        User::factory(500)
            ->withAvatar()
            ->withRandomInterests()
            ->create();
    }
}

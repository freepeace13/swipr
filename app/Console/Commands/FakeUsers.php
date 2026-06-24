<?php

namespace App\Console\Commands;

use App\Models\Interest;
use App\Models\User;
use Database\Seeders\InterestSeeder;
use Illuminate\Console\Command;

class FakeUsers extends Command
{
    protected $signature = 'swipr:fake-users {--count=50 : How many fake users to generate}';

    protected $description = 'Generate random fake users (with interests) for testing';

    public function handle(): int
    {
        $count = (int) $this->option('count');

        if ($count < 1) {
            $this->error('The --count option must be a positive integer.');

            return self::FAILURE;
        }

        if (Interest::count() === 0) {
            $this->info('No interests found — seeding interests first...');
            $this->call('db:seed', ['--class' => InterestSeeder::class]);
        }

        $this->info("Generating {$count} fake users...");

        User::factory($count)
            ->withRandomInterests()
            ->create();

        $this->info("Done! {$count} fake users created. They all share the password \"password\".");

        return self::SUCCESS;
    }
}

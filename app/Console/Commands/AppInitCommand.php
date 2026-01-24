<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Database\Seeders\UserFakeSeeder;

class AppInitCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:init {--initdb} {--seed} {--all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Initializes the application, runs migrations, seeds, and sets up base roles.';

    const DB_SEED = 'db:seed';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("* {$this->signature}");

        if ($this->option('initdb') || $this->option('all')) {
            $this->initDatabase();
        }

        // if (!is_link(public_path('storage'))) {
        //     $this->call('storage:link');
        // }

        return Command::SUCCESS;
    }

    private function initDatabase(): void
    {
        $this->call('migrate:refresh');

        $this->runRequiredSeeders();

        if ($this->option('seed') || $this->option('all')) {
            $this->runFakeDataSeeders();
        }
    }

    private function runRequiredSeeders(): void
    {
        // $this->call(self::DB_SEED);
    }

    private function runFakeDataSeeders(): void
    {
        $this->call(self::DB_SEED, ['class' => UserFakeSeeder::class]);
    }

    private function runQueries(array $queries): void
    {
        foreach ($queries as $query) {
            $result = DB::statement($query);
            $this->info($query . " ($result)");
        }
    }
}

<?php

namespace App\Console\Commands;

use App\Models\ReturnCase;
use Database\Seeders\DemoBootstrapSeeder;
use Database\Seeders\ReturnsDemoSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetReturnsDemo extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'returns:reset-demo
                            {--force : Skip the confirmation prompt}
                            {--bootstrap : Reapply demo bootstrap settings before reseeding returns data}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Wipe returns demo cases, evidence, events, and refund decisions, then reseed the canonical demo dataset.';

    public function handle(): int
    {
        if (!$this->option('force') && !$this->confirm(
            'This will delete all return cases, evidence uploads, return events, and refund gate decisions in this demo workspace. Continue?'
        )) {
            $this->warn('Returns demo reset cancelled.');
            return self::SUCCESS;
        }

        $existingCount = ReturnCase::query()->count();

        Schema::disableForeignKeyConstraints();
        DB::table('refund_gate_decisions')->truncate();
        DB::table('return_case_media')->truncate();
        DB::table('return_case_events')->truncate();
        DB::table('return_cases')->truncate();
        Schema::enableForeignKeyConstraints();

        Storage::disk('public')->deleteDirectory('return-cases');
        Storage::disk('public')->makeDirectory('return-cases');

        if ($this->option('bootstrap')) {
            $this->laravel->make(DemoBootstrapSeeder::class)->run();
            $this->info('Demo bootstrap settings restored.');
        }

        $this->laravel->make(ReturnsDemoSeeder::class)->run();
        $this->info('Canonical returns demo data restored.');

        $seededCount = ReturnCase::query()->count();

        $this->info("Returns demo reset complete. Removed {$existingCount} case(s) and restored {$seededCount} canonical demo case(s).");
        $this->line('Run this any time the local demo data gets noisy: php artisan returns:reset-demo --force');

        return self::SUCCESS;
    }
}

<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class OptimizeAndClearCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'optimize:full';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Full optimization of the application';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        // Clear various caches
        Artisan::call('cache:clear');
        $this->info('Application cache cleared!');

        Artisan::call('config:clear');
        $this->info('Configuration cache cleared!');

        Artisan::call('route:clear');
        $this->info('Route cache cleared!');

        Artisan::call('view:clear');
        $this->info('View cache cleared!');

        Artisan::call('event:clear');
        $this->info('Event cache cleared!');

        // Optimize various aspects
        Artisan::call('config:cache');
        $this->info('Configuration cached!');

        Artisan::call('route:cache');
        $this->info('Route cache created!');

        Artisan::call('view:cache');
        $this->info('View cache created!');

        Artisan::call('event:cache');
        $this->info('Event cache created!');

        Artisan::call('optimize');
        $this->info('Application optimized!');

        Artisan::call('icons:cache');
        $this->info('Icons optimized!');
    }
}

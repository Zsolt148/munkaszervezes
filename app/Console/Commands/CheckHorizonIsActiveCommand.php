<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

class CheckHorizonIsActiveCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:horizon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if horizon is inactive';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (! $this->isHorizonActive()) {

            $this->info('Horizon is inactive');

            Log::critical('Horizon is inactive');

            return 0;
        }

        $this->info('Horizon is active');
    }

    protected function isHorizonActive(): bool
    {
        if (! $masters = app(MasterSupervisorRepository::class)->all()) {
            return false;
        }

        return collect($masters)->some(function ($master) {
            return $master->status !== 'paused';
        });
    }
}

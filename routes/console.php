<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Console\Commands\UpdateDrinkPrices;
use Illuminate\Support\Facades\Log; 



Artisan::command('UpdateDrinkPrices', function () {
    $cmd = new UpdateDrinkPrices();
    $startTime = time();
    while (true) {
        $this->comment("Running all drinks");
        $cmd->handleAll($this);
        $this->comment("Ran all drinks");
        sleep(10);
    }
    
})->purpose('Updating all drink prices');


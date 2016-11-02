<?php

use Illuminate\Foundation\Inspiring;
use App\Redshift\Dchannel;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->describe('Display an inspiring quote');


Artisan::command('redshiftimport', function () {
    $this->comment("Starting redshift data import ...");
    $dchannel = new Dchannel;
    $dchannel->generate();
})->describe('Importing redshift data');
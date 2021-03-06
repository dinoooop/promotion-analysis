<?php

use Illuminate\Foundation\Inspiring;

use App\Redshift\Dmaterial;
use App\Redshift\Dsales;
use App\Redshift\Dchannel;
use App\Swcalc;
use App\Printm;
use App\RawData;

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

Artisan::command('redshiftimport_dmaterial', function () {
    $this->comment("Starting redshift data import (Dmaterial) ...");
    $obj = new Dmaterial;
    $obj->generate();
})->describe('Importing redshift data');

Artisan::command('redshiftimport_dsales', function () {
    $this->comment("Starting redshift data import (Dsales) ...");
    $obj = new Dsales;
    $obj->generate();
})->describe('Importing redshift data');

Artisan::command('redshiftimport_dchannel', function () {
    $this->comment("Starting redshift data import ...");
    $obj = new Dchannel;
    $obj->generate();
})->describe('Importing redshift data');

Artisan::command('print_array', function () {
    $obj = new Printm;
    $obj->print_array();
    
})->describe('Print array for code use');

Artisan::command('raw_data_process', function () {
    
    $obj = new RawData;
    $obj->process();
    
})->describe('Processing the raw data');
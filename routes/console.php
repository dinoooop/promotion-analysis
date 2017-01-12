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


Artisan::command('promo {action}', function ($action) {    
    $obj = new RawData;
    $obj->$action();
    
})->describe('Processing data');

Artisan::command('run_promotion {id}', function ($id) {    
    $obj = new RawData;
    $obj->run_promotion($id);
    
})->describe('Processing data');

Artisan::command('printm {action}', function ($action) {    
    $obj = new Printm;
    $obj->$action();
    
})->describe('Processing data');

Artisan::command('db_change {action}', function ($action) {
    $obj = new App\DBChange;
    $obj->$action();
    
})->describe('Custom database changes');

Artisan::command('promo_invoice {action}', function ($action) {
    $obj = new App\Redshift\Invoice();
    $obj->$action();
    
})->describe('Create database similar to redshift');


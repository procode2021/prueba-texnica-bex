<?php

use App\Console\Commands\CreateVisitCommand;
use Illuminate\Support\Facades\Artisan;

Artisan::registerCommand(new CreateVisitCommand());
<?php

use App\Console\Commands\OverdueInvoices;

Schedule::call(OverdueInvoices::class)->dailyAt('00:05');

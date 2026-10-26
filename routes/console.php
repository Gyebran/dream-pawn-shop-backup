<?php
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Artisan::command('inspire',function(){$this->comment('Dream Pawn Shop backend is ready for work.');})->purpose('Display a project message');
Schedule::command('loans:cancel-expired-handover')->everyMinute();

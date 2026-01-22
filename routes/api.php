<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\EmlUploadController;
use App\Http\Middleware\EnsureBearerToken;

Route::post('/eml-upload', [EmlUploadController::class, 'store'])
    ->middleware([EnsureBearerToken::class, 'throttle:60,1']);

<?php

use Illuminate\Support\Facades\Route;
use Samirlama\Webhook\WebhookController;

Route::post("/webhook/gitlab/merge_request", [WebhookController::class, 'MergeRequest']);

Route::post("/webhook/gitlab/tags", [WebhookController::class, 'TagRequest']);

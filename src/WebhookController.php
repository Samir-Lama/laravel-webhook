<?php

namespace Samirlama\Webhook;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class WebhookController extends Controller
{
    public function MergeRequest(Request $request)
    {
        $payload = $this->validateRequest($request);

        if ($payload->object_kind != "merge_request") {
            return response("Invalid action", 419);
        }

        if ($payload->object_attributes->state != "merged") {
            return response("Not merged", 419);
        }

        $commands = [
            "cd ..",
            "git fetch",
            "git reset --hard",
            "git pull origin main"
        ];

        $outputs = [];

        foreach ($commands as $command) {
            exec($command, $outputs);
        }

        $log_file = 'git-log-' . date('Y-m-d') . '.log';
        Log::channel('git')->info(implode("\n", $outputs), ['file' => $log_file]);

        return response("Webhook received", 200);
    }

    public function TagRequest(Request $request)
    {
        $payload = $this->validateRequest($request);

        if ($payload->object_kind != "tag_push") {
            return response("Invalid action", 419);
        }

        $commands = [
            "cd ..",
            "git fetch",
            "git checkout tags/" . str_replace('refs/', '', $payload->ref)
        ];

        $outputs = [];

        foreach ($commands as $command) {
            exec($command, $outputs);
        }

        $log_file = 'git-log-' . date('Y-m-d') . '.log';
        Log::channel('git')->info(implode("\n", $outputs), ['file' => $log_file]);

        return response("Webhook received", 200);
    }

    private function validateRequest(Request $request)
    {
        $gitlab_token = $request->header("X-Gitlab-Token");

        if ($gitlab_token !== env("GITLAB_WEBHOOK_TOKEN")) {
            return response("Unauthorized", 401);
        }

        $payload = json_decode($request->getContent());

        if (!$payload) {
            return response("Invalid request", 419);
        }

        return $payload;
    }
}

<?php

namespace App\Listeners;

use App\Models\UserLog;
use Illuminate\Auth\Events\Failed;
use Jenssegers\Agent\Agent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserFailedLogin
{
    public function handle(Failed $event)
    {
        $userId = optional($event->user)->id;

        if (is_null($userId)) {
            return;
        }

        $lastLog = UserLog::where('user_id', $userId)
            ->where('event_type', 'failed_login')
            ->orderByDesc('created_at')
            ->first();

        if ($lastLog && $lastLog->created_at->gt(now()->subSeconds(5))) {
            return;
        }

        $agent = new Agent();
        $browser = $agent->browser();
        $platform = $agent->platform();
        $device = $agent->isMobile() ? 'Mobile' : ($agent->isTablet() ? 'Tablet' : 'Desktop');

        UserLog::create([
            'user_id' => $userId,
            'event_type' => 'failed_login',
            'browser' => $browser,
            'device' => $device,
            'platform' => $platform,
        ]);
    }
}



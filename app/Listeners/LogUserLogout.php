<?php

namespace App\Listeners;

use App\Models\UserLog;
use Illuminate\Auth\Events\Logout;
use Jenssegers\Agent\Agent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserLogout
{
    public function handle(Logout $event)
    {
        $userId = $event->user->id;

        // Avoid duplicate logout logs within 5 seconds
        $lastLog = UserLog::where('user_id', $userId)
            ->where('event_type', 'logout')
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
            'event_type' => 'logout',
            'browser' => $browser,
            'platform' => $platform,
            'device' => $device,
        ]);
    }
}

<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('cloudsunny:sync-accounts')->everyFifteenMinutes();
        
        // Quét VPS sắp hết hạn mỗi ngày vào lúc 09:00 sáng
        $schedule->command('cloudsunny:check-expiring')->dailyAt('09:00');

        // Kiểm tra và xóa VPS đã quá hạn 10 phút (Chạy mỗi phút)
        $schedule->command('vps:delete-expired')->everyMinute()->withoutOverlapping();
        
        // Đồng bộ trạng thái VPS đang khởi tạo (chạy mỗi phút)
        $schedule->command('cloudsunny:sync-status')->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}

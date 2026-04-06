<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;

class SyncFcmTopics extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fcm:sync-topics';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Synchronize all existing user interests with FCM Topics.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $users = User::with('interests', 'deviceTokens')->get();
        $fcm   = app(\App\Services\FcmDeliveryService::class);
        
        $this->info("🔄 Starting Topic Synchronization for " . $users->count() . " users...");
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $users->each(function (User $user) use ($fcm, $bar) {
            $tokens      = $user->deviceTokens()->pluck('fcm_token')->toArray();
            $interestIds = $user->interests()->pluck('interests.id')->toArray();

            if (!empty($tokens) && !empty($interestIds)) {
                foreach ($interestIds as $id) {
                    $fcm->subscribeToTopic("interest_{$id}", $tokens);
                }
            }
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();
        $this->info("✅ Successfully synchronized all database users with Firebase Topics!");
    }
}



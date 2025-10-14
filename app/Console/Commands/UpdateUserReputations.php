<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\ReputationService;
use Illuminate\Console\Command;

class UpdateUserReputations extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reputation:update {--user_id= : Update specific user} {--all : Update all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update user reputation scores and badges based on their activities';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reputationService = new ReputationService();
        
        if ($this->option('user_id')) {
            // Update specific user
            $user = User::find($this->option('user_id'));
            if (!$user) {
                $this->error('User not found!');
                return 1;
            }
            
            $this->info("Updating reputation for user: {$user->name}");
            $result = $reputationService->updateReputation($user);
            
            $this->info("Updated successfully!");
            $this->line("Trust Score: {$result['trust_score']}/100");
            $this->line("Reputation Level: {$result['reputation_level']}");
            $this->line("Badges: " . implode(', ', $result['badges']));
            
        } elseif ($this->option('all')) {
            // Update all users
            $users = User::all();
            $this->info("Updating reputation for {$users->count()} users...");
            
            $bar = $this->output->createProgressBar($users->count());
            $bar->start();
            
            foreach ($users as $user) {
                $reputationService->updateReputation($user);
                $bar->advance();
            }
            
            $bar->finish();
            $this->newLine();
            $this->info('All user reputations updated successfully!');
            
        } else {
            $this->error('Please specify --user_id=ID or --all option');
            return 1;
        }
        
        return 0;
    }
}

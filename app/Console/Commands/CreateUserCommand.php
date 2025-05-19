<?php

namespace App\Console\Commands;

use App\Models\User\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create {--name= : The name of the user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new user using UserFactory';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $password = Str::random(8);

        $data = [
            'password' => $password,
        ];

        if ($name = $this->option('name')) {
            $data['name'] = $name;
        } elseif ($name = $this->ask('What name would you like to use for the user? (leave empty for fake name)')) {
            $data['name'] = $name;
        }

        $user = User::factory()->create($data);

        // Display output
        $this->info('');
        $this->info('New user created successfully.');
        $this->info('');

        $this->line('<fg=green>User ID</> .............................................................. ' . $user->id);
        $this->line('<fg=green>User Name</> ........................................................... ' . $user->name);
        $this->line('<fg=green>Username</> ........................................................... ' . $user->username);
        $this->line('<fg=green>Email</> .............................................................. ' . $user->email);
        $this->line('<fg=green>Password</> ........................................................... ' . $password);
        $this->warn("<fg=yellow>Warning:</> The User password will not be shown again, so don't lose it! ");
        $this->info('');

        return Command::SUCCESS;
    }
}

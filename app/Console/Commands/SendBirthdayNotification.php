<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Notifications\BirthdayNotification;
use Illuminate\Console\Command;

class SendBirthdayNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthday:notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send Birthday Notification';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = User::whereMonth('birthday', '=', date('m'))->whereDay('birthday', '=', date('d'))->get();

        foreach($users as $user)
        {
            $user->notify(new BirthdayNotification());
        }
    }
}

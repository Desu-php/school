<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Sendpulse\RestApi\ApiClient;
use Sendpulse\RestApi\Storage\FileStorage;

class SendNewsLetterMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        $SPApiClient = new ApiClient(env('SENDPULSE_API_USER_ID', 'b12601c6c3f861ec6c7d4629c2dbc81c'), env('SENDPULSE_API_SECRET', '1384b902b9b87fb2375f825ed8472820'), new FileStorage());

        $books = $SPApiClient->listAddressBooks();

        $bookID = $books[0]->id;

        $emails = $SPApiClient->getEmailsFromBook($bookID);

        $email = array(
            'html' => '<p>Hello!</p>',
            'text' => 'Hello!',
            'subject' => 'Mail subject',
            'from' => array(
                'name' => 'Стелла',
                'email' => 'info@itway.bz',
            ),
            'to' => $emails
        );
        $SPApiClient->smtpSendMail($email);
    }
}

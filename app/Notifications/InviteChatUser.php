<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InviteChatUser extends Notification
{
    use Queueable;

    private $user;
    private $chat;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($user, $chat)
    {
        $this->user = $user;
        $this->chat = $chat;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'data' => [
                "user" => $this->user,
                "chat" => $this->chat,
                "type" => 'invite_chat',
            ],
        ];
    }
    /**
     * @param $notifiable
     *
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'data' => [
                "user" => $this->user,
                "chat" => $this->chat,
                "type" => 'invite_chat',
            ],
        ]);
    }
}

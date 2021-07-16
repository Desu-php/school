<?php

namespace App\Broadcasting;

use App\Models\Channel;
use App\Models\CourseChat;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class CourseChatChannel
{
    /**
     * Create a new channel instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Authenticate the user's access to the channel.
     *
     * @param  \App\Models\User  $user
     * @return array|bool
     */
    public function join(User $user, $course_chat_id)
    {
        $count = CourseChat::where('id', $course_chat_id)
            ->whereHas('users', function($q) use ($user) {
                $q->where('user_id', $user->id);
            })->count();

        return $count;
    }
}

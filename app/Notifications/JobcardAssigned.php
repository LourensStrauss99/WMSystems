<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use NotificationChannels\FCM\FCMMessage;
use NotificationChannels\FCM\FCMChannel;

class JobcardAssigned extends Notification
{
    use Queueable;

    protected $jobcard;

    public function __construct($jobcard)
    {
        $this->jobcard = $jobcard;
    }

    public function via($notifiable)
    {
        return [FCMChannel::class];
    }

    public function toFcm($notifiable)
    {
        return FCMMessage::create()
            ->setData([
                'jobcard_id' => $this->jobcard->id,
                'title' => 'New Jobcard Assigned',
                'body' => 'You have been assigned a new jobcard.',
            ])
            ->setNotification([
                'title' => 'New Jobcard Assigned',
                'body' => 'You have been assigned a new jobcard.',
            ]);
    }
}


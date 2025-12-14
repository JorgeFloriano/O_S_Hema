<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use YieldStudio\LaravelExpoNotifier\ExpoNotificationsChannel;
use YieldStudio\LaravelExpoNotifier\Dto\ExpoMessage;

class NewSampleNotification extends Notification
{
    public function via($notifiable): array
    {
        return [ExpoNotificationsChannel::class];
    }

    public function toExpoNotification($notifiable): ExpoMessage
    {
        return (new ExpoMessage())
            ->to([$notifiable->expoToken->value])
            ->title($notifiable->title)
            ->body($notifiable->message)
            ->jsonData([
                'SAT' => $notifiable->order_id
            ])
            ->priority('high')
            ->channelId('default');
    }
}
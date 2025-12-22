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
        // O método getLatestToken() já retorna o token correto
        return (new ExpoMessage())
            ->to($notifiable->getLatestToken()) // ← Vai buscar o MAIS RECENTE
            ->title($notifiable->title)
            ->body($notifiable->message)
            ->jsonData([
                'SAT' => $notifiable->order_id
            ])
            ->priority('high')
            ->channelId('default');
    }
}
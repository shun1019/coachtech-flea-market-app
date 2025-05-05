<?php

namespace App\Notifications;

use App\Models\Trade;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class TradeCompletedNotification extends Notification
{
    use Queueable;

    protected $trade;

    public function __construct(Trade $trade)
    {
        $this->trade = $trade;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $itemName = $this->trade->item->name;
        $buyerName = $this->trade->buyer->username;

        return (new MailMessage)
            ->subject('【取引完了通知】' . $itemName)
            ->greeting($notifiable->username . ' 様')
            ->line("商品「{$itemName}」の取引が完了しました。")
            ->line("購入者：{$buyerName}")
            ->action('マイページで確認する', url(route('profile.index')))
            ->line('引き続きご利用をよろしくお願いいたします。');
    }
}

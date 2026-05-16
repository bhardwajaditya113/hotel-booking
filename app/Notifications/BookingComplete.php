<?php

namespace App\Notifications;

use App\Models\Notification as InAppNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class BookingComplete extends Notification
{
    use Queueable;

    public $name;

    /**
     * Create a new notification instance.
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Use custom channel to handle user_id
        return ['custom_database'];
    }

    /**
     * Custom database notification - saves with user_id
     */
    public function toCustomDatabase(object $notifiable): void
    {
        InAppNotification::query()->create([
            'user_id' => $notifiable->getKey(),
            'type' => 'booking',
            'title' => 'New booking',
            'message' => 'New Booking Added by '.$this->name,
            'data' => ['message' => 'New Booking Added by '.$this->name],
        ]);
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'message' => 'New Booking Added in Hotel',
        ];
    }
}

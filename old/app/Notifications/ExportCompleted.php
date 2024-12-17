<?php

declare(strict_types=1);

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportCompleted extends Notification
{
    use Queueable;

    public string $export;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $export)
    {
        $this->export = $export;
    }

    /**
     * Get the notification's delivery channels.
     */
    public function via(mixed $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(mixed $notifiable): MailMessage
    {
        $attachmentName = explode('-', $this->export);
        unset($attachmentName[0]);
        $attachmentName = implode('-', $attachmentName);

        return (new MailMessage)
            ->subject(get_env_prefix() . 'Export complete')
            ->attach(storage_path("app/exports/{$this->export}"), [
                'as' => $attachmentName,
            ])
            ->markdown('mail.notifications.export-complete', [
                'filename' => $attachmentName,
            ]);
    }

    /**
     * Get the array representation of the notification.
     */
    public function toArray(mixed $notifiable): array
    {
        return [
            //
        ];
    }
}

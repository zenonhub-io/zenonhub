<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ExportCompleted extends Notification
{
    use Queueable;

    public string $export;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(string $export)
    {
        $this->export = $export;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $attachmentName = explode('-', $this->export);
        unset($attachmentName[0]);
        $attachmentName = implode('-', $attachmentName);

        return (new MailMessage)
            ->subject(get_env_prefix() . 'Export complete')
            ->attach(storage_path("app/exports/{$this->export}"), [
                'as' => $attachmentName
            ])
            ->markdown('mail.notifications.export-complete', [
                'filename' => $attachmentName
            ]);
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
            //
        ];
    }
}

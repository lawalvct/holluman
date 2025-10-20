<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WelcomeNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // Get company settings
        $companySettings = [
            'name' => \App\Helpers\SettingsHelper::getCompanySettings()['name'] ?? 'Veesta',
            'address' => \App\Helpers\SettingsHelper::getCompanySettings()['address'] ?? '',
            'support_email' => \App\Helpers\SettingsHelper::getCompanySettings()['support_email'] ?? '',
            'support_phone' => \App\Helpers\SettingsHelper::getCompanySettings()['support_phone'] ?? '',
        ];

        $dashboardUrl = url(route('dashboard'));

        return (new MailMessage)
            ->subject('Welcome to Veesta - Get Started!')
            ->view('vendor.notifications.welcome', [
                'actionUrl' => $dashboardUrl,
                'notifiable' => $notifiable,
                'companySettings' => $companySettings,
            ]);
    }
}

<?php

namespace App\Notifications;

use App\Models\Check;
use App\Models\Probe;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProbeStatusChanged extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Probe $probe,
        public Check $check 
    ) {
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
        $isUp = $this->check->is_up;

        $message = (new MailMessage)
            ->subject($isUp 
                ? "✅ {$this->probe->name} est de nouveau en ligne"
                : "🔴 {$this->probe->name} est hors ligne")
            ->greeting('Bonjour ' . $notifiable->name . ',' )
            ->line($isUp 
                ? "La sonde \"{$this->probe->name}\" répond à nouveau normalement."
                : "La sonde \"{$this->probe->name}\" ne répond plus.")
            ->line("Cible : {$this->probe->target}")
            ->line('Vérification effectuée le ' . $this->check->checked_at->format('d/m/Y à H:i:s'));
        
        if (!$isUp && $this->check->error_message) {
            $message->line("Détail de l'erreur : {$this->check->error_message}");
        }

        $message->action('Vois le tableau de bord', url('/dashboard'));

        return $message;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}

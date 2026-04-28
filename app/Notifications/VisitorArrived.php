<?php

namespace App\Notifications;

use App\Models\Visitor;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;

class VisitorArrived extends Notification
{
    use Queueable;

    public function __construct(public readonly Visitor $visitor) {}

    /**
     * Tentukan channel pengiriman: database, mail, dan custom whatsapp
     */
    public function via(object $notifiable): array
    {
        $channels = ['database'];
        
        // Aktifkan email jika user punya email
        if ($notifiable->email) {
            $channels[] = 'mail';
        }
        
        // Channel custom untuk WhatsApp (kita panggil manual di class ini atau via provider)
        return $channels;
    }

    /**
     * Notifikasi via Email
     */
    public function toMail(object $notifiable): MailMessage
    {
        $appUrl = config('app.url');
        $detailUrl = $appUrl . '/visitors/' . $this->visitor->id;

        return (new MailMessage)
            ->subject('🔔 Ada Tamu Baru: ' . $this->visitor->name)
            ->greeting('Halo, ' . $notifiable->name . '!')
            ->line('Anda mendapatkan tamu baru di ' . config('app.name') . '.')
            ->line('**Detail Tamu:**')
            ->line('• Nama: ' . $this->visitor->name)
            ->line('• Instansi: ' . ($this->visitor->institution ?? '-'))
            ->line('• Keperluan: ' . $this->visitor->purpose)
            ->line('• Check-in: ' . $this->visitor->check_in_at?->format('H:i') . ' WIB')
            ->action('Lihat Detail di Aplikasi', $detailUrl)
            ->line('Mohon segera menuju lobby atau hubungi resepsionis jika diperlukan.')
            ->salutation('Terima kasih, Sistem Buku Tamu SMKN 2 Jakarta');
    }

    /**
     * Data yang disimpan ke tabel notifications (Database)
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type'             => 'visitor_arrived',
            'visitor_id'       => $this->visitor->id,
            'visit_code'       => $this->visitor->visit_code,
            'visitor_name'     => $this->visitor->name,
            'institution'      => $this->visitor->institution ?? 'Tidak diketahui',
            'purpose'          => $this->visitor->purpose,
            'check_in_at'      => $this->visitor->check_in_at?->toISOString(),
            'check_in_display' => $this->visitor->check_in_at?->format('H:i'),
        ];
    }
}

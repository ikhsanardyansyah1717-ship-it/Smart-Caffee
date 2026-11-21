<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class SiteSettingsService
{
    private const FILE = 'quattro-site-settings.json';

    public function all(): array
    {
        if (!Storage::disk('local')->exists(self::FILE)) {
            return [];
        }

        $settings = json_decode(Storage::disk('local')->get(self::FILE), true);
        return is_array($settings) ? $settings : [];
    }

    public function update(array $values): void
    {
        $settings = array_merge($this->all(), $values);
        Storage::disk('local')->put(self::FILE, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }

    public function bankDetails(): array
    {
        $settings = $this->all();
        return [
            'bank_name' => trim((string) ($settings['bank_name'] ?? '')),
            'bank_account_number' => trim((string) ($settings['bank_account_number'] ?? '')),
            'bank_account_holder' => trim((string) ($settings['bank_account_holder'] ?? '')),
        ];
    }

    public function hasBankAccount(): bool
    {
        return collect($this->bankDetails())->every(fn ($value) => $value !== '');
    }

    public function privacyPolicy(): string
    {
        return trim((string) ($this->all()['privacy_policy'] ?? '')) ?: implode("\n\n", [
            'Kebijakan ini menjelaskan cara Quattro Coffee menggunakan informasi saat Anda memakai layanan kami.',
            'Informasi yang digunakan: nama dan informasi akun yang Anda berikan, detail pesanan, serta informasi yang diperlukan untuk pembayaran. Untuk pengunjung, daftar favorit dapat disimpan sementara di browser Anda.',
            'Penggunaan informasi: informasi digunakan untuk menerima dan menyiapkan pesanan, memproses pembayaran, menampilkan riwayat pesanan, menyimpan preferensi, dan membantu meningkatkan layanan kafe.',
            'Penyimpanan dan keamanan: Quattro Coffee menjaga informasi dengan langkah yang wajar dan hanya menyimpannya selama diperlukan untuk layanan, operasional, atau kewajiban yang berlaku.',
            'Pembagian informasi: kami tidak menjual informasi pribadi Anda. Informasi hanya dapat dibagikan kepada penyedia layanan yang membantu operasional atau pembayaran, atau jika diwajibkan oleh hukum.',
            'Pilihan Anda: Anda dapat menghubungi pihak Quattro Coffee untuk meminta bantuan terkait informasi akun Anda. Menghapus data browser dapat menghapus favorit pengunjung yang tersimpan di perangkat tersebut.',
        ]);
    }
}

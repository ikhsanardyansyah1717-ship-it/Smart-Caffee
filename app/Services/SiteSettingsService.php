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
        return $this->bankAccounts()[0] ?? [
            'id' => '', 'bank_name' => '', 'bank_account_number' => '', 'bank_account_holder' => '',
        ];
    }

    public function bankAccounts(): array
    {
        $settings = $this->all();
        $accounts = $settings['bank_accounts'] ?? null;

        if (!is_array($accounts)) {
            $legacy = [
                'id' => 'legacy-default',
                'bank_name' => trim((string) ($settings['bank_name'] ?? '')),
                'bank_account_number' => trim((string) ($settings['bank_account_number'] ?? '')),
                'bank_account_holder' => trim((string) ($settings['bank_account_holder'] ?? '')),
            ];
            return collect($legacy)->only(['bank_name', 'bank_account_number', 'bank_account_holder'])->every(fn ($value) => $value !== '')
                ? [$legacy]
                : [];
        }

        return collect($accounts)->filter(fn ($account) => is_array($account)
                && trim((string) ($account['bank_name'] ?? '')) !== ''
                && trim((string) ($account['bank_account_number'] ?? '')) !== ''
                && trim((string) ($account['bank_account_holder'] ?? '')) !== '')
            ->map(fn ($account, $index) => [
                'id' => (string) ($account['id'] ?? 'bank-' . $index),
                'bank_name' => trim((string) $account['bank_name']),
                'bank_account_number' => trim((string) $account['bank_account_number']),
                'bank_account_holder' => trim((string) $account['bank_account_holder']),
            ])->values()->all();
    }

    public function hasBankAccount(): bool
    {
        return count($this->bankAccounts()) > 0;
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
    public function homeBanners(): array
    {
        $defaults = [
            ['badge' => 'Rekomendasi', 'title' => 'Cafe Latte', 'description' => 'Best Seller pekan ini di Quattro Coffee', 'image' => 'https://images.unsplash.com/photo-1541167760496-1628856ab772?w=500&auto=format&fit=crop'],
            ['badge' => 'Favorit', 'title' => 'Cappuccino', 'description' => 'Kopi creamy favorit pelanggan Quattro', 'image' => 'https://images.unsplash.com/photo-1534778101976-62847782c213?w=500&auto=format&fit=crop'],
            ['badge' => 'Special', 'title' => 'Caramel Macchiato', 'description' => 'Rasa manis dan creamy untuk hari ini', 'image' => 'https://images.unsplash.com/photo-1485808191679-5f86510681a2?w=500&auto=format&fit=crop'],
            ['badge' => 'Quattro', 'title' => 'Americano', 'description' => 'Simple, bold, dan cocok untuk kapan saja', 'image' => 'https://images.unsplash.com/photo-1514432324607-a09d9b4aefdd?w=500&auto=format&fit=crop'],
        ];
        $saved = $this->all()['home_banners'] ?? [];

        return collect($defaults)->values()->map(function (array $default, int $index) use ($saved) {
            return array_merge($default, is_array($saved[$index] ?? null) ? $saved[$index] : []);
        })->all();
    }

    public function updateHomeBanners(array $banners): void
    {
        $this->update(['home_banners' => array_values($banners)]);
    }

    public function promotionForProduct(int $productId): ?array
    {
        foreach ($this->homeBanners() as $banner) {
            if ((int) ($banner['product_id'] ?? 0) === $productId && (int) ($banner['discount_percent'] ?? 0) > 0) {
                return $banner;
            }
        }

        return null;
    }

    public function discountedPrice(int $productId, float $basePrice): float
    {
        $promotion = $this->promotionForProduct($productId);
        $discount = (int) ($promotion['discount_percent'] ?? 0);

        return $discount > 0
            ? round($basePrice * (100 - $discount) / 100, 2)
            : $basePrice;
    }}

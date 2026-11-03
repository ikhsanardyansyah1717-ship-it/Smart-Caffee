<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;

class WhatsAppLoginWebhookController extends Controller
{
    /**
     * Verifikasi webhook Meta WhatsApp.
     */
    public function verify(Request $request)
    {
        $verifyToken = (string) config('services.whatsapp.webhook_verify_token');

        $mode = $request->query('hub.mode', $request->query('hub_mode'));
        $token = $request->query('hub.verify_token', $request->query('hub_verify_token'));
        $challenge = $request->query('hub.challenge', $request->query('hub_challenge'));

        if (
            $mode === 'subscribe' &&
            $verifyToken !== '' &&
            hash_equals($verifyToken, (string) $token)
        ) {
            return response((string) $challenge, 200)
                ->header('Content-Type', 'text/plain');
        }

        return response('Forbidden', 403);
    }

    /**
     * Menerima pesan masuk dari WhatsApp.
     */
    public function receive(Request $request)
    {
        // Validasi tanda tangan resmi dari Meta.
        $appSecret = (string) config('services.whatsapp.app_secret');
        $signature = (string) $request->header('X-Hub-Signature-256');

        $expected = $appSecret !== ''
            ? 'sha256=' . hash_hmac('sha256', $request->getContent(), $appSecret)
            : '';

        if (
            $expected === '' ||
            $signature === '' ||
            !hash_equals($expected, $signature)
        ) {
            return response('Invalid signature', 401);
        }

        $payload = $request->json()->all();

        foreach (($payload['entry'] ?? []) as $entry) {
            foreach (($entry['changes'] ?? []) as $change) {
                $value = $change['value'] ?? [];

                // Pastikan pesan berasal dari nomor WhatsApp bisnis yang dikonfigurasi.
                $incomingPhoneId = (string) data_get(
                    $value,
                    'metadata.phone_number_id',
                    ''
                );

                $configuredPhoneId = (string) config(
                    'services.whatsapp.phone_number_id',
                    ''
                );

                if (
                    $configuredPhoneId === '' ||
                    $incomingPhoneId !== $configuredPhoneId
                ) {
                    continue;
                }

                foreach (($value['messages'] ?? []) as $message) {
                    $text = (string) data_get($message, 'text.body', '');

                    // Ambil kode login dari pesan pelanggan.
                    if (!preg_match(
                        '/(?:verification code|kode verifikasi):\s*([A-Z0-9]{12})/i',
                        $text,
                        $matches
                    )) {
                        continue;
                    }

                    $code = strtoupper($matches[1]);
                    $key = 'customer_whatsapp_login:' . $code;

                    $challenge = Cache::get($key);

                    if (
                        !$challenge ||
                        ($challenge['status'] ?? null) !== 'pending'
                    ) {
                        continue;
                    }

                    // Nomor WhatsApp pengirim.
                    $phone = $this->normalizePhone(
                        (string) data_get($message, 'from', '')
                    );

                    if (!$phone) {
                        $this->updateChallenge($key, $challenge, null);
                        continue;
                    }

                    // Ambil nama profil WhatsApp pengirim.
                    $profileName = $this->getWhatsAppProfileName(
                        $value['contacts'] ?? [],
                        $phone
                    );

                    // Cari akun berdasarkan nomor WhatsApp.
                    $existingUser = User::where('phone', $phone)->first();

                    if ($existingUser) {
                        // Nomor yang terdaftar pada role selain customer
                        // tidak boleh dipakai untuk login pelanggan.
                        if ($existingUser->role !== 'customer') {
                            $this->updateChallenge($key, $challenge, null);
                            continue;
                        }

                        $this->updateChallenge(
                            $key,
                            $challenge,
                            $existingUser->id
                        );

                        continue;
                    }

                    // Nomor belum terdaftar: buat akun pelanggan baru.
                    try {
                        $newUser = $this->createWhatsAppCustomer(
                            $phone,
                            $profileName
                        );
                    } catch (QueryException $exception) {
                        // Jika webhook Meta terkirim berulang hampir bersamaan,
                        // periksa kembali apakah nomor sudah berhasil dibuat.
                        $newUser = User::where('phone', $phone)
                            ->where('role', 'customer')
                            ->first();
                    }

                    if (!$newUser) {
                        $this->updateChallenge($key, $challenge, null);
                        continue;
                    }

                    $this->updateChallenge(
                        $key,
                        $challenge,
                        $newUser->id
                    );
                }
            }
        }

        return response()->json(['received' => true]);
    }

    /**
     * Membuat akun customer khusus WhatsApp.
     */
    private function createWhatsAppCustomer(
        string $phone,
        ?string $profileName
    ): ?User {
        $name = trim((string) $profileName);

        if ($name === '') {
            $name = 'Pelanggan WhatsApp';
        }

        $name = mb_substr($name, 0, 255);

        // Buat username unik.
        $username = 'wa' . $phone;

        while (User::where('username', $username)->exists()) {
            $username = 'wa' . $phone . '_' . Str::lower(Str::random(5));
        }

        // Email internal untuk akun yang dibuat melalui WhatsApp.
        $email = 'wa' . $phone . '@whatsapp.quattro.local';

        while (User::where('email', $email)->exists()) {
            $email = 'wa' . $phone . '_' . Str::lower(Str::random(5))
                . '@whatsapp.quattro.local';
        }

        return User::create([
            'name' => $name,
            'username' => $username,
            'email' => $email,
            'phone' => $phone,
            'password' => Str::random(48),
            'role' => 'customer',
        ]);
    }

    /**
     * Mengambil nama profil dari data kontak WhatsApp.
     */
    private function getWhatsAppProfileName(
        array $contacts,
        string $phone
    ): ?string {
        foreach ($contacts as $contact) {
            $contactPhone = $this->normalizePhone(
                (string) data_get($contact, 'wa_id', '')
            );

            if ($contactPhone === $phone) {
                $name = trim((string) data_get(
                    $contact,
                    'profile.name',
                    ''
                ));

                return $name !== '' ? $name : null;
            }
        }

        return null;
    }

    /**
     * Memperbarui status proses login.
     */
    private function updateChallenge(
        string $key,
        array $challenge,
        ?int $userId
    ): void {
        $challenge['status'] = $userId ? 'verified' : 'unlinked';
        $challenge['user_id'] = $userId;

        Cache::put($key, $challenge, now()->addMinutes(5));
    }

    /**
     * Menyamakan format nomor menjadi 62xxxxxxxx.
     */
    private function normalizePhone(string $phone): ?string
    {
        $digits = preg_replace('/\D+/', '', $phone);

        if (str_starts_with($digits, '0')) {
            $digits = '62' . substr($digits, 1);
        } elseif (str_starts_with($digits, '8')) {
            $digits = '62' . $digits;
        }

        return preg_match('/^62[0-9]{8,13}$/', $digits)
            ? $digits
            : null;
    }
}
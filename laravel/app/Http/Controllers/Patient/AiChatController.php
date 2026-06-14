<?php

namespace App\Http\Controllers\Patient;

use App\Http\Controllers\Controller;
use App\Models\Poli;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class AiChatController extends Controller
{
    public function index(Request $request)
    {
        return view('patient.ai-chat', [
            'messages' => $request->session()->get('patient_ai_chat', []),
        ]);
    }

    public function ask(Request $request)
    {
        $validated = $request->validate([
            'message' => ['required', 'string', 'min:2', 'max:1000'],
        ]);

        $messages = $request->session()->get('patient_ai_chat', []);
        $messages[] = [
            'role' => 'user',
            'content' => trim($validated['message']),
        ];

        if (! $this->isInScopeQuestion($validated['message'])) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $this->outOfScopeReply(),
            ];

            $request->session()->put('patient_ai_chat', array_slice($messages, -12));

            return redirect()->route('patient.ai.index');
        }

        $manualReply = $this->manualReply($validated['message']);

        if ($manualReply !== null) {
            $messages[] = [
                'role' => 'assistant',
                'content' => $manualReply,
            ];

            $request->session()->put('patient_ai_chat', array_slice($messages, -12));

            return redirect()->route('patient.ai.index');
        }

        try {
            $reply = empty(config('openrouter.api_key'))
                ? $this->fallbackReply($validated['message'])
                : $this->sendToOpenRouter($request, $messages);
        } catch (ConnectionException $exception) {
            report($exception);

            $reply = $this->fallbackReply($validated['message']);
        } catch (\Throwable $exception) {
            report($exception);

            $reply = $this->fallbackReply($validated['message']);
        }

        $messages[] = [
            'role' => 'assistant',
            'content' => $reply,
        ];

        $request->session()->put('patient_ai_chat', array_slice($messages, -12));

        return redirect()->route('patient.ai.index');
    }

    public function reset(Request $request)
    {
        $request->session()->forget('patient_ai_chat');

        return redirect()->route('patient.ai.index');
    }

    private function sendToOpenRouter(Request $request, array $messages): string
    {
        $user = $request->user();
        $bookingSummary = $this->bookingSummary($user);
        $serviceContext = $this->serviceContext();

        $payloadMessages = array_merge([
            [
                'role' => 'system',
                'content' => $this->systemPrompt($user->name, $bookingSummary, $serviceContext),
            ],
        ], array_slice($messages, -8));

        $response = Http::withToken(config('openrouter.api_key'))
            ->withHeaders([
                'Content-Type' => 'application/json',
                'HTTP-Referer' => config('openrouter.site_url'),
                'X-Title' => config('openrouter.site_name'),
            ])
            ->timeout(config('openrouter.timeout'))
            ->post(rtrim(config('openrouter.base_url'), '/') . '/chat/completions', [
                'model' => config('openrouter.model'),
                'messages' => $payloadMessages,
                'temperature' => 0.4,
                'max_tokens' => 700,
                'user' => (string) $user->id,
            ]);

        if ($response->failed()) {
            throw new \RuntimeException('OpenRouter error: ' . $response->body());
        }

        $content = data_get($response->json(), 'choices.0.message.content');

        if (! is_string($content) || trim($content) === '') {
            throw new \RuntimeException('OpenRouter returned an empty response.');
        }

        return $this->normalizeAssistantReply($content);
    }

    private function bookingSummary($user): string
    {
        try {
            return $user->bookings()
                ->with(['doctor.poli'])
                ->latest()
                ->limit(3)
                ->get()
                ->map(function ($booking) {
                    $doctorName = $booking->doctor?->name ?? 'dokter belum tersedia';
                    $poliName = $booking->doctor?->poli?->name ?? 'poli belum tersedia';
                    $visitDate = $booking->visit_date
                        ? $booking->visit_date->format('d M Y')
                        : 'tanggal belum tersedia';

                    return sprintf(
                        '%s - Dr. %s, %s, tanggal %s, status %s',
                        $booking->queue_number,
                        $doctorName,
                        $poliName,
                        $visitDate,
                        $booking->status
                    );
                })
                ->implode("\n");
        } catch (\Throwable $exception) {
            report($exception);

            return '';
        }
    }

    private function systemPrompt(string $name, string $bookingSummary, string $serviceContext): string
    {
        $bookingContext = $bookingSummary !== ''
            ? "Ringkasan booking pasien terbaru:\n{$bookingSummary}"
            : 'Pasien belum memiliki ringkasan booking terbaru.';
        $serviceContext = $serviceContext !== ''
            ? "Data layanan yang tersedia di aplikasi:\n{$serviceContext}"
            : 'Data layanan/poli/jadwal dokter belum tersedia di aplikasi.';

        return <<<PROMPT
Kamu adalah asisten virtual Puskesmas di aplikasi sistem antrian online.
Tugasmu membantu pasien dengan gaya bicara manusia sehari-hari: ramah, santai, tenang, jelas, dan mudah dipahami masyarakat umum Indonesia.
Nama pasien tersedia sebagai konteks saja: {$name}. Jangan memanggil nama pasien di setiap jawaban.
{$bookingContext}
{$serviceContext}

Gaya bicara yang harus dipakai:
- Jawab seperti sedang ngobrol, bukan seperti surat resmi atau customer service kaku.
- Pakai bahasa Indonesia sehari-hari yang sopan, sederhana, dan langsung ke inti.
- Jangan terlalu banyak memakai "Bapak/Ibu". Pakai "kamu", "kalau", "biasanya", atau kalimat netral bila lebih natural.
- Maksimal 2 sampai 4 paragraf pendek.
- Jangan pakai markdown, tanda **, heading, emoji, bullet panjang, atau format bernomor kecuali benar-benar perlu.
- Sesuaikan jawaban dengan pertanyaan terakhir dan riwayat percakapan. Jangan mengulang penjelasan yang tidak ditanya.

Hindari gaya dan kalimat seperti ini:
- "Mohon maaf atas ketidaknyamanan"
- "Silakan informasikan kepada kami"
- "Bapak/Ibu diwajibkan"
- "Sesuai ketentuan yang berlaku"
- "Kami sarankan agar Anda melakukan"
- "Apakah ada hal lain yang bisa saya bantu?"

Contoh gaya yang disukai:
- "Tenang, biasanya..."
- "Kalau untuk kondisi seperti itu..."
- "Umumnya cukup..."
- "Kalau punya BPJS, bawa saja BPJS-nya."
- "Tidak perlu khawatir."

Aturan isi jawaban:
- Jawab hanya pertanyaan yang masih berhubungan dengan aplikasi ini: antrian online puskesmas, booking, akun/login/daftar, dashboard pasien, notifikasi, poli, dokter, jadwal, kuota, status/riwayat booking, dokumen pendaftaran, BPJS/JKN/KIS, persiapan kunjungan, dan edukasi kesehatan umum yang wajar untuk layanan puskesmas.
- Kalau pertanyaan di luar konteks aplikasi dan layanan puskesmas, jangan menjawab isi pertanyaannya. Tolak singkat dengan kalimat: "Maaf, aku hanya bisa membantu pertanyaan seputar aplikasi antrian online puskesmas, booking, layanan puskesmas, dan informasi kesehatan umum yang terkait."
- Jangan menjawab pertanyaan tentang politik, hiburan, tugas sekolah umum, coding, keuangan, investasi, agama, berita, olahraga, resep masakan, atau topik lain yang tidak berhubungan dengan web ini.
- Pertanyaan singkat seperti "buka jam berapa", "poli gigi buka jam berapa", "jam praktik", "jadwal dokter", atau "hari apa buka" tetap termasuk konteks layanan puskesmas. Jangan ditolak sebagai luar konteks.
- Kalau pasien bertanya jam buka puskesmas umum dan data jam operasional puskesmas tidak ada di konteks, jawab bahwa jam operasional puskesmas belum tersedia di aplikasi. Kalau yang tersedia hanya jadwal dokter/poli, jelaskan berdasarkan data jadwal dokter/poli yang ada.
- Kalau pasien bertanya jadwal poli tertentu, gunakan data layanan yang tersedia di aplikasi. Jangan mengarang hari atau jam yang tidak ada di data.
- Jangan memberi diagnosis pasti, resep obat, dosis obat, atau keputusan medis personal.
- Jangan menggantikan dokter. Kalau keluhan berlanjut, makin berat, atau pasien ragu, arahkan untuk periksa langsung ke puskesmas/dokter.
- Kalau ada tanda bahaya seperti demam sangat tinggi, sesak napas, kejang, pingsan, lemas sekali, perdarahan, nyeri dada berat, atau penurunan kesadaran, sarankan segera ke IGD/layanan darurat.
- Kalau tidak tahu informasi yang pasti, akui dengan jujur dan arahkan pasien untuk cek ke petugas puskesmas.
- Saat ini sistem tidak menyediakan nomor telepon puskesmas resmi. Kalau pasien bertanya nomor telepon, kontak, hotline, alamat, jam layanan pasti, atau info resmi lain yang tidak ada di konteks, jawab bahwa datanya belum tersedia di aplikasi. Jangan mengarang nomor telepon, alamat, hotline, atau sumber resmi.
- Jangan selalu mengulang daftar dokumen. Jangan selalu menjelaskan semua poli.
- Untuk pertanyaan dokumen saat keluhan ringan seperti demam, batuk, flu, sakit kepala, atau panas beberapa hari, jawab dokumen minimum saja: KTP atau identitas diri jika ada, dan BPJS/JKN/KIS atau Mobile JKN kalau ingin memakai BPJS.
- Kalau dokumen tidak lengkap, jelaskan dengan santai bahwa biasanya tetap bisa dibantu, tapi pendaftaran mungkin lebih lama.
- Jangan menyebut KIA, fotokopi, hasil pemeriksaan lama, atau bukti booking kecuali memang relevan dengan pertanyaan.
- Kalau pasien bertanya "kenapa ribet harus bawa dokumen", jawab empatik dan menenangkan. Jelaskan singkat bahwa identitas membantu pendaftaran, dan BPJS diperlukan kalau ingin biaya ditanggung BPJS.

Contoh jawaban untuk "Poli apa saja yang tersedia?":
"Saat ini tersedia Poli Umum, Poli Gigi, Poli KIA, dan Poli Laboratorium. Kalau belum yakin harus ke poli mana, biasanya bisa mulai dari Poli Umum, nanti dokter akan membantu mengarahkan kalau perlu pemeriksaan lebih lanjut."

Contoh jawaban untuk "Kalau saya demam 3 hari perlu bawa dokumen banyak?":
"Kalau hanya mau periksa demam, biasanya cukup bawa KTP atau identitas diri. Kalau ingin menggunakan BPJS, bawa juga kartu BPJS atau Mobile JKN.

Kalau tidak membawa dokumen lengkap, biasanya petugas tetap akan membantu proses pendaftaran. Yang penting segera diperiksa, apalagi kalau demamnya sudah beberapa hari."

Contoh jawaban untuk "Kenapa ribet banget harus bawa dokumen?":
"Sebenarnya tidak harus banyak dokumen. Biasanya identitas diri saja sudah cukup untuk pendaftaran. Kalau memakai BPJS, baru perlu kartu BPJS agar biaya bisa ditanggung sesuai aturan BPJS."
PROMPT;
    }

    private function normalizeAssistantReply(string $content): string
    {
        $text = trim(str_replace(["\r\n", "\r"], "\n", $content));

        $replacements = [
            '/\*\*(.*?)\*\*/s' => '$1',
            '/__(.*?)__/s' => '$1',
            '/\*(.*?)\*/s' => '$1',
            '/`{1,3}([^`]*)`{1,3}/s' => '$1',
            '/^\s{0,3}#{1,6}\s*/m' => '',
            '/^\s*[-*]\s+/m' => '',
            '/^\s*\d+\.\s+/m' => '',
        ];

        foreach ($replacements as $pattern => $replacement) {
            $text = preg_replace($pattern, $replacement, $text) ?? $text;
        }

        $paragraphs = preg_split("/\n\s*\n/", $text) ?: [];
        $paragraphs = array_values(array_filter(array_map(function ($paragraph) {
            $paragraph = preg_replace("/[ \t]+/", ' ', trim($paragraph)) ?? trim($paragraph);
            return $paragraph;
        }, $paragraphs)));

        if (count($paragraphs) > 4) {
            $paragraphs = array_slice($paragraphs, 0, 4);
        }

        return Str::limit(implode("\n\n", $paragraphs), 1600, '');
    }

    private function isInScopeQuestion(string $message): bool
    {
        $text = Str::lower($message);
        $normalizedText = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

        $blockedKeywords = [
            'kode python',
            'coding',
            'programming',
            'javascript',
            'laravel error',
            'siapa presiden',
            'presiden indonesia',
            'resep',
            'nasi goreng',
            'investasi',
            'saham',
            'crypto',
            'bola',
            'film',
        ];

        foreach ($blockedKeywords as $keyword) {
            if (Str::contains($text, $keyword)) {
                return false;
            }
        }

        $conversationReplies = [
            'halo',
            'hallo',
            'hello',
            'hai',
            'hi',
            'selamat pagi',
            'selamat siang',
            'selamat sore',
            'selamat malam',
            'ok',
            'oke',
            'okay',
            'baik',
            'iya',
            'ya',
            'sip',
            'siap',
            'paham',
            'mengerti',
            'terima kasih',
            'makasih',
            'thanks',
            'lanjut',
        ];

        if (in_array($normalizedText, $conversationReplies, true)) {
            return true;
        }

        $servicePhrases = [
            'jam berapa',
            'buka jam',
            'jam buka',
            'jam layanan',
            'jam operasional',
            'jam praktek',
            'jam praktik',
            'hari apa',
            'kapan buka',
            'kapan tutup',
            'masih buka',
        ];

        foreach ($servicePhrases as $phrase) {
            if (Str::contains($text, $phrase)) {
                return true;
            }
        }

        $allowedKeywords = [
            'antrian',
            'booking',
            'pesan',
            'daftar',
            'pendaftaran',
            'login',
            'masuk',
            'akun',
            'password',
            'dashboard',
            'notifikasi',
            'nomor',
            'status',
            'riwayat',
            'batal',
            'batalkan',
            'puskesmas',
            'pasien',
            'dokter',
            'poli',
            'jadwal',
            'buka',
            'tutup',
            'jam',
            'operasional',
            'praktek',
            'praktik',
            'hari',
            'kuota',
            'kunjungan',
            'periksa',
            'pemeriksaan',
            'kesehatan',
            'tes kesehatan',
            'cek kesehatan',
            'medical check up',
            'check up',
            'skrining',
            'layanan',
            'bpjs',
            'jkn',
            'kis',
            'ktp',
            'identitas',
            'dokumen',
            'kartu',
            'mobile jkn',
            'rekam medis',
            'surat rujukan',
            'rujukan',
            'obat',
            'demam',
            'batuk',
            'flu',
            'pilek',
            'sakit',
            'nyeri',
            'mual',
            'pusing',
            'sesak',
            'diare',
            'gigi',
            'hamil',
            'imunisasi',
            'laboratorium',
            'darurat',
            'igd',
        ];

        foreach ($allowedKeywords as $keyword) {
            if (Str::contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function manualReply(string $message): ?string
    {
        $text = Str::lower($message);
        $normalizedText = trim(preg_replace('/\s+/', ' ', $text) ?? $text);

        $greetings = [
            'halo',
            'hallo',
            'hello',
            'hai',
            'hi',
            'selamat pagi',
            'selamat siang',
            'selamat sore',
            'selamat malam',
        ];

        if (in_array($normalizedText, $greetings, true)) {
            return 'Halo, aku bisa bantu pertanyaan seputar booking antrian, poli, dokter, jadwal, dokumen pendaftaran, BPJS, dan keluhan kesehatan ringan yang terkait layanan puskesmas.';
        }

        $shortReplies = [
            'ok',
            'oke',
            'okay',
            'baik',
            'iya',
            'ya',
            'sip',
            'siap',
            'paham',
            'mengerti',
            'terima kasih',
            'makasih',
            'thanks',
        ];

        if (in_array($normalizedText, $shortReplies, true)) {
            return 'Siap. Kalau mau, kamu bisa tanya soal jadwal poli, cara booking, status antrian, dokumen yang perlu dibawa, atau keluhan ringan seperti demam dan sakit gigi.';
        }

        if ($normalizedText === 'lanjut') {
            return 'Boleh, lanjut. Coba tulis pertanyaan yang ingin kamu cek, misalnya jadwal dokter, poli yang tersedia, atau cara membuat booking.';
        }

        return null;
    }

    private function fallbackReply(string $message): string
    {
        $text = Str::lower($message);

        if (Str::contains($text, ['booking', 'pesan', 'daftar', 'antrian'])) {
            return 'Untuk booking antrian, biasanya kamu bisa pilih menu booking/antrian di dashboard pasien, lalu pilih poli, dokter, dan tanggal kunjungan yang tersedia. Setelah berhasil, simpan nomor antrian atau cek lagi di riwayat booking.';
        }

        if (Str::contains($text, ['dokumen', 'ktp', 'bpjs', 'jkn', 'kis', 'kartu'])) {
            return 'Untuk periksa ke puskesmas, biasanya cukup bawa KTP atau identitas diri. Kalau ingin memakai BPJS/JKN/KIS, bawa juga kartu BPJS atau Mobile JKN supaya pendaftaran lebih lancar.';
        }

        if (Str::contains($text, ['jadwal', 'poli', 'dokter', 'jam', 'buka'])) {
            return $this->asksForPoliList($text)
                ? $this->poliListReply()
                : $this->scheduleReply($text);
        }

        if (Str::contains($text, ['tes kesehatan', 'cek kesehatan', 'periksa kesehatan', 'medical check up', 'check up', 'skrining'])) {
            return $this->symptomReply(
                ['umum'],
                'Kalau mau tes atau cek kesehatan umum, biasanya bisa mulai dari %s. Nanti dokter akan menilai dulu pemeriksaan apa yang diperlukan.',
                'Kalau kamu butuh pemeriksaan khusus seperti laboratorium, tanyakan dulu ke petugas atau dokter karena jenis pemeriksaannya mengikuti layanan yang tersedia di puskesmas.'
            );
        }

        if (Str::contains($text, ['demam', 'panas', 'batuk', 'flu', 'pilek', 'sakit', 'nyeri', 'mual', 'pusing', 'diare', 'sesak'])) {
            return $this->symptomReply(
                ['umum'],
                'Untuk keluhan seperti itu, biasanya bisa mulai periksa ke %s. Nanti dokter akan memeriksa dulu dan mengarahkan kalau perlu pemeriksaan lanjutan.',
                'Kalau keluhannya makin berat, sesak napas, nyeri dada, lemas sekali, pingsan, demam sangat tinggi, atau kesadaran menurun, sebaiknya segera ke IGD atau layanan darurat.'
            );
        }

        return 'Maaf, AI sedang belum bisa terhubung ke layanan utama. Tapi aku tetap bisa bantu untuk topik dasar seperti jadwal poli, cara booking, dokumen pendaftaran, BPJS, status antrian, dan keluhan kesehatan ringan yang terkait layanan puskesmas.';
    }

    private function asksForPoliList(string $text): bool
    {
        return Str::contains($text, ['poli apa', 'poli apa saja', 'daftar poli', 'layanan apa', 'layanan tersedia'])
            || (Str::contains($text, 'poli') && Str::contains($text, ['tersedia', 'ada apa', 'apa saja']));
    }

    private function asksForSchedule(string $text): bool
    {
        return Str::contains($text, [
            'jadwal',
            'jam berapa',
            'buka jam',
            'jam buka',
            'jam layanan',
            'jam operasional',
            'jam praktek',
            'jam praktik',
            'hari apa',
            'kapan buka',
            'masih buka',
        ]);
    }

    private function poliListReply(): string
    {
        $polis = Poli::query()->orderBy('name')->pluck('name');

        if ($polis->isEmpty()) {
            return 'Data poli belum tersedia di aplikasi. Admin perlu mengisi data Poli, Dokter, dan Jadwal Dokter dulu supaya AI bisa menjawab sesuai isi web.';
        }

        $names = $polis->implode(', ');

        return "Saat ini poli yang tersedia di aplikasi:\n{$names}\n\nKalau belum yakin harus ke poli mana, biasanya bisa mulai dari Poli Umum jika tersedia. Nanti dokter akan membantu mengarahkan kalau perlu pemeriksaan lanjutan.";
    }

    private function scheduleReply(string $text): string
    {
        $polis = Poli::with(['doctors.schedules'])->orderBy('name')->get();

        if ($polis->isEmpty()) {
            return 'Data jadwal belum tersedia di aplikasi. Admin perlu mengisi data Poli, Dokter, dan Jadwal Dokter dulu.';
        }

        $matchedPolis = $polis->filter(function ($poli) use ($text) {
            $name = Str::lower($poli->name);
            $withoutPoli = trim(str_replace('poli', '', $name));

            return Str::contains($text, $name)
                || ($withoutPoli !== '' && Str::contains($text, $withoutPoli));
        });

        $selectedPolis = $matchedPolis->isNotEmpty() ? $matchedPolis : $polis;
        $lines = $this->formatScheduleLines($selectedPolis);

        if ($matchedPolis->isEmpty()) {
            return "Ini jadwal dokter/poli yang tersedia di aplikasi:\n\n{$lines}\n\nKalau yang kamu maksud jam operasional puskesmas secara umum, datanya belum tersedia di aplikasi.";
        }

        return "Berdasarkan jadwal yang tersedia di aplikasi:\n\n{$lines}";
    }

    private function formatScheduleLines($polis): string
    {
        return $polis->map(function ($poli) {
            if ($poli->doctors->isEmpty()) {
                return "{$poli->name}:\nBelum ada dokter/jadwal tersedia.";
            }

            $doctors = $poli->doctors->map(function ($doctor) {
                $schedules = $this->formatDoctorSchedules($doctor);

                return "Dr. {$doctor->name}: {$schedules}";
            })->implode("\n");

            return "{$poli->name}:\n{$doctors}";
        })->implode("\n\n");
    }

    private function formatDoctorSchedules($doctor): string
    {
        $schedules = $doctor->schedules
            ->sortBy(fn ($schedule) => $this->weekdayOrder($schedule->weekday))
            ->map(function ($schedule) {
                return sprintf(
                    '%s %s-%s',
                    $this->weekdayName($schedule->weekday),
                    substr((string) $schedule->start_time, 0, 5),
                    substr((string) $schedule->end_time, 0, 5)
                );
            })
            ->implode(', ');

        return $schedules !== '' ? $schedules : 'jadwal belum tersedia';
    }

    private function symptomReply(array $poliKeywords, string $mainTemplate, string $warning): string
    {
        $poliName = $this->findPoliName($poliKeywords);

        if ($poliName === null) {
            return 'Data poli yang sesuai belum tersedia di aplikasi. Untuk keluhan seperti ini, kamu bisa datang ke puskesmas agar petugas membantu mengarahkan ke layanan yang tepat.' . "\n\n" . $warning;
        }

        return sprintf($mainTemplate, $poliName) . "\n\n" . $warning;
    }

    private function findPoliName(array $keywords): ?string
    {
        return Poli::query()
            ->orderBy('name')
            ->get()
            ->first(function ($poli) use ($keywords) {
                return Str::contains(Str::lower($poli->name), $keywords);
            })
            ?->name;
    }

    private function serviceContext(): string
    {
        try {
            return Poli::with(['doctors.schedules'])
                ->orderBy('name')
                ->get()
                ->map(function ($poli) {
                    $doctors = $poli->doctors->map(function ($doctor) {
                        return sprintf(
                            'Dr. %s (%s, kuota %s/hari): %s',
                            $doctor->name,
                            $doctor->specialty,
                            $doctor->daily_quota,
                            $this->formatDoctorSchedules($doctor)
                        );
                    })->implode('; ');

                    return sprintf(
                        '%s: %s',
                        $poli->name,
                        $doctors !== '' ? $doctors : 'belum ada dokter/jadwal tersedia'
                    );
                })
                ->implode("\n");
        } catch (\Throwable $exception) {
            report($exception);

            return '';
        }
    }

    private function weekdayName(string $weekday): string
    {
        return [
            'monday' => 'Senin',
            'tuesday' => 'Selasa',
            'wednesday' => 'Rabu',
            'thursday' => 'Kamis',
            'friday' => 'Jumat',
            'saturday' => 'Sabtu',
            'sunday' => 'Minggu',
        ][$weekday] ?? $weekday;
    }

    private function weekdayOrder(string $weekday): int
    {
        return [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 7,
        ][$weekday] ?? 99;
    }

    private function outOfScopeReply(): string
    {
        return 'Maaf, aku hanya bisa membantu pertanyaan seputar aplikasi antrian online puskesmas, booking, layanan puskesmas, dan informasi kesehatan umum yang terkait.';
    }
}

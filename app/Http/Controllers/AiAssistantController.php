<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FlashSale;
use App\Models\Product;
use App\Models\Store;
use App\Models\Voucher;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AiAssistantController extends Controller
{
    public function chat(Request $request): JsonResponse
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $message = trim($request->input('message'));
        $apiKey = env('GEMINI_API_KEY');

        if ($apiKey) {
            try {
                $categories = Category::pluck('name')->implode(', ');
                $activeFlashSale = FlashSale::active()->first();
                $flashSaleInfo = $activeFlashSale ? "Ada sesi Flash Sale aktif bernama '{$activeFlashSale->name}'." : "Saat ini belum ada sesi Flash Sale aktif.";

                $systemPrompt = "Kamu adalah Asisten AI resmi yang ramah dan cerdas untuk platform marketplace e-commerce 'SakserShop' (Indonesia).
Informasi Platform:
- Kategori yang tersedia: {$categories}.
- Info Promo: {$flashSaleInfo}
- Fitur utama: Belanja online, Flash Sale dengan diskon besar, Voucher Diskon Toko & Platform, Buka Toko Gratis untuk penjual, Panel Seller Center & Admin Panel.
Jawablah dengan bahasa Indonesia yang sopan, ramah, ringkas, dan jelas dalam format Markdown.";

                $response = Http::timeout(10)->post('https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=' . $apiKey, [
                    'contents' => [
                        [
                            'parts' => [
                                [
                                    'text' => $systemPrompt . "\n\nPertanyaan pengguna: " . $message
                                ]
                            ]
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                    if ($reply) {
                        return response()->json([
                            'reply' => Str::markdown($reply)
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('Gemini AI API Fallback: ' . $e->getMessage());
            }
        }

        $fallbackReply = $this->generateLocalResponse($message);

        return response()->json([
            'reply' => Str::markdown($fallbackReply)
        ]);
    }

    private function generateLocalResponse(string $message): string
    {
        $lower = strtolower($message);

        if (Str::contains($lower, ['halo', 'hai', 'hi', 'pagi', 'siang', 'sore', 'malam', 'assalamualaikum'])) {
            return "Halo! 👋 Selamat datang di **SakserShop**.\n\nAda yang bisa saya bantu hari ini? Anda bisa menanyakan seputar:\n- 🛍️ **Cara Belanja & Checkout**\n- 🏪 **Cara Buka Toko & Jualan**\n- ⚡ **Info Flash Sale & Diskon**\n- 🎟️ **Cara Menggunakan Voucher**\n- 📦 **Cek Pesanan & Pengiriman**";
        }

        if (Str::contains($lower, ['buka toko', 'daftar toko', 'jualan', 'seller', 'buat toko'])) {
            return "Untuk **membuka toko dan mulai berjualan** di SakserShop:\n1. Masuk ke menu profil lalu klik **Buka Toko Gratis** (atau akses rute `/customer/store/register`).\n2. Isi nama toko, domain/slug toko, dan deskripsi singkat.\n3. Tunggu persetujuan dari Admin SakserShop.\n4. Setelah disetujui, Anda dapat langsung mengakses **Seller Center** untuk mengunggah produk dan mengatur voucher!";
        }

        if (Str::contains($lower, ['flash sale', 'flashsale', 'diskon kilat', 'promo kilat'])) {
            $active = FlashSale::active()->first();
            if ($active) {
                return "⚡ **Flash Sale Sedang Berlangsung!**\n\nSesi aktif saat ini: **{$active->name}**.\nDapatkan diskon spesial dengan kuota terbatas langsung di halaman beranda atau etalase produk!";
            }
            return "⚡ Saat ini belum ada sesi Flash Sale yang aktif. Pantau terus halaman utama SakserShop untuk jadwal promo Flash Sale berikutnya!";
        }

        if (Str::contains($lower, ['voucher', 'kupon', 'kode promo', 'diskon'])) {
            $vouchers = Voucher::where('is_active', true)->take(3)->get();
            if ($vouchers->count() > 0) {
                $list = "";
                foreach ($vouchers as $v) {
                    $val = $v->type === 'percent' ? "{$v->amount}%" : "Rp " . number_format($v->amount, 0, ',', '.');
                    $list .= "- Kode **{$v->code}**: Diskon {$val} (Min. belanja Rp " . number_format($v->min_spend, 0, ',', '.') . ")\n";
                }
                return "🎟️ **Voucher Promo SakserShop yang Tersedia:**\n\n{$list}\nMasukkan kode kupon di atas saat proses checkout di keranjang belanja Anda!";
            }
            return "🎟️ Voucher promo dapat Anda temukan di halaman utama. Salin kode kupon dan gunakan saat checkout untuk mendapatkan potongan harga!";
        }

        if (Str::contains($lower, ['cara belanja', 'checkout', 'beli', 'bayar', 'pembayaran'])) {
            return "🛒 **Cara Belanja Mudah di SakserShop:**\n1. Pilih produk yang Anda inginkan lalu klik **Beli Sekarang** atau **+ Keranjang**.\n2. Masuk ke halaman **Keranjang Belanja** dan klik **Lanjut ke Checkout**.\n3. Masukkan alamat pengiriman lengkap Anda.\n4. Lakukan pembayaran via transfer bank sesuai nominal invoice dan unggah bukti transfer.";
        }

        if (Str::contains($lower, ['kategori', 'produk apa saja', 'jual apa'])) {
            $categories = Category::pluck('name')->implode(', ');
            return "📦 **Kategori Produk di SakserShop:**\n\n{$categories}.\n\nJelajahi berbagai pilihan produk berkualitas dengan memilih kategori di bagian atas navigasi.";
        }

        if (Str::contains($lower, ['pesanan', 'status', 'resi', 'kirim', 'pengiriman'])) {
            return "📦 **Cek Status Pesanan:**\nAnda dapat memantau status pesanan belanja Anda secara berkala melalui menu **Pesanan Saya** di dasbor akun Anda.";
        }

        if (Str::contains($lower, ['terima kasih', 'makasih', 'thanks', 'oke', 'ok', 'siap'])) {
            return "Sama-sama! 😊 Senang bisa membantu Anda. Selamat berbelanja di **SakserShop**!";
        }

        return "Terima kasih telah bertanya! 😊\n\nSebagai Asisten AI **SakserShop**, saya dapat membantu Anda seputar informasi produk, cara belanja, pembukaan toko, voucher promo, hingga status pesanan.\n\nSilakan ketik pertanyaan spesifik Anda atau pilih topik yang ingin Anda ketahui!";
    }
}

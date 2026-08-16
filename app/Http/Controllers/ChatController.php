<?php

namespace App\Http\Controllers;

use App\Models\Conversation;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(): View
    {
        $user = Auth::user();
        $userId = $user->id;

        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'messages' => fn ($q) => $q->latest()])
            ->latest('updated_at')
            ->get();

        $admins = collect();
        if ($user->role === 'super_admin') {
            $admins = User::where('role', 'admin')->where('id', '!=', $userId)->get();
        }

        return view('chat.index', compact('conversations', 'admins'));
    }

    // Memulai chat dengan pengguna baru (misal: Customer chat Seller toko)
    public function startConversation(User $receiver): RedirectResponse
    {
        $senderId = Auth::id();

        if ($senderId === $receiver->id) {
            return back()->with('error', 'Anda tidak dapat mengirim pesan ke diri sendiri.');
        }

        // Cari atau buat percakapan yang sudah ada
        $conversation = Conversation::where(function ($q) use ($senderId, $receiver) {
            $q->where('user_one_id', $senderId)->where('user_two_id', $receiver->id);
        })->orWhere(function ($q) use ($senderId, $receiver) {
            $q->where('user_one_id', $receiver->id)->where('user_two_id', $senderId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $senderId,
                'user_two_id' => $receiver->id,
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    // Menampilkan detail room obrolan
    public function show(Conversation $conversation): View
    {
        $userId = Auth::id();

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $partner = $conversation->user_one_id === $userId ? $conversation->userTwo : $conversation->userOne;

        // Tandai pesan sudah dibaca
        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        return view('chat.show', compact('conversation', 'partner', 'messages'));
    }

    // Kirim balasan pesan
    public function sendMessage(Request $request, Conversation $conversation): RedirectResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $userId = Auth::id();

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        Message::create([
            'conversation_id' => $conversation->id,
            'sender_id'       => $userId,
            'message'         => $request->message,
            'is_read'         => false,
        ]);

        $conversation->touch(); // Update timestamp percakapan

        return redirect()->route('chat.show', $conversation);
    }
}
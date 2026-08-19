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
    public function index(): View|RedirectResponse
    {
        $user = Auth::user();
        if ($user->role === 'seller') {
            return redirect()->route('seller.chat.cus');
        }

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

    public function sellerCustomerChat(): View
    {
        $userId = Auth::id();

        $allConversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'messages' => fn ($q) => $q->latest()])
            ->latest('updated_at')
            ->get();

        // Filter only customer partners
        $conversations = $allConversations->filter(function ($conv) use ($userId) {
            $partner = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;
            return $partner && $partner->role === 'customer';
        });

        $activeTab = 'cus';

        return view('chat.index', compact('conversations', 'activeTab'));
    }

    public function sellerAdminChat(): View
    {
        $userId = Auth::id();

        $allConversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne', 'userTwo', 'messages' => fn ($q) => $q->latest()])
            ->latest('updated_at')
            ->get();

        // Filter only admin or super_admin partners
        $conversations = $allConversations->filter(function ($conv) use ($userId) {
            $partner = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;
            return $partner && in_array($partner->role, ['admin', 'super_admin']);
        });

        $admins = User::whereIn('role', ['admin', 'super_admin'])->where('id', '!=', $userId)->get();
        $activeTab = 'admin';

        return view('chat.index', compact('conversations', 'admins', 'activeTab'));
    }

    public function startConversation(User $receiver): RedirectResponse
    {
        $sender = Auth::user();
        $senderId = $sender->id;

        if ($senderId === $receiver->id) {
            return back()->with('error', 'Anda tidak dapat mengirim pesan ke diri sendiri.');
        }

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

        if ($sender->role === 'seller') {
            if (in_array($receiver->role, ['admin', 'super_admin'])) {
                return redirect()->route('seller.chat.admin.show', $conversation);
            }
            return redirect()->route('seller.chat.cus.show', $conversation);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function show(Conversation $conversation): View
    {
        $userId = Auth::id();

        if ($conversation->user_one_id !== $userId && $conversation->user_two_id !== $userId) {
            abort(403);
        }

        $partner = $conversation->user_one_id === $userId ? $conversation->userTwo : $conversation->userOne;

        Message::where('conversation_id', $conversation->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $messages = $conversation->messages()->with('sender')->oldest()->get();

        return view('chat.show', compact('conversation', 'partner', 'messages'));
    }

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

        $conversation->touch();

        return redirect()->route('chat.show', $conversation);
    }
}
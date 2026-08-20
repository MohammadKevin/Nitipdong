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

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => 'Pesan berhasil dikirim.',
            ]);
        }

        return redirect()->route('chat.show', $conversation);
    }

    public function apiConversations(): \Illuminate\Http\JsonResponse
    {
        $userId = Auth::id();
        if (!$userId) {
            return response()->json(['status' => 'error', 'conversations' => []], 401);
        }

        $conversations = Conversation::where('user_one_id', $userId)
            ->orWhere('user_two_id', $userId)
            ->with(['userOne.store', 'userTwo.store', 'messages' => fn ($q) => $q->latest()])
            ->latest('updated_at')
            ->get();

        $data = $conversations->map(function ($conv) use ($userId) {
            $partner = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;
            if (!$partner) return null;

            $lastMsg = $conv->messages->first();
            $unreadCount = $conv->messages->where('sender_id', '!=', $userId)->where('is_read', false)->count();

            $partnerName = $partner->role === 'seller' && $partner->store ? $partner->store->name : $partner->name;
            $partnerAvatar = $partner->avatar_url;

            return [
                'id'            => $conv->id,
                'obfuscated_id' => $conv->obfuscated_id,
                'partner'       => [
                    'id'     => $partner->id,
                    'name'   => $partnerName,
                    'avatar' => $partnerAvatar,
                    'role'   => $partner->role,
                    'is_online' => true,
                ],
                'last_message'      => $lastMsg ? $lastMsg->message : 'Belum ada pesan',
                'last_message_time' => $lastMsg ? $lastMsg->created_at->diffForHumans(null, true) : '',
                'unread_count'      => $unreadCount,
                'full_url'          => route('chat.show', $conv),
            ];
        })->filter()->values();

        $totalUnread = $data->sum('unread_count');

        return response()->json([
            'status'        => 'success',
            'total_unread'  => $totalUnread,
            'conversations' => $data,
        ]);
    }

    protected function resolveConversationInstance($conversation): ?Conversation
    {
        if ($conversation instanceof Conversation) {
            return $conversation;
        }
        if (is_numeric($conversation)) {
            return Conversation::find((int) $conversation);
        }
        return Conversation::findByObfuscatedId((string) $conversation) ?: Conversation::find($conversation);
    }

    public function apiMessages($conversation): \Illuminate\Http\JsonResponse
    {
        $conv = $this->resolveConversationInstance($conversation);
        if (!$conv) {
            return response()->json(['status' => 'error', 'message' => 'Percakapan tidak ditemukan.'], 404);
        }

        $userId = Auth::id();
        if ($conv->user_one_id !== $userId && $conv->user_two_id !== $userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        // Mark as read
        Message::where('conversation_id', $conv->id)
            ->where('sender_id', '!=', $userId)
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $partner = $conv->user_one_id === $userId ? $conv->userTwo : $conv->userOne;
        $partnerName = $partner && $partner->role === 'seller' && $partner->store ? $partner->store->name : ($partner?->name ?? 'Pengguna');

        $messages = $conv->messages()->oldest()->get()->map(function ($msg) use ($userId) {
            return [
                'id'         => $msg->id,
                'sender_id'  => $msg->sender_id,
                'is_me'      => $msg->sender_id === $userId,
                'message'    => $msg->message,
                'time'       => $msg->created_at->format('H:i'),
                'created_at' => $msg->created_at->toIso8601String(),
            ];
        });

        return response()->json([
            'status'       => 'success',
            'conversation' => [
                'id'       => $conv->id,
                'full_url' => route('chat.show', $conv),
            ],
            'partner'      => [
                'id'     => $partner?->id,
                'name'   => $partnerName,
                'avatar' => $partner?->avatar_url ?? '/img/saksershop-logo.png',
                'role'   => $partner?->role ?? 'user',
            ],
            'messages'     => $messages,
        ]);
    }

    public function apiSendMessage(Request $request, $conversation): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $conv = $this->resolveConversationInstance($conversation);
        if (!$conv) {
            return response()->json(['status' => 'error', 'message' => 'Percakapan tidak ditemukan.'], 404);
        }

        $userId = Auth::id();
        if ($conv->user_one_id !== $userId && $conv->user_two_id !== $userId) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $msg = Message::create([
            'conversation_id' => $conv->id,
            'sender_id'       => $userId,
            'message'         => $request->message,
            'is_read'         => false,
        ]);

        $conv->touch();

        return response()->json([
            'status'  => 'success',
            'message' => [
                'id'        => $msg->id,
                'sender_id' => $msg->sender_id,
                'is_me'     => true,
                'message'   => $msg->message,
                'time'      => $msg->created_at->format('H:i'),
            ],
        ]);
    }

    public function apiStartConversation($receiver): \Illuminate\Http\JsonResponse
    {
        $sender = Auth::user();
        $senderId = $sender->id;

        $targetUser = null;
        if ($receiver instanceof User) {
            $targetUser = $receiver;
        } elseif (is_numeric($receiver)) {
            $targetUser = User::find($receiver);
        } else {
            $targetUser = User::findByObfuscatedId((string) $receiver) ?: User::find($receiver);
        }

        if (!$targetUser) {
            $store = \App\Models\Store::where('id', $receiver)->orWhere('slug', $receiver)->first();
            if ($store) {
                $targetUser = $store->user;
            }
        }

        if (!$targetUser) {
            return response()->json(['status' => 'error', 'message' => 'Penjual tidak ditemukan.'], 404);
        }

        if ($senderId === $targetUser->id) {
            return response()->json(['status' => 'error', 'message' => 'Tidak dapat chat dengan diri sendiri.'], 422);
        }

        $conversation = Conversation::where(function ($q) use ($senderId, $targetUser) {
            $q->where('user_one_id', $senderId)->where('user_two_id', $targetUser->id);
        })->orWhere(function ($q) use ($senderId, $targetUser) {
            $q->where('user_one_id', $targetUser->id)->where('user_two_id', $senderId);
        })->first();

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_one_id' => $senderId,
                'user_two_id' => $targetUser->id,
            ]);
        }

        $partnerName = $targetUser->role === 'seller' && $targetUser->store ? $targetUser->store->name : $targetUser->name;

        return response()->json([
            'status'          => 'success',
            'conversation_id' => $conversation->id,
            'full_url'        => route('chat.show', $conversation),
            'partner'         => [
                'id'     => $targetUser->id,
                'name'   => $partnerName,
                'avatar' => $targetUser->avatar_url,
                'role'   => $targetUser->role,
            ],
        ]);
    }
}
<!--
Inbox — sorted by latest message
Conversation::query()
    ->where(function ($q) use ($userId) {
        $q->where('sender_id', $userId)
          ->whereNull('sender_deleted_at');
    })
    ->orWhere(function ($q) use ($userId) {
        $q->where('recipient_id', $userId)
          ->whereNull('recipient_deleted_at');
    })
    ->orderByDesc('last_message_at')
    ->with(['lastMessage', 'otherUser'])
    ->paginate(20); -->

<!--
    Unread message count per conversation
    // Fast — uses the message_status index
MessageStatus::where('recipient_id', $userId)
    ->whereNull('read_at')
    ->whereHas('message', fn($q) => $q->where('conversation_id', $conversationId))
    ->count(); -->


    <!--
    Mark conversation as read
    // When user opens a conversation, stamp all their unread messages
MessageStatus::whereHas('message', fn($q) =>
    $q->where('conversation_id', $conversationId)
)
->where('recipient_id', $userId)
->whereNull('read_at')
->update([
    'read_at' => now(),
    'delivered_at' => DB::raw('COALESCE(delivered_at, NOW())'),
]);

// Also clear the notification badge
Notification::where('user_id', $userId)
    ->where('is_read', false)
    ->whereHas('message', fn($q) =>
        $q->where('conversation_id', $conversationId)
    )
    ->update(['is_read' => true, 'read_at' => now()]); -->


    <!--
    Total unread badge count
    // Notification badge — all unread across all conversations
Notification::where('user_id', $userId)
    ->where('is_read', false)
    ->count(); -->

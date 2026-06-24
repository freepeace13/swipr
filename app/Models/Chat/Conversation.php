<?php

namespace App\Models\Chat;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Conversation extends Model
{
    use HasFactory;

    protected $table = 'chat_conversations';

    protected $fillable = [
        'sender_id',
        'recipient_id',
        'sender_deleted_at',
        'recipient_deleted_at',
        'last_message_at',
    ];

    protected $casts = [
        'sender_deleted_at' => 'datetime',
        'recipient_deleted_at' => 'datetime',
        'last_message_at' => 'datetime',
    ];

    public static function between(User $userA, User $userB): ?self
    {
        return static::where(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userA->id)
              ->where('recipient_id', $userB->id);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('sender_id', $userB->id)
              ->where('recipient_id', $userA->id);
        })->first();
    }

    public function isParticipant(User $user): bool
    {
        return $this->sender_id === $user->id || $this->recipient_id === $user->id;
    }

    public function otherParticipantId(User $user): int
    {
        return $this->sender_id === $user->id
            ? $this->recipient_id
            : $this->sender_id;
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function lastMessage(): HasOne
    {
        return $this->hasOne(Message::class)->latestOfMany();
    }

    public function otherUser(?User $user = null): User
    {
        $user ??= auth()->user();

        return $this->sender_id === $user?->id
            ? $this->recipient
            : $this->sender;
    }
}

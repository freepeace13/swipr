<?php

namespace App\Models\Chat;

use App\Enums\Chat\MessageType;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Message extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'chat_messages';

    protected $fillable = [
        'conversation_id',
        'sender_id',
        'body',
        'type',
        'attachments',
    ];

    protected $casts = [
        'type' => MessageType::class,
        'attachments' => 'array',
    ];

    public function status(): HasMany
    {
        return $this->hasMany(MessageStatus::class);
    }

    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Normalized payload broadcast to clients over the websocket.
     *
     * @return array<string, mixed>
     */
    public function toBroadcastArray(): array
    {
        $this->loadMissing('sender');

        return [
            'id' => $this->id,
            'conversation_id' => $this->conversation_id,
            'sender_id' => $this->sender_id,
            'body' => $this->body,
            'type' => $this->type->value,
            'attachments' => $this->attachments,
            'created_at' => $this->created_at->toIso8601String(),
            'sender' => [
                'id' => $this->sender->id,
                'name' => $this->sender->name,
                'avatar' => $this->sender->avatar,
            ],
        ];
    }
}

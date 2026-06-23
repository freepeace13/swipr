<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_conversations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();

            // Per-user soft delete — each side can "clear" their chat independently
            $table->timestamp('sender_deleted_at')->nullable();
            $table->timestamp('recipient_deleted_at')->nullable();

            // Denormalized for fast inbox sorting — updated on every new message
            $table->timestamp('last_message_at')->nullable();
            $table->timestamps();

            // Prevent duplicate conversations between the same two users
            $table->unique(['sender_id', 'recipient_id']);
            $table->index('last_message_at');
            $table->index('sender_id');
            $table->index('recipient_id');
        });

        Schema::create('chat_messages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('conversation_id')->constrained('chat_conversations')->cascadeOnDelete();
            $table->foreignId('sender_id')->constrained('users')->cascadeOnDelete();

            $table->enum('type', ['text', 'image', 'file'])->default('text');
            $table->text('body')->nullable(); // null when type is image/file only
            $table->json('attachments')->nullable();  // [{ url, name, size, mime_type }]
            $table->softDeletes();
            $table->timestamps();

            $table->index(['conversation_id', 'created_at']); // primary query pattern
            $table->index('sender_id');
        });

        Schema::create('chat_message_status', function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();
            $table->foreignId('recipient_id')->constrained('users')->cascadeOnDelete();

            $table->timestamp('delivered_at')->nullable(); // device received it
            $table->timestamp('read_at')->nullable();       // user opened the conversation

            $table->unique(['message_id', 'recipient_id']); // one row per message per recipient
            $table->index(['recipient_id', 'read_at']);      // fast unread count query
        });

        Schema::create('chat_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('message_id')->constrained('chat_messages')->cascadeOnDelete();

            $table->enum('type', ['new_message'])->default('new_message');
            // Expand later: 'match', 'like', 'profile_view', etc.

            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->nullable();

            $table->index(['user_id', 'is_read']);   // unread notification badge count
            $table->index(['user_id', 'created_at']); // notification feed ordering
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_notifications');
        Schema::dropIfExists('chat_message_status');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_conversations');
    }
};

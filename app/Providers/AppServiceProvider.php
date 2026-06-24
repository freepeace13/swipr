<?php

namespace App\Providers;

use App\Actions\Chat\CreateConversation;
use App\Actions\Chat\DeleteConversation;
use App\Actions\Chat\DeleteMessage;
use App\Actions\Chat\ReadConversation;
use App\Actions\Chat\ReadMessage;
use App\Actions\Chat\SendMessage;
use App\Actions\Chat\UpdateMessage;
use App\Contracts\Chat\CreatesConversations;
use App\Contracts\Chat\DeletesConversations;
use App\Contracts\Chat\DeletesMessages;
use App\Contracts\Chat\ReadsConversations;
use App\Contracts\Chat\ReadsMessages;
use App\Contracts\Chat\SendsMessages;
use App\Contracts\Chat\UpdatesMessages;
use App\Models\Chat\Conversation;
use App\Models\Chat\Message;
use App\Models\User;
use App\Observers\UserObserver;
use App\Policies\Chat\ConversationPolicy;
use App\Policies\Chat\MessagePolicy;
use App\Policies\ProfilePolicy;
use App\View\Composers\CurrentUserComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(CreatesConversations::class, CreateConversation::class);
        $this->app->bind(DeletesConversations::class, DeleteConversation::class);
        $this->app->bind(ReadsConversations::class, ReadConversation::class);
        $this->app->bind(SendsMessages::class, SendMessage::class);
        $this->app->bind(DeletesMessages::class, DeleteMessage::class);
        $this->app->bind(UpdatesMessages::class, UpdateMessage::class);
        $this->app->bind(ReadsMessages::class, ReadMessage::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        User::observe(UserObserver::class);
        View::composer('*', CurrentUserComposer::class);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, ProfilePolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
    }
}

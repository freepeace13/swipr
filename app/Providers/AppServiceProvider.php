<?php

namespace App\Providers;

use App\Actions;
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
use App\Policies\Chat\ConversationPolicy;
use App\Policies\Chat\MessagePolicy;
use App\Policies\ProfilePolicy;
use App\View\Composers\CurrentUserComposer;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->configureBindings();
    }

    public function boot(): void
    {
        $this->registerPolicies();

        View::composer('*', CurrentUserComposer::class);
    }

    private function configureBindings(): void
    {
        $this->app->bind(CreatesConversations::class, Actions\Chat\CreateConversation::class);
        $this->app->bind(DeletesConversations::class, Actions\Chat\DeleteConversation::class);
        $this->app->bind(ReadsConversations::class, Actions\Chat\ReadConversation::class);
        $this->app->bind(SendsMessages::class, Actions\Chat\SendMessage::class);
        $this->app->bind(DeletesMessages::class, Actions\Chat\DeleteMessage::class);
        $this->app->bind(UpdatesMessages::class, Actions\Chat\UpdateMessage::class);
        $this->app->bind(ReadsMessages::class, Actions\Chat\ReadMessage::class);
    }

    protected function registerPolicies(): void
    {
        Gate::policy(User::class, ProfilePolicy::class);
        Gate::policy(Message::class, MessagePolicy::class);
        Gate::policy(Conversation::class, ConversationPolicy::class);
    }
}

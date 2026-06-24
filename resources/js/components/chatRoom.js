export default function chatRoom(config) {
    return {
        ...config,
        body: '',
        rows: 1,
        sending: false,
        othersTyping: false,
        channel: null,
        typingTimer: null,
        lastWhisper: 0,

        init() {
            this.scrollToBottom(true);
            this.subscribe();
            this.markRead();

            // Re-mark as read whenever the tab regains focus.
            window.addEventListener('focus', () => this.markRead());
        },

        subscribe() {
            if (!window.Echo) return;

            this.channel = window.Echo.private(`chat.conversation.${this.conversationId}`);

            this.channel
                .listen('.message.sent', (e) => this.onIncoming(e.message))
                .listen('.message.updated', (e) => this.updateMessageBody(e.message))
                .listen('.message.deleted', (e) => this.removeMessage(e.id))
                .listen('.messages.read', (e) => {
                    if (e.reader_id === this.otherId) this.markMineRead();
                });

            this.channel.listenForWhisper('typing', (e) => {
                if (e.userId === this.otherId) this.showTyping();
            });
        },

        async send() {
            if (!this.body.trim() || this.sending) return;

            this.sending = true;
            try {
                const res = await fetch(this.storeUrl, {
                    method: 'POST',
                    headers: this.headers(),
                    body: JSON.stringify({ body: this.body }),
                });
                if (!res.ok) throw await res.json();
                const data = await res.json();
                this.appendMessage(data.message);
                this.body = '';
                this.rows = 1;
            } catch (e) {
                console.error(e);
            } finally {
                this.sending = false;
                this.$nextTick(() => this.$refs.input.focus());
            }
        },

        onIncoming(msg) {
            this.appendMessage(msg);

            // The conversation is open in front of us, so acknowledge the read.
            if (document.hasFocus()) this.markRead();
        },

        appendMessage(msg) {
            if (document.querySelector(`[data-message-id="${msg.id}"]`)) return; // de-dupe

            const mine = msg.sender_id === this.meId;
            const tpl = document.getElementById(mine ? 'sent-message-template' : 'received-message-template');
            const clone = tpl.content.cloneNode(true);

            clone.querySelector('[data-message-id]').setAttribute('data-message-id', msg.id);
            clone.querySelector('[data-body]').textContent = msg.body ?? '';

            const time = new Date(msg.created_at);
            clone.querySelector('[data-time]').textContent = time.toLocaleTimeString('en-US', {
                hour: 'numeric',
                minute: '2-digit',
            });

            if (mine) {
                const del = clone.querySelector('[data-delete]');
                if (del) del.addEventListener('click', () => this.deleteMessage(msg.id));
            }

            this.list().appendChild(clone);
            this.scrollToBottom();
        },

        updateMessageBody(msg) {
            const node = document.querySelector(`[data-message-id="${msg.id}"] [data-body]`);
            if (node) node.textContent = msg.body ?? '';
        },

        removeMessage(id) {
            const node = document.querySelector(`[data-message-id="${id}"]`);
            if (node) node.remove();
        },

        markMineRead() {
            const icon =
                '<svg class="h-3.5 w-3.5 text-brand-500" viewBox="0 0 20 20" fill="currentColor">' +
                '<path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 0 1 .143 1.052l-8 10.5a.75.75 0 0 1-1.127.075l-4.5-4.5a.75.75 0 0 1 1.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 0 1 1.05-.143Z" clip-rule="evenodd" />' +
                '</svg>';

            document.querySelectorAll('[data-status-icon]').forEach((el) => {
                el.innerHTML = icon;
            });
        },

        async deleteMessage(id) {
            if (!confirm('Delete this message?')) return;

            try {
                const res = await fetch(`/conversations/${this.conversationId}/messages/${id}`, {
                    method: 'DELETE',
                    headers: this.headers(),
                });
                if (res.ok) this.removeMessage(id);
            } catch (e) {
                console.error(e);
            }
        },

        async markRead() {
            try {
                await fetch(this.readUrl, { method: 'POST', headers: this.headers() });
            } catch (e) {
                // Read receipts are best-effort.
            }
        },

        whisperTyping() {
            if (!this.channel) return;

            const now = Date.now();
            if (now - this.lastWhisper < 1500) return;

            this.lastWhisper = now;
            this.channel.whisper('typing', { userId: this.meId });
        },

        showTyping() {
            this.othersTyping = true;
            clearTimeout(this.typingTimer);
            this.typingTimer = setTimeout(() => {
                this.othersTyping = false;
            }, 2500);
        },

        list() {
            const list = document.getElementById('messages-list');
            const empty = document.getElementById('empty-state');
            if (empty) empty.style.display = 'none';
            list.style.display = '';

            return list;
        },

        scrollToBottom(force = false) {
            const c = document.getElementById('messages-container');
            if (!c) return;

            const nearBottom = c.scrollHeight - c.scrollTop - c.clientHeight < 150;
            if (force || nearBottom) {
                this.$nextTick(() => {
                    c.scrollTop = c.scrollHeight;
                });
            }
        },

        headers() {
            const headers = {
                'Content-Type': 'application/json',
                Accept: 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content,
            };

            // Lets the server exclude our own socket when broadcasting (->toOthers()).
            if (window.Echo && window.Echo.socketId()) {
                headers['X-Socket-ID'] = window.Echo.socketId();
            }

            return headers;
        },
    };
}

/**
 * Mini Chat JavaScript
 * 
 * Handles chat interactions for the web mini chat popover
 */

class MiniChat {
    constructor() {
        this.isOpen = false;
        this.isLoading = false;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        this.container = document.getElementById('mini-chat');
        this.toggleBtn = document.getElementById('mini-chat-toggle');
        this.closeBtn = document.getElementById('mini-chat-close');
        this.window = document.getElementById('mini-chat-window');
        this.messagesArea = document.getElementById('mini-chat-messages');
        this.quickRepliesArea = document.getElementById('mini-chat-quick-replies');
        this.input = document.getElementById('mini-chat-input');
        this.sendBtn = document.getElementById('mini-chat-send');

        if (this.container) {
            this.init();
        }
    }

    init() {
        // Toggle button
        this.toggleBtn.addEventListener('click', () => this.toggle());
        this.closeBtn.addEventListener('click', () => this.close());

        // Send message
        this.sendBtn.addEventListener('click', () => this.sendMessage());
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Close on click outside
        document.addEventListener('click', (e) => {
            if (this.isOpen && !this.container.contains(e.target)) {
                this.close();
            }
        });

        // Load welcome message when first opened
        this.hasLoadedWelcome = false;
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        this.isOpen = true;
        this.container.classList.add('open');
        this.input.focus();

        // Load welcome message on first open
        if (!this.hasLoadedWelcome) {
            this.loadWelcome();
            this.hasLoadedWelcome = true;
        }
    }

    close() {
        this.isOpen = false;
        this.container.classList.remove('open');
    }

    async loadWelcome() {
        this.showTyping();

        try {
            const response = await this.apiRequest('/api/web-chat/welcome', 'GET');
            this.hideTyping();

            if (response.success) {
                this.renderResponse(response.data);
            } else {
                this.showError(response.error);
            }
        } catch (error) {
            this.hideTyping();
            this.showError('ไม่สามารถเชื่อมต่อได้ กรุณาลองใหม่');
        }
    }

    async sendMessage() {
        const text = this.input.value.trim();
        if (!text || this.isLoading) return;

        // Clear input
        this.input.value = '';

        // Show user message
        this.addMessage(text, 'user');

        // Clear quick replies
        this.quickRepliesArea.innerHTML = '';

        // Send to server
        await this.processRequest('/api/web-chat/message', { text });
    }

    async sendAction(action, params) {
        // Clear quick replies
        this.quickRepliesArea.innerHTML = '';

        // Send to server
        await this.processRequest('/api/web-chat/action', { action, params });
    }

    async processRequest(url, data) {
        this.showTyping();
        this.isLoading = true;

        try {
            const response = await this.apiRequest(url, 'POST', data);
            this.hideTyping();
            this.isLoading = false;

            if (response.success) {
                this.renderResponse(response.data);
            } else {
                this.showError(response.error);
            }
        } catch (error) {
            this.hideTyping();
            this.isLoading = false;
            this.showError('เกิดข้อผิดพลาด กรุณาลองใหม่');
        }
    }

    async apiRequest(url, method = 'GET', data = null) {
        const options = {
            method,
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        };

        if (data && method !== 'GET') {
            options.body = JSON.stringify(data);
        }

        const response = await fetch(url, options);
        return response.json();
    }

    renderResponse(data) {
        switch (data.type) {
            case 'text':
                this.addMessage(data.text, 'bot');
                break;

            case 'menu':
                this.addMessage(data.text, 'bot');
                this.renderQuickReplies(data.quickReplies);
                break;

            case 'card':
                this.renderCard(data);
                break;

            default:
                this.addMessage(JSON.stringify(data), 'bot');
        }

        this.scrollToBottom();
    }

    addMessage(text, sender) {
        const message = document.createElement('div');
        message.className = `mini-chat-message ${sender}`;
        message.textContent = text;
        this.messagesArea.appendChild(message);
        this.scrollToBottom();
    }

    renderCard(data) {
        const card = document.createElement('div');
        card.className = 'mini-chat-card';

        // Header
        const header = document.createElement('div');
        header.className = 'mini-chat-card-header';
        header.style.background = data.headerColor || '#1DB446';
        header.innerHTML = `
            <div class="mini-chat-card-title">${this.escapeHtml(data.title)}</div>
            ${data.subtitle ? `<div class="mini-chat-card-subtitle">${this.escapeHtml(data.subtitle)}</div>` : ''}
        `;
        card.appendChild(header);

        // Body
        const body = document.createElement('div');
        body.className = 'mini-chat-card-body';

        if (data.rows && Array.isArray(data.rows)) {
            data.rows.forEach(row => {
                if (row.type === 'separator') {
                    const separator = document.createElement('div');
                    separator.className = 'mini-chat-card-row separator';
                    body.appendChild(separator);
                } else {
                    const rowEl = document.createElement('div');
                    rowEl.className = 'mini-chat-card-row';
                    
                    const labelClass = row.bold ? 'mini-chat-card-label bold' : 'mini-chat-card-label';
                    const valueStyle = row.valueColor ? `color: ${row.valueColor}` : '';
                    
                    rowEl.innerHTML = `
                        <span class="${labelClass}">${this.escapeHtml(row.label)}</span>
                        <span class="mini-chat-card-value" style="${valueStyle}">${this.escapeHtml(row.value)}</span>
                    `;
                    body.appendChild(rowEl);
                }
            });
        }

        card.appendChild(body);
        this.messagesArea.appendChild(card);
        this.scrollToBottom();
    }

    renderQuickReplies(replies) {
        this.quickRepliesArea.innerHTML = '';

        if (!replies || !Array.isArray(replies)) return;

        replies.forEach(reply => {
            const btn = document.createElement('button');
            btn.className = 'mini-chat-quick-reply';

            // Simple text reply (string)
            if (typeof reply === 'string') {
                btn.textContent = reply;
                btn.addEventListener('click', () => {
                    this.input.value = reply;
                    this.sendMessage();
                });
            }
            // Action reply (object)
            else if (typeof reply === 'object') {
                btn.textContent = reply.label;
                btn.addEventListener('click', () => {
                    if (reply.action) {
                        this.sendAction(reply.action, reply.data || {});
                    } else {
                        this.input.value = reply.label;
                        this.sendMessage();
                    }
                });
            }

            this.quickRepliesArea.appendChild(btn);
        });
    }

    showTyping() {
        // Remove existing typing indicator
        this.hideTyping();

        const typing = document.createElement('div');
        typing.className = 'mini-chat-typing';
        typing.id = 'mini-chat-typing';
        typing.innerHTML = `
            <div class="mini-chat-typing-dot"></div>
            <div class="mini-chat-typing-dot"></div>
            <div class="mini-chat-typing-dot"></div>
        `;
        this.messagesArea.appendChild(typing);
        this.scrollToBottom();
    }

    hideTyping() {
        const typing = document.getElementById('mini-chat-typing');
        if (typing) {
            typing.remove();
        }
    }

    showError(message) {
        this.addMessage('⚠️ ' + message, 'bot');
    }

    scrollToBottom() {
        this.messagesArea.scrollTop = this.messagesArea.scrollHeight;
    }

    escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Initialize when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    window.miniChat = new MiniChat();
});

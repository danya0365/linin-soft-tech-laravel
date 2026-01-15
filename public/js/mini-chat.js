/**
 * Mini Chat JavaScript
 * 
 * Handles chat interactions for the web mini chat popover
 * Redesigned with menu button and popup system
 */

class MiniChat {
    constructor() {
        this.isOpen = false;
        this.isLoading = false;
        this.menuOpen = false;
        this.submenuOpen = false;
        this.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

        // Elements
        this.container = document.getElementById('mini-chat');
        this.toggleBtn = document.getElementById('mini-chat-toggle');
        this.closeBtn = document.getElementById('mini-chat-close');
        this.window = document.getElementById('mini-chat-window');
        this.messagesArea = document.getElementById('mini-chat-messages');
        this.input = document.getElementById('mini-chat-input');
        this.sendBtn = document.getElementById('mini-chat-send');
        this.menuBtn = document.getElementById('mini-chat-menu-btn');
        this.menuPopup = document.getElementById('mini-chat-menu-popup');
        this.submenuPopup = document.getElementById('mini-chat-submenu-popup');
        this.submenuTitle = document.getElementById('mini-chat-submenu-title');
        this.submenuItems = document.getElementById('mini-chat-submenu-items');
        this.submenuBack = document.getElementById('mini-chat-submenu-back');

        if (this.container) {
            this.init();
        }
    }

    init() {
        // Prevent all clicks inside the chat window from propagating
        this.window.addEventListener('click', (e) => {
            e.stopPropagation();
        });

        // Toggle chat button
        this.toggleBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggle();
        });
        
        // Close button
        this.closeBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.close();
        });

        // Menu button
        this.menuBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.toggleMenu();
        });

        // Menu items
        this.menuPopup.querySelectorAll('.mini-chat-menu-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.stopPropagation();
                const command = item.getAttribute('data-command');
                this.executeCommand(command);
            });
        });

        // Submenu back button
        this.submenuBack.addEventListener('click', (e) => {
            e.stopPropagation();
            this.closeSubmenu();
            this.openMenu();
        });

        // Send message
        this.sendBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            this.sendMessage();
        });
        
        this.input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Click on input closes menus
        this.input.addEventListener('click', (e) => {
            e.stopPropagation();
            this.closeAllMenus();
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
        this.closeAllMenus();
    }

    toggleMenu() {
        if (this.menuOpen) {
            this.closeMenu();
        } else {
            this.closeSubmenu();
            this.openMenu();
        }
    }

    openMenu() {
        this.menuOpen = true;
        this.menuPopup.classList.add('show');
        this.menuBtn.classList.add('active');
    }

    closeMenu() {
        this.menuOpen = false;
        this.menuPopup.classList.remove('show');
        this.menuBtn.classList.remove('active');
    }

    openSubmenu(title, items) {
        this.submenuTitle.textContent = title;
        this.submenuItems.innerHTML = '';

        items.forEach(item => {
            const btn = document.createElement('button');
            btn.className = 'mini-chat-submenu-item';
            btn.textContent = item.label;
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (item.action) {
                    this.sendAction(item.action, item.data || {});
                } else {
                    this.executeCommand(item.label);
                }
                this.closeAllMenus();
            });
            this.submenuItems.appendChild(btn);
        });

        this.submenuOpen = true;
        this.submenuPopup.classList.add('show');
    }

    closeSubmenu() {
        this.submenuOpen = false;
        this.submenuPopup.classList.remove('show');
    }

    closeAllMenus() {
        this.closeMenu();
        this.closeSubmenu();
    }

    executeCommand(command) {
        this.closeAllMenus();
        this.addMessage(command, 'user');
        this.processRequest('/api/web-chat/message', { text: command });
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

        // Close menus
        this.closeAllMenus();

        // Show user message
        this.addMessage(text, 'user');

        // Send to server
        await this.processRequest('/api/web-chat/message', { text });
    }

    async sendAction(action, params) {
        this.closeAllMenus();
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
                // If there are quick replies with actions, show as submenu
                if (data.quickReplies && data.quickReplies.length > 0 && typeof data.quickReplies[0] === 'object') {
                    this.openSubmenu(data.title || 'เลือกรายการ', data.quickReplies);
                }
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
        // Create card container
        const card = document.createElement('div');
        card.className = 'mini-chat-card';

        // Create header
        const header = document.createElement('div');
        header.className = 'mini-chat-card-header';
        header.style.backgroundColor = data.headerColor || '#1DB446';
        
        // Title
        const title = document.createElement('div');
        title.className = 'mini-chat-card-title';
        title.textContent = data.title || '';
        header.appendChild(title);
        
        // Subtitle
        if (data.subtitle) {
            const subtitle = document.createElement('div');
            subtitle.className = 'mini-chat-card-subtitle';
            subtitle.textContent = data.subtitle;
            header.appendChild(subtitle);
        }
        
        card.appendChild(header);

        // Create body
        const body = document.createElement('div');
        body.className = 'mini-chat-card-body';

        // Render rows
        const rows = data.rows || [];
        for (let i = 0; i < rows.length; i++) {
            const row = rows[i];
            
            if (!row) continue;
            
            if (row.type === 'separator') {
                const separator = document.createElement('div');
                separator.className = 'mini-chat-card-row separator';
                body.appendChild(separator);
            } else if (row.bold && !row.value) {
                // Section header (bold with no value)
                const sectionHeader = document.createElement('div');
                sectionHeader.className = 'mini-chat-card-row';
                const label = document.createElement('span');
                label.className = 'mini-chat-card-label bold';
                label.textContent = row.label || '';
                sectionHeader.appendChild(label);
                body.appendChild(sectionHeader);
            } else {
                // Normal row
                const rowEl = document.createElement('div');
                rowEl.className = 'mini-chat-card-row';
                
                const label = document.createElement('span');
                label.className = row.bold ? 'mini-chat-card-label bold' : 'mini-chat-card-label';
                label.textContent = row.label || '';
                
                const value = document.createElement('span');
                value.className = 'mini-chat-card-value';
                if (row.valueColor) {
                    value.style.color = row.valueColor;
                }
                value.textContent = row.value || '';
                
                rowEl.appendChild(label);
                rowEl.appendChild(value);
                body.appendChild(rowEl);
            }
        }

        card.appendChild(body);
        this.messagesArea.appendChild(card);
        
        // Force layout recalculation
        card.offsetHeight;
        
        this.scrollToBottom();
    }

    showTyping() {
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

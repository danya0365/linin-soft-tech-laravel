{{-- Mini Chat Popover Component --}}
@auth
<div id="mini-chat" class="mini-chat">
    {{-- Toggle Button --}}
    <button id="mini-chat-toggle" class="mini-chat-toggle" title="Chat">
        <span class="chat-icon">💬</span>
        <span class="close-icon">✕</span>
    </button>

    {{-- Chat Window --}}
    <div id="mini-chat-window" class="mini-chat-window">
        {{-- Header --}}
        <div class="mini-chat-header">
            <div class="mini-chat-title">
                <span class="mini-chat-avatar">🤖</span>
                <span>LinenSoftTech Assistant</span>
            </div>
            <button id="mini-chat-close" class="mini-chat-close" title="ปิด">✕</button>
        </div>

        {{-- Messages Area --}}
        <div id="mini-chat-messages" class="mini-chat-messages">
            {{-- Messages will be appended here --}}
        </div>

        {{-- Menu Popup (shows above input when menu button clicked) --}}
        <div id="mini-chat-menu-popup" class="mini-chat-menu-popup">
            <div class="mini-chat-menu-title">📋 เลือกเมนู</div>
            <div class="mini-chat-menu-grid">
                <button class="mini-chat-menu-item" data-command="📊 สรุปวันนี้">
                    <span class="menu-icon">📊</span>
                    <span class="menu-label">สรุปวันนี้</span>
                </button>
                <button class="mini-chat-menu-item" data-command="👥 ลูกค้า">
                    <span class="menu-icon">👥</span>
                    <span class="menu-label">ลูกค้า</span>
                </button>
                <button class="mini-chat-menu-item" data-command="📦 สต๊อก">
                    <span class="menu-icon">📦</span>
                    <span class="menu-label">สต๊อก</span>
                </button>
                <button class="mini-chat-menu-item" data-command="⚡ พลังงาน">
                    <span class="menu-icon">⚡</span>
                    <span class="menu-label">พลังงาน</span>
                </button>
                <button class="mini-chat-menu-item" data-command="👷 พนักงาน">
                    <span class="menu-icon">👷</span>
                    <span class="menu-label">พนักงาน</span>
                </button>
                <button class="mini-chat-menu-item" data-command="⚙️ เครื่องจักร">
                    <span class="menu-icon">⚙️</span>
                    <span class="menu-label">เครื่องจักร</span>
                </button>
                <button class="mini-chat-menu-item" data-command="📈 รายงาน">
                    <span class="menu-icon">📈</span>
                    <span class="menu-label">รายงาน</span>
                </button>
            </div>
        </div>

        {{-- Sub Menu Popup (for nested menus like customer groups) --}}
        <div id="mini-chat-submenu-popup" class="mini-chat-submenu-popup">
            <div class="mini-chat-submenu-header">
                <button id="mini-chat-submenu-back" class="mini-chat-submenu-back">← กลับ</button>
                <span id="mini-chat-submenu-title">เลือกรายการ</span>
            </div>
            <div id="mini-chat-submenu-items" class="mini-chat-submenu-items">
                {{-- Dynamic items will be inserted here --}}
            </div>
        </div>

        {{-- Input Area --}}
        <div class="mini-chat-input-area">
            <button id="mini-chat-menu-btn" class="mini-chat-menu-btn" title="เมนู">
                <span>☰</span>
            </button>
            <input 
                type="text" 
                id="mini-chat-input" 
                class="mini-chat-input" 
                placeholder="พิมพ์ข้อความ..."
                autocomplete="off"
            >
            <button id="mini-chat-send" class="mini-chat-send" title="ส่ง">
                <span>➤</span>
            </button>
        </div>
    </div>
</div>

<link rel="stylesheet" href="{{ asset('css/mini-chat.css') }}?v={{ config('app.build') }}">
<script src="{{ asset('js/mini-chat.js') }}?v={{ config('app.build') }}" defer></script>
@endauth

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

        {{-- Quick Replies Area --}}
        <div id="mini-chat-quick-replies" class="mini-chat-quick-replies">
            {{-- Quick reply buttons will be rendered here --}}
        </div>

        {{-- Input Area --}}
        <div class="mini-chat-input-area">
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

<link rel="stylesheet" href="{{ asset('css/mini-chat.css') }}">
<script src="{{ asset('js/mini-chat.js') }}" defer></script>
@endauth

@extends('layouts.app')

@section('content')
<div class="ai-chat" id="ai-chat">
    {{-- Mobile sidebar backdrop --}}
    <div id="ai-chat-backdrop" class="ai-chat-backdrop"></div>

    {{-- Sidebar: รายการแชท --}}
    <aside id="ai-chat-sidebar" class="ai-chat-sidebar">
        <div class="ai-chat-sidebar-header">
            <button id="ai-chat-new-session" class="ai-chat-new-btn">＋ แชทใหม่</button>
        </div>
        <nav id="ai-chat-session-list" class="ai-chat-session-list">
            {{-- session items rendered by JS --}}
        </nav>
    </aside>

    {{-- Main chat area --}}
    <main class="ai-chat-main">
        {{-- Header --}}
        <header class="ai-chat-header">
            <button id="ai-chat-sidebar-toggle" class="ai-chat-icon-btn ai-chat-sidebar-toggle" title="รายการแชท">☰</button>
            <div class="ai-chat-header-title">
                <span class="ai-chat-avatar">🤖</span>
                <span class="ai-chat-title-text">AI Chat</span>
            </div>
            <select id="ai-chat-model" class="ai-chat-model-select" title="เลือกโมเดล">
                {{-- optgroup rendered by JS --}}
            </select>
            <span id="ai-chat-credit" class="ai-chat-credit" title="เครดิตคงเหลือ"></span>
            <button id="ai-chat-stats-btn" class="ai-chat-icon-btn" title="สถิติการใช้งาน">📊</button>
            <button id="ai-chat-settings-btn" class="ai-chat-icon-btn" title="ตั้งค่า">⚙️</button>
        </header>

        {{-- Messages --}}
        <div id="ai-chat-messages" class="ai-chat-messages">
            {{-- messages rendered by JS --}}
        </div>

        {{-- Composer --}}
        <div class="ai-chat-composer">
            <div id="ai-chat-context-preview" class="ai-chat-context-preview"></div>
            <div class="ai-chat-composer-row">
                <textarea
                    id="ai-chat-input"
                    class="ai-chat-input"
                    rows="1"
                    placeholder="พิมพ์ข้อความ... (Enter เพื่อส่ง, Shift+Enter ขึ้นบรรทัดใหม่)"
                ></textarea>
                <button id="ai-chat-send" class="ai-chat-send-btn" disabled>ส่ง</button>
                <button id="ai-chat-stop" class="ai-chat-stop-btn" style="display: none;">⏹ หยุด</button>
            </div>
        </div>
    </main>

    {{-- Modal: สถิติการใช้งาน --}}
    <div id="ai-chat-stats-modal" class="ai-chat-modal">
        <div class="ai-chat-modal-card">
            <div class="ai-chat-modal-header">
                <span>📊 สถิติการใช้งาน</span>
                <button class="ai-chat-modal-close" data-close="ai-chat-stats-modal">✕</button>
            </div>
            <div id="ai-chat-stats-body" class="ai-chat-modal-body"></div>
        </div>
    </div>

    {{-- Modal: ตั้งค่า --}}
    <div id="ai-chat-settings-modal" class="ai-chat-modal">
        <div class="ai-chat-modal-card">
            <div class="ai-chat-modal-header">
                <span>⚙️ ตั้งค่าแชท</span>
                <button class="ai-chat-modal-close" data-close="ai-chat-settings-modal">✕</button>
            </div>
            <div class="ai-chat-modal-body">
                <label class="ai-chat-field">
                    <span class="ai-chat-field-label">ประวัติที่ส่งให้ AI</span>
                    <select id="ai-chat-setting-history-mode" class="ai-chat-field-input">
                        <option value="recent">เฉพาะข้อความล่าสุด</option>
                        <option value="all">ทั้งหมด</option>
                    </select>
                </label>
                <label class="ai-chat-field" id="ai-chat-setting-max-history-field">
                    <span class="ai-chat-field-label">จำนวนข้อความล่าสุดที่ส่ง (1–100)</span>
                    <input type="number" id="ai-chat-setting-max-history" class="ai-chat-field-input" min="1" max="100">
                </label>
                <label class="ai-chat-field">
                    <span class="ai-chat-field-label">จำกัดความยาวคำตอบ (tokens, เว้นว่าง = ไม่จำกัด)</span>
                    <input type="number" id="ai-chat-setting-max-tokens" class="ai-chat-field-input" min="16" max="32768" placeholder="เช่น 1024">
                </label>
            </div>
            <div class="ai-chat-modal-footer">
                <button id="ai-chat-settings-save" class="ai-chat-primary-btn">บันทึก</button>
            </div>
        </div>
    </div>

    {{-- Error toast --}}
    <div id="ai-chat-toast" class="ai-chat-toast"></div>
</div>

<script>
    window.AI_CHAT_CONFIG = @json($aiChatConfig);
</script>
<link rel="stylesheet" href="{{ asset('css/ai-chat.css') }}?v={{ config('app.build') }}">
<script src="{{ asset('js/ai-chat.js') }}?v={{ config('app.build') }}" defer></script>
@endsection

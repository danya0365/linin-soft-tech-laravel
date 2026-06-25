/**
 * AI Chat (หน้า /ai-chat)
 *
 * UI แบบ jarvis-nextjs แต่:
 * - ประวัติแชทเก็บใน DB ต่อ user (ผ่าน /api/ai-chat/sessions)
 * - server เป็นเจ้าของ history — client ส่งเฉพาะข้อความใหม่
 * - streaming SSE พร้อม tool loop (ตอบข้อมูลธุรกิจได้) + tool_status events
 * - settings (context window ฯลฯ) ยังเก็บใน localStorage และส่งไปกับ request
 */

(function () {
    'use strict';

    var CONFIG = window.AI_CHAT_CONFIG || {};

    var STORAGE_SETTINGS = 'linin.ai-chat.settings';

    // heuristic เดียวกับ server: ~3 ตัวอักษร/token + overhead ต่อข้อความ
    var CHARS_PER_TOKEN = 3;
    var TOKENS_PER_MESSAGE_OVERHEAD = 4;

    var DEFAULT_SETTINGS = {
        historyMode: 'recent',      // 'recent' = ส่งเฉพาะ N ข้อความล่าสุด
        maxHistoryMessages: 10,
        maxResponseTokens: 1024,    // null = ไม่จำกัด
    };

    // ป้ายภาษาไทยของ tools (แสดงระหว่าง AI ดึงข้อมูล)
    var TOOL_LABELS = {
        get_today_summary: 'สรุปวันนี้',
        get_business_report: 'รายงานธุรกิจ',
        list_entities: 'รายชื่อกลุ่ม/หมวด',
        search_customers: 'ค้นหาลูกค้า',
        get_customer_detail: 'ข้อมูลลูกค้า',
        get_inventories_by_group: 'รายการสต๊อก',
        get_energy_logs: 'ประวัติพลังงาน',
        search_employees: 'ค้นหาพนักงาน',
        get_employee_detail: 'ข้อมูลพนักงาน',
        get_machine_list: 'รายการเครื่องจักร',
        get_machine_notes: 'บันทึกซ่อมบำรุง',
    };

    // จำว่าผู้ใช้เคยเห็นคู่มือแล้ว (เปิดอัตโนมัติเฉพาะครั้งแรก)
    var STORAGE_GUIDE_SEEN = 'linin.ai-chat.guide-seen';

    // หมวดความสามารถ "อ่านข้อมูล" — เปิดให้ staff ทุกคนที่เข้าแชทได้ (presentational)
    var READ_CAPABILITIES = [
        'รายงาน/สรุปประจำวัน รายรับ-รายจ่าย น้ำหนักผ้า',
        'ข้อมูลลูกค้า: ยอดบิล น้ำหนักผ้า ตามช่วงวันที่',
        'สต๊อก/ผ้า: รายการและจำนวนคงเหลือในแต่ละกลุ่ม',
        'การใช้พลังงาน: ประวัติการใช้แต่ละทรัพยากร',
        'พนักงาน: ค้นหา ดูผลงาน และเวลาทำงาน',
        'เครื่องจักร/รถ: รายการ สถานะ และบันทึกซ่อมบำรุง',
    ];

    // คำสั่งตัวอย่าง — คลิกแล้วเด้งใส่ช่องพิมพ์
    var READ_EXAMPLE_PROMPTS = [
        'สรุปวันนี้',
        'รายงานธุรกิจเดือนนี้',
        'ยอดลูกค้าโรงแรม A เดือนนี้',
        'สต๊อกผ้าเหลือเท่าไหร่',
        'พนักงานแผนกซักมีใครบ้าง',
        'เครื่องซักมีกี่เครื่อง สถานะเป็นยังไง',
    ];

    // คำสั่งตัวอย่างฝั่งจัดการข้อมูล (สร้าง/แก้ไข/ลบ) — แสดงเฉพาะผู้ใช้ที่มีสิทธิ์ (canWrite)
    var WRITE_EXAMPLE_PROMPTS = [
        'เพิ่มลูกค้าใหม่ชื่อ โรงแรม XYZ กลุ่มโรงแรม',
        'แก้ชื่อกลุ่มลูกค้า id 3 เป็น โรงแรม 5 ดาว',
        'ลบประเภทผ้า id 7',
    ];

    // งานประจำวัน (operational) — ทำได้ตามสิทธิ์ (ระบบเช็คให้ตอนสั่งจริง)
    var OPERATIONAL_CAPABILITIES = [
        'บันทึกการใช้พลังงาน (น้ำ/ไฟ/แก๊ส/ชีวมวล/น้ำมัน/เคมี)',
        'รับสต๊อกเข้า และเบิกสต๊อกออก',
        'ออกบิลเก็บเงินลูกค้า',
        'บันทึกค่าใช้จ่ายแผนก',
        'สร้างงานผ้า: ซัก/อบ/รีด/แพ็ค/เก็บ (พร้อมรายการผ้า)',
        'สร้างงานส่งผ้า (จากงานเก็บที่ปิดแล้ว)',
    ];
    var OPERATIONAL_EXAMPLE_PROMPTS = [
        'บันทึกค่าน้ำ 1200 หน่วย 850 บาท',
        'เบิกผงซักฟอก 30 ออกจากสต๊อก ต้นทุน 600',
        'ออกบิลลูกค้าโรงแรม A น้ำหนัก 500 ยอด 25000',
        'สร้างงานซักให้ลูกค้า A พนักงาน สมชาย ผ้าปูที่นอน 12 กก.',
        'สร้างงานส่งผ้าของลูกค้า A พนักงาน สมชาย',
    ];

    // ── Helpers ─────────────────────────────────────────────────

    function loadJson(key, fallback) {
        try {
            var raw = localStorage.getItem(key);
            return raw ? JSON.parse(raw) : fallback;
        } catch (e) {
            return fallback;
        }
    }

    function saveJson(key, value) {
        try {
            localStorage.setItem(key, JSON.stringify(value));
        } catch (e) {
            // storage ปิดใช้งาน — settings จะไม่ persist เฉยๆ
        }
    }

    function estimateTokens(text) {
        if (!text) return 0;
        return Math.ceil(text.length / CHARS_PER_TOKEN);
    }

    function getModelPricing(modelId) {
        var models = CONFIG.models || [];
        for (var i = 0; i < models.length; i++) {
            if (models[i].id === modelId) return models[i].pricing || null;
        }
        return null;
    }

    function calculateCostUsd(modelId, promptTokens, completionTokens) {
        var pricing = getModelPricing(modelId);
        if (!pricing) return null;
        return (promptTokens / 1000000) * pricing.inputPerMTok
            + (completionTokens / 1000000) * pricing.outputPerMTok;
    }

    function formatCost(cost) {
        if (cost === null || cost === undefined) return '';
        if (cost > 0 && cost < 0.0001) return '<$0.0001';
        return '$' + cost.toFixed(4);
    }

    function formatThb(amount) {
        return '฿' + (Number(amount) || 0).toFixed(2);
    }

    // ยอดที่จะถูกหักจริง (รวมค่าคอมแล้ว) — ใช้แสดงกับข้อความเก่าที่ไม่มี creditCharged
    function calculateChargeThb(modelId, promptTokens, completionTokens) {
        var usd = calculateCostUsd(modelId, promptTokens, completionTokens);
        if (usd === null) return null;
        var credit = CONFIG.credit || {};
        return usd * (credit.usdToThb || 0) * (1 + (credit.commissionPercent || 0) / 100);
    }

    // ── App ─────────────────────────────────────────────────────

    function AiChat() {
        this.sessions = [];          // metadata จาก server [{id,title,model,lastMessageAt}]
        this.messages = [];          // ข้อความของ session ที่เปิดอยู่
        this.activeSessionId = null; // null = แชทใหม่ยังไม่ถูกสร้างใน DB (lazy create)
        this.settings = Object.assign({}, DEFAULT_SETTINGS, loadJson(STORAGE_SETTINGS, {}));
        this.isStreaming = false;
        this.streamingText = '';
        this.toolStatusText = '';
        this.creditBalance = (CONFIG.credit || {}).balance || 0;
        this.abortController = null;
        this.csrfToken = (document.querySelector('meta[name="csrf-token"]') || {}).content;

        this.el = {
            root: document.getElementById('ai-chat'),
            backdrop: document.getElementById('ai-chat-backdrop'),
            sidebarToggle: document.getElementById('ai-chat-sidebar-toggle'),
            newSession: document.getElementById('ai-chat-new-session'),
            sessionList: document.getElementById('ai-chat-session-list'),
            model: document.getElementById('ai-chat-model'),
            messages: document.getElementById('ai-chat-messages'),
            contextPreview: document.getElementById('ai-chat-context-preview'),
            input: document.getElementById('ai-chat-input'),
            send: document.getElementById('ai-chat-send'),
            stop: document.getElementById('ai-chat-stop'),
            credit: document.getElementById('ai-chat-credit'),
            helpBtn: document.getElementById('ai-chat-help-btn'),
            helpModal: document.getElementById('ai-chat-help-modal'),
            helpBody: document.getElementById('ai-chat-help-body'),
            statsBtn: document.getElementById('ai-chat-stats-btn'),
            statsModal: document.getElementById('ai-chat-stats-modal'),
            statsBody: document.getElementById('ai-chat-stats-body'),
            settingsBtn: document.getElementById('ai-chat-settings-btn'),
            settingsModal: document.getElementById('ai-chat-settings-modal'),
            settingHistoryMode: document.getElementById('ai-chat-setting-history-mode'),
            settingMaxHistory: document.getElementById('ai-chat-setting-max-history'),
            settingMaxHistoryField: document.getElementById('ai-chat-setting-max-history-field'),
            settingMaxTokens: document.getElementById('ai-chat-setting-max-tokens'),
            settingsSave: document.getElementById('ai-chat-settings-save'),
            toast: document.getElementById('ai-chat-toast'),
        };

        if (this.el.root) {
            this.init();
        }
    }

    // ── API helper ──────────────────────────────────────────────

    AiChat.prototype.api = function (method, path, body) {
        var options = {
            method: method,
            headers: {
                'Accept': 'application/json',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
        };

        if (body !== undefined) {
            options.headers['Content-Type'] = 'application/json';
            options.body = JSON.stringify(body);
        }

        return fetch((CONFIG.apiBase || '/api/ai-chat') + path, options).then(function (response) {
            return response.json().catch(function () { return {}; }).then(function (data) {
                if (!response.ok || data.success === false) {
                    throw new Error(data.error || ('เกิดข้อผิดพลาด (HTTP ' + response.status + ')'));
                }
                return data;
            });
        });
    };

    AiChat.prototype.init = function () {
        var self = this;

        this.renderModelOptions();
        this.renderMessages();
        this.renderCredit();

        if (CONFIG.enabled === false) {
            this.showToast('ยังไม่ได้ตั้งค่า WAVESPEED_API_KEY — แชทจะใช้งานไม่ได้', 8000);
        }

        // โหลดรายการ session จาก server
        this.api('GET', '/sessions').then(function (data) {
            self.sessions = data.sessions || [];
            if (data.credit) {
                self.creditBalance = data.credit.balance || 0;
                self.renderCredit();
            }
            self.renderSessions();
            if (self.sessions.length) {
                self.selectSession(self.sessions[0].id);
            }
        }).catch(function (error) {
            self.showToast(error.message);
        });

        this.el.newSession.addEventListener('click', function () {
            if (self.isStreaming) return;
            self.startNewChat();
        });

        this.el.sidebarToggle.addEventListener('click', function () {
            self.el.root.classList.toggle('sidebar-open');
        });

        this.el.backdrop.addEventListener('click', function () {
            self.closeSidebar();
        });

        this.el.model.addEventListener('change', function () {
            var model = self.el.model.value;
            var session = self.activeSession();
            if (session) {
                session.model = model;
                self.api('PATCH', '/sessions/' + session.id, { model: model }).catch(function (error) {
                    self.showToast(error.message);
                });
            }
        });

        this.el.input.addEventListener('input', function () {
            self.autosizeInput();
            self.updateSendState();
            self.updateContextPreview();
        });

        this.el.input.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                self.sendMessage();
            }
        });

        this.el.send.addEventListener('click', function () {
            self.sendMessage();
        });

        this.el.stop.addEventListener('click', function () {
            if (self.abortController) {
                self.abortController.abort();
            }
        });

        // Modals
        this.el.helpBtn.addEventListener('click', function () {
            self.openHelp();
        });

        this.el.statsBtn.addEventListener('click', function () {
            self.el.statsModal.classList.add('open');
            self.renderStats();
        });

        this.el.settingsBtn.addEventListener('click', function () {
            self.fillSettingsForm();
            self.el.settingsModal.classList.add('open');
        });

        document.querySelectorAll('.ai-chat-modal-close').forEach(function (btn) {
            btn.addEventListener('click', function () {
                document.getElementById(btn.getAttribute('data-close')).classList.remove('open');
            });
        });

        [this.el.helpModal, this.el.statsModal, this.el.settingsModal].forEach(function (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) modal.classList.remove('open');
            });
        });

        this.el.settingHistoryMode.addEventListener('change', function () {
            self.el.settingMaxHistoryField.style.display =
                self.el.settingHistoryMode.value === 'recent' ? '' : 'none';
        });

        this.el.settingsSave.addEventListener('click', function () {
            self.saveSettingsForm();
        });

        this.updateSendState();

        // เปิดคู่มืออัตโนมัติเฉพาะครั้งแรกที่ใช้งาน (จำผ่าน localStorage)
        if (CONFIG.enabled !== false && !loadJson(STORAGE_GUIDE_SEEN, false)) {
            this.openHelp();
        }
    };

    // ── Help / guide ────────────────────────────────────────────

    AiChat.prototype.openHelp = function () {
        this.renderHelp();
        this.el.helpModal.classList.add('open');
        saveJson(STORAGE_GUIDE_SEEN, true);
    };

    /** ใส่ข้อความลงช่องพิมพ์ (ใช้ร่วมกันโดย example chips) */
    AiChat.prototype.fillInput = function (text) {
        this.el.input.value = text;
        this.autosizeInput();
        this.updateSendState();
        this.updateContextPreview();
        this.el.input.focus();
    };

    /** ปุ่มตัวอย่างคำสั่งที่คลิกได้ */
    AiChat.prototype.buildChip = function (text, closeHelpOnClick) {
        var self = this;
        var chip = document.createElement('button');
        chip.type = 'button';
        chip.className = 'ai-chat-chip';
        chip.textContent = text;
        chip.addEventListener('click', function () {
            if (closeHelpOnClick && self.el.helpModal) {
                self.el.helpModal.classList.remove('open');
            }
            self.fillInput(text);
        });
        return chip;
    };

    /** section เช็คลิสต์ (หัวข้อ + รายการติ๊กถูก) */
    AiChat.prototype.buildHelpChecklist = function (title, items) {
        var section = document.createElement('div');
        section.className = 'ai-chat-help-section';

        var heading = document.createElement('h4');
        heading.className = 'ai-chat-help-title';
        heading.textContent = title;
        section.appendChild(heading);

        var list = document.createElement('ul');
        list.className = 'ai-chat-help-list';
        items.forEach(function (text) {
            var li = document.createElement('li');
            li.textContent = text;
            list.appendChild(li);
        });
        section.appendChild(list);

        return section;
    };

    /** เนื้อหาคู่มือใน modal — เช็คลิสต์ความสามารถตามสิทธิ์ + วิธีใช้ + ตัวอย่าง */
    AiChat.prototype.renderHelp = function () {
        var self = this;
        var body = this.el.helpBody;
        if (!body) return;
        body.innerHTML = '';

        var caps = CONFIG.capabilities || {};
        var canWrite = !!caps.canWrite && (caps.writableEntities || []).length > 0;

        // 1) ถามข้อมูลได้
        body.appendChild(this.buildHelpChecklist('📊 ถามข้อมูลได้', READ_CAPABILITIES));

        // 2) สร้าง/แก้ไข/ลบข้อมูลได้ (ตามสิทธิ์)
        if (canWrite) {
            var labels = caps.writableEntities.map(function (e) { return e.label; });
            body.appendChild(this.buildHelpChecklist('✏️ สร้าง / แก้ไข / ลบข้อมูลได้ (ตามสิทธิ์ของคุณ)', labels));
        }

        // 3) งานประจำวัน (operational) — ทำได้ตามสิทธิ์
        body.appendChild(this.buildHelpChecklist('🧾 ทำงานประจำวันได้ (ตามสิทธิ์)', OPERATIONAL_CAPABILITIES));

        // 3) วิธีใช้
        var how = document.createElement('div');
        how.className = 'ai-chat-help-section';
        var howTitle = document.createElement('h4');
        howTitle.className = 'ai-chat-help-title';
        howTitle.textContent = '🪄 วิธีใช้';
        how.appendChild(howTitle);

        var steps = document.createElement('ol');
        steps.className = 'ai-chat-help-steps';
        var stepTexts = [
            'พิมพ์เป็นภาษาคนปกติ เช่น “สรุปวันนี้” หรือ “ยอดลูกค้าโรงแรม A เดือนนี้”',
            'ถ้าต้องอ้างถึงกลุ่ม/แผนก/ประเภท ระบบจะค้นหารหัส (id) ให้เองอัตโนมัติ',
        ];
        if (canWrite) {
            stepTexts.push('เวลาสั่งสร้าง/แก้ไข/ลบข้อมูล AI จะแสดงตัวอย่าง (preview) ให้ตรวจก่อน — พิมพ์ “ยืนยัน” จึงทำจริง');
            stepTexts.push('การลบจะถูกกันถ้ามีข้อมูลอื่นผูกอยู่ (เช่น กลุ่มที่ยังมีลูกค้า) และลบแล้วยังกู้คืนได้');
            stepTexts.push('แนบรูปภาพผ่านแชทไม่ได้ ช่องรูปจะเว้นว่างไว้ ไปเพิ่มทีหลังที่หน้าจัดการได้');
        }
        stepTexts.forEach(function (text) {
            var li = document.createElement('li');
            li.textContent = text;
            steps.appendChild(li);
        });
        how.appendChild(steps);
        body.appendChild(how);

        // 4) ตัวอย่างคำสั่ง (คลิกได้)
        var ex = document.createElement('div');
        ex.className = 'ai-chat-help-section';
        var exTitle = document.createElement('h4');
        exTitle.className = 'ai-chat-help-title';
        exTitle.textContent = '💡 ตัวอย่างคำสั่ง (คลิกเพื่อใช้)';
        ex.appendChild(exTitle);

        var chipRow = document.createElement('div');
        chipRow.className = 'ai-chat-chip-row';
        var prompts = READ_EXAMPLE_PROMPTS.slice();
        if (canWrite) {
            prompts = prompts.concat(WRITE_EXAMPLE_PROMPTS);
        }
        prompts = prompts.concat(OPERATIONAL_EXAMPLE_PROMPTS);
        prompts.forEach(function (text) {
            chipRow.appendChild(self.buildChip(text, true));
        });
        ex.appendChild(chipRow);
        body.appendChild(ex);
    };

    /** welcome panel ในแชทใหม่ (empty state) */
    AiChat.prototype.buildWelcomePanel = function () {
        var self = this;
        var caps = CONFIG.capabilities || {};
        var canWrite = !!caps.canWrite && (caps.writableEntities || []).length > 0;

        var wrap = document.createElement('div');
        wrap.className = 'ai-chat-welcome';

        var avatar = document.createElement('div');
        avatar.className = 'ai-chat-welcome-avatar';
        avatar.textContent = '🤖';
        wrap.appendChild(avatar);

        var title = document.createElement('div');
        title.className = 'ai-chat-welcome-title';
        title.textContent = 'เริ่มคุยกับ AI ได้เลย';
        wrap.appendChild(title);

        var sub = document.createElement('div');
        sub.className = 'ai-chat-welcome-sub';
        sub.textContent = canWrite
            ? 'ถามข้อมูลธุรกิจ หรือสั่งสร้างข้อมูลใหม่ผ่านแชทได้เลย'
            : 'ถามข้อมูลธุรกิจได้เลย เช่น ยอดขาย ลูกค้า สต๊อก พลังงาน';
        wrap.appendChild(sub);

        var chipRow = document.createElement('div');
        chipRow.className = 'ai-chat-chip-row';
        var prompts = READ_EXAMPLE_PROMPTS.slice(0, 4);
        if (canWrite) {
            prompts.push(WRITE_EXAMPLE_PROMPTS[0]);
        }
        prompts.forEach(function (text) {
            chipRow.appendChild(self.buildChip(text, false));
        });
        wrap.appendChild(chipRow);

        var more = document.createElement('button');
        more.type = 'button';
        more.className = 'ai-chat-welcome-more';
        more.textContent = '❓ ดูทั้งหมดว่าแชทนี้ทำอะไรได้บ้าง';
        more.addEventListener('click', function () {
            self.openHelp();
        });
        wrap.appendChild(more);

        return wrap;
    };

    // ── Sessions ────────────────────────────────────────────────

    AiChat.prototype.activeSession = function () {
        var id = this.activeSessionId;
        return this.sessions.find(function (s) { return s.id === id; }) || null;
    };

    AiChat.prototype.startNewChat = function () {
        // lazy create — session จริงถูกสร้างใน DB ตอนส่งข้อความแรก
        this.activeSessionId = null;
        this.messages = [];
        this.el.model.value = CONFIG.defaultModel || '';
        this.renderSessions();
        this.renderMessages();
        this.updateContextPreview();
        this.closeSidebar();
        this.el.input.focus();
    };

    AiChat.prototype.selectSession = function (id) {
        var self = this;
        if (this.isStreaming) return;

        this.activeSessionId = id;
        this.messages = [];
        this.renderSessions();
        this.closeSidebar();

        this.api('GET', '/sessions/' + id).then(function (data) {
            // กันกรณีผู้ใช้สลับ session ไปแล้วระหว่างรอโหลด
            if (self.activeSessionId !== id) return;
            self.messages = data.messages || [];
            if (data.session && data.session.model) {
                self.el.model.value = data.session.model;
            }
            self.renderMessages();
            self.updateContextPreview();
        }).catch(function (error) {
            self.showToast(error.message);
        });
    };

    AiChat.prototype.renameSession = function (id) {
        var self = this;
        var session = this.sessions.find(function (s) { return s.id === id; });
        if (!session) return;

        var title = prompt('ตั้งชื่อแชท', session.title);
        if (!title || !title.trim()) return;

        this.api('PATCH', '/sessions/' + id, { title: title.trim() }).then(function (data) {
            session.title = data.session.title;
            self.renderSessions();
        }).catch(function (error) {
            self.showToast(error.message);
        });
    };

    AiChat.prototype.deleteSession = function (id) {
        var self = this;
        if (!confirm('ลบแชทนี้?')) return;

        this.api('DELETE', '/sessions/' + id).then(function () {
            self.sessions = self.sessions.filter(function (s) { return s.id !== id; });
            if (self.activeSessionId === id) {
                self.activeSessionId = null;
                self.messages = [];
                if (self.sessions.length) {
                    self.selectSession(self.sessions[0].id);
                }
            }
            self.renderSessions();
            self.renderMessages();
        }).catch(function (error) {
            self.showToast(error.message);
        });
    };

    AiChat.prototype.closeSidebar = function () {
        this.el.root.classList.remove('sidebar-open');
    };

    // ── Send / stream ───────────────────────────────────────────

    AiChat.prototype.sendMessage = function () {
        var self = this;
        var content = this.el.input.value.trim();
        if (!content || this.isStreaming) return;

        var model = this.el.model.value || CONFIG.defaultModel;

        // session ยังไม่มีใน DB → สร้างก่อน (lazy create)
        if (this.activeSessionId === null) {
            this.api('POST', '/sessions', { model: model }).then(function (data) {
                self.sessions.unshift(data.session);
                self.activeSessionId = data.session.id;
                self.renderSessions();
                self.streamTo(data.session.id, content, model);
            }).catch(function (error) {
                self.showToast(error.message);
            });
            return;
        }

        this.streamTo(this.activeSessionId, content, model);
    };

    AiChat.prototype.streamTo = function (sessionId, content, model) {
        var self = this;

        // แสดงข้อความ user ทันที (optimistic)
        this.messages.push({
            id: null,
            role: 'user',
            content: content,
            createdAt: new Date().toISOString(),
        });

        this.el.input.value = '';
        this.autosizeInput();
        this.renderMessages();

        this.isStreaming = true;
        this.streamingText = '';
        this.toolStatusText = '';
        this.serverError = null;
        this.serverUsage = null;
        this.lastCreditCharged = null;
        this.abortController = new AbortController();
        this.updateSendState();
        this.renderStreamingBubble();

        var payload = {
            content: content,
            model: model,
            historyMode: this.settings.historyMode,
            maxHistoryMessages: this.settings.maxHistoryMessages,
        };
        if (this.settings.maxResponseTokens) {
            payload.maxTokens = this.settings.maxResponseTokens;
        }

        fetch((CONFIG.apiBase || '/api/ai-chat') + '/sessions/' + sessionId + '/stream', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/event-stream',
                'X-CSRF-TOKEN': this.csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify(payload),
            signal: this.abortController.signal,
        }).then(function (response) {
            if (!response.ok) {
                return response.json().catch(function () { return {}; }).then(function (data) {
                    throw new Error(data.error || (data.errors ? 'ข้อมูลที่ส่งไม่ถูกต้อง' : 'เกิดข้อผิดพลาด (HTTP ' + response.status + ')'));
                });
            }
            return self.readSseStream(response.body);
        }).then(function () {
            if (self.serverError) {
                self.finishStreaming(model, true);
                self.showToast(self.serverError);
                return;
            }
            self.finishStreaming(model, false);
        }).catch(function (error) {
            if (error && error.name === 'AbortError') {
                // ผู้ใช้กดหยุด — server บันทึก partial ให้แล้ว แสดงที่มีไว้
                self.finishStreaming(model, false, true);
                return;
            }
            self.finishStreaming(model, true);
            self.showToast(error.message || 'เชื่อมต่อ AI ไม่สำเร็จ กรุณาลองใหม่อีกครั้ง');
        });
    };

    /**
     * อ่าน SSE stream ตาม protocol ของ server:
     * meta / content deltas / tool_status / usage / done / error / [DONE]
     */
    AiChat.prototype.readSseStream = function (body) {
        var self = this;
        var reader = body.getReader();
        var decoder = new TextDecoder('utf-8');
        var buffer = '';

        function processPayload(payload) {
            if (payload === '[DONE]') return true;

            var parsed;
            try {
                parsed = JSON.parse(payload);
            } catch (e) {
                return false;
            }

            var type = parsed.type || '';

            if (type === 'meta') {
                // auto-title จาก server
                if (parsed.title) {
                    var session = self.activeSession();
                    if (session) {
                        session.title = parsed.title;
                        self.renderSessions();
                    }
                }
                return false;
            }

            if (type === 'tool_status') {
                if (parsed.status === 'running') {
                    var label = parsed.label || TOOL_LABELS[parsed.name] || parsed.name;
                    self.toolStatusText = '🔍 กำลังดึงข้อมูล: ' + label + '…';
                } else {
                    self.toolStatusText = '';
                }
                self.updateStreamingBubble();
                return false;
            }

            if (type === 'error') {
                self.serverError = parsed.message || 'เกิดข้อผิดพลาดจาก AI';
                return false;
            }

            if (type === 'done') {
                if (parsed.creditCharged !== null && parsed.creditCharged !== undefined) {
                    self.lastCreditCharged = Number(parsed.creditCharged);
                }
                if (parsed.creditBalance !== null && parsed.creditBalance !== undefined) {
                    self.creditBalance = Number(parsed.creditBalance);
                    self.renderCredit();
                }
                return false;
            }

            var delta = (((parsed.choices || [])[0] || {}).delta || {}).content || '';
            if (delta) {
                self.streamingText += delta;
                self.updateStreamingBubble();
            }

            if (parsed.usage) {
                self.serverUsage = {
                    promptTokens: Number(parsed.usage.prompt_tokens) || 0,
                    cachedTokens: Number(parsed.usage.cached_tokens) || 0,
                    completionTokens: Number(parsed.usage.completion_tokens) || 0,
                    estimated: !!parsed.estimated,
                };
            }

            return false;
        }

        return new Promise(function (resolve, reject) {
            function pump() {
                reader.read().then(function (step) {
                    if (step.done) {
                        resolve();
                        return;
                    }

                    buffer += decoder.decode(step.value, { stream: true });

                    var events = buffer.split('\n\n');
                    buffer = events.pop() || '';

                    for (var i = 0; i < events.length; i++) {
                        var lines = events[i].split('\n');
                        for (var j = 0; j < lines.length; j++) {
                            if (lines[j].indexOf('data:') !== 0) continue;
                            if (processPayload(lines[j].slice(5).trim())) {
                                resolve();
                                return;
                            }
                        }
                    }

                    pump();
                }).catch(reject);
            }
            pump();
        });
    };

    AiChat.prototype.finishStreaming = function (model, failed, aborted) {
        this.isStreaming = false;
        this.abortController = null;
        this.toolStatusText = '';

        if (this.streamingText && !failed) {
            this.messages.push({
                id: null,
                role: 'assistant',
                content: this.streamingText,
                model: model,
                createdAt: new Date().toISOString(),
                isPartial: !!aborted,
                usage: this.serverUsage,
                creditCharged: this.lastCreditCharged,
            });

            // ขยับ session ขึ้นบนสุด
            var self = this;
            var idx = this.sessions.findIndex(function (s) { return s.id === self.activeSessionId; });
            if (idx > 0) {
                this.sessions.unshift(this.sessions.splice(idx, 1)[0]);
                this.renderSessions();
            }
        }

        this.streamingText = '';
        this.serverUsage = null;
        this.updateSendState();
        this.renderMessages();
        this.updateContextPreview();
        this.el.input.focus();

        // เตือนเครดิตใกล้หมด/หมด หลังจบแต่ละคำตอบ
        var credit = CONFIG.credit || {};
        if (!failed) {
            if (this.creditBalance <= 0) {
                this.showToast('เครดิตหมดแล้ว กรุณาติดต่อผู้ดูแลระบบเพื่อเติมเครดิต', 8000);
            } else if (credit.lowThreshold && this.creditBalance < credit.lowThreshold) {
                this.showToast('เครดิตใกล้หมด (เหลือ ' + formatThb(this.creditBalance) + ') กรุณาติดต่อผู้ดูแลเพื่อเติมเครดิต', 8000);
            }
        }
    };

    AiChat.prototype.renderCredit = function () {
        var el = this.el.credit;
        if (!el) return;

        el.textContent = 'เครดิต: ' + formatThb(this.creditBalance);
        el.classList.remove('low', 'empty');

        var credit = CONFIG.credit || {};
        if (this.creditBalance <= 0) {
            el.classList.add('empty');
        } else if (credit.lowThreshold && this.creditBalance < credit.lowThreshold) {
            el.classList.add('low');
        }
    };

    // ── Rendering ───────────────────────────────────────────────

    AiChat.prototype.renderModelOptions = function () {
        var select = this.el.model;
        select.innerHTML = '';

        var groups = {};
        var order = [];
        (CONFIG.models || []).forEach(function (m) {
            if (!groups[m.vendor]) {
                groups[m.vendor] = [];
                order.push(m.vendor);
            }
            groups[m.vendor].push(m);
        });

        order.forEach(function (vendor) {
            var optgroup = document.createElement('optgroup');
            optgroup.label = vendor;
            groups[vendor].forEach(function (m) {
                var option = document.createElement('option');
                option.value = m.id;
                option.textContent = m.label;
                option.title = m.description || '';
                optgroup.appendChild(option);
            });
            select.appendChild(optgroup);
        });

        select.value = CONFIG.defaultModel || '';
    };

    AiChat.prototype.renderSessions = function () {
        var self = this;
        var list = this.el.sessionList;
        list.innerHTML = '';

        if (!this.sessions.length) {
            var empty = document.createElement('div');
            empty.className = 'ai-chat-session-empty';
            empty.textContent = 'ยังไม่มีแชท';
            list.appendChild(empty);
            return;
        }

        this.sessions.forEach(function (session) {
            var item = document.createElement('div');
            item.className = 'ai-chat-session-item' + (session.id === self.activeSessionId ? ' active' : '');

            var titleBtn = document.createElement('button');
            titleBtn.className = 'ai-chat-session-title';
            titleBtn.type = 'button';
            titleBtn.textContent = session.title;
            titleBtn.title = session.title;
            titleBtn.addEventListener('click', function () { self.selectSession(session.id); });

            var renameBtn = document.createElement('button');
            renameBtn.className = 'ai-chat-session-action';
            renameBtn.type = 'button';
            renameBtn.textContent = '✏️';
            renameBtn.title = 'ตั้งชื่อ';
            renameBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                self.renameSession(session.id);
            });

            var deleteBtn = document.createElement('button');
            deleteBtn.className = 'ai-chat-session-action';
            deleteBtn.type = 'button';
            deleteBtn.textContent = '🗑️';
            deleteBtn.title = 'ลบ';
            deleteBtn.addEventListener('click', function (e) {
                e.stopPropagation();
                self.deleteSession(session.id);
            });

            item.appendChild(titleBtn);
            item.appendChild(renameBtn);
            item.appendChild(deleteBtn);
            list.appendChild(item);
        });
    };

    AiChat.prototype.formatTime = function (iso) {
        var d = new Date(iso);
        var pad = function (n) { return n < 10 ? '0' + n : '' + n; };
        return pad(d.getHours()) + ':' + pad(d.getMinutes());
    };

    AiChat.prototype.formatMessageUsage = function (message) {
        if (!message.usage) return '';
        var u = message.usage;
        var prefix = u.estimated ? '~' : '';
        var text = prefix + '↓' + u.promptTokens;
        if (u.cachedTokens > 0) {
            text += ' (cache ' + u.cachedTokens + ')';
        }
        text += ' ↑' + u.completionTokens + ' tokens';

        // ยอดหักจริงจาก server ถ้ามี ไม่งั้นคำนวณจาก pricing (ข้อความเก่า)
        var thb = (message.creditCharged !== null && message.creditCharged !== undefined)
            ? Number(message.creditCharged)
            : calculateChargeThb(message.model || '', u.promptTokens, u.completionTokens);
        if (thb !== null) {
            text += ' · ฿' + thb.toFixed(4);
        }
        return text;
    };

    AiChat.prototype.renderMessages = function () {
        var self = this;
        var container = this.el.messages;
        container.innerHTML = '';

        if (!this.messages.length && !this.isStreaming) {
            container.appendChild(this.buildWelcomePanel());
            return;
        }

        this.messages.forEach(function (message) {
            container.appendChild(self.buildMessageEl(message));
        });

        if (this.isStreaming) {
            this.renderStreamingBubble();
        } else {
            this.scrollToBottom();
        }
    };

    AiChat.prototype.buildMessageEl = function (message) {
        var wrapper = document.createElement('div');
        wrapper.className = 'ai-chat-msg ' + (message.role === 'user' ? 'user' : 'assistant');

        var bubble = document.createElement('div');
        bubble.className = 'ai-chat-bubble';

        var text = document.createElement('p');
        text.className = 'ai-chat-bubble-text';
        text.textContent = message.content;
        bubble.appendChild(text);

        var meta = document.createElement('p');
        meta.className = 'ai-chat-bubble-meta';
        var metaText = this.formatTime(message.createdAt);
        var usageText = this.formatMessageUsage(message);
        if (usageText) metaText += ' · ' + usageText;
        if (message.isPartial) metaText += ' · ⏹ หยุดกลางทาง';
        meta.textContent = metaText;
        bubble.appendChild(meta);

        wrapper.appendChild(bubble);
        return wrapper;
    };

    AiChat.prototype.renderStreamingBubble = function () {
        var container = this.el.messages;

        var existing = document.getElementById('ai-chat-streaming');
        if (existing) existing.remove();

        var wrapper = document.createElement('div');
        wrapper.className = 'ai-chat-msg assistant';
        wrapper.id = 'ai-chat-streaming';

        var bubble = document.createElement('div');
        bubble.className = 'ai-chat-bubble';

        var text = document.createElement('p');
        text.className = 'ai-chat-bubble-text';
        text.id = 'ai-chat-streaming-text';

        var status = document.createElement('p');
        status.className = 'ai-chat-tool-status';
        status.id = 'ai-chat-streaming-status';

        bubble.appendChild(text);
        bubble.appendChild(status);
        wrapper.appendChild(bubble);
        container.appendChild(wrapper);
        this.updateStreamingBubble();
    };

    AiChat.prototype.updateStreamingBubble = function () {
        var text = document.getElementById('ai-chat-streaming-text');
        var status = document.getElementById('ai-chat-streaming-status');
        if (!text) return;

        text.textContent = this.streamingText;
        var cursor = document.createElement('span');
        cursor.className = 'ai-chat-cursor';
        cursor.textContent = '▌';
        text.appendChild(cursor);

        if (status) {
            status.textContent = this.toolStatusText;
            status.style.display = this.toolStatusText ? '' : 'none';
        }

        this.scrollToBottom();
    };

    AiChat.prototype.scrollToBottom = function () {
        this.el.messages.scrollTop = this.el.messages.scrollHeight;
    };

    AiChat.prototype.autosizeInput = function () {
        var input = this.el.input;
        input.style.height = 'auto';
        input.style.height = Math.min(input.scrollHeight, 128) + 'px';
    };

    AiChat.prototype.updateSendState = function () {
        var hasText = !!this.el.input.value.trim();
        this.el.send.disabled = !hasText || this.isStreaming;
        this.el.send.style.display = this.isStreaming ? 'none' : '';
        this.el.stop.style.display = this.isStreaming ? '' : 'none';
    };

    AiChat.prototype.updateContextPreview = function () {
        var preview = this.el.contextPreview;
        if (!this.messages.length) {
            preview.textContent = '';
            return;
        }

        var context = this.messages;
        if (this.settings.historyMode !== 'all') {
            context = context.slice(-Math.max(1, this.settings.maxHistoryMessages));
        }

        var tokens = estimateTokens(this.el.input.value);
        context.forEach(function (m) {
            tokens += estimateTokens(m.content) + TOKENS_PER_MESSAGE_OVERHEAD;
        });

        preview.textContent = 'จะส่ง ' + context.length + '/' + this.messages.length
            + ' ข้อความ (~' + tokens + ' tokens)';
    };

    // ── Stats (จาก server) ──────────────────────────────────────

    AiChat.prototype.renderStats = function () {
        var self = this;
        var body = this.el.statsBody;
        body.textContent = 'กำลังโหลด…';

        this.api('GET', '/stats').then(function (data) {
            body.innerHTML = '';

            if (!data.all || !data.all.calls) {
                body.textContent = 'ยังไม่มีข้อมูลการใช้งาน';
                return;
            }

            function chargeOf(rows) {
                var total = 0;
                var known = false;
                rows.forEach(function (row) {
                    var c = calculateChargeThb(row.model, row.prompt, row.completion);
                    if (c !== null) {
                        total += c;
                        known = true;
                    }
                });
                return known ? total : null;
            }

            var byModel = data.byModel || [];

            var grid = document.createElement('div');
            grid.className = 'ai-chat-stats-grid';
            [
                { label: 'วันนี้', data: data.today },
                { label: 'ทั้งหมด', data: data.all },
            ].forEach(function (card, cardIdx) {
                var el = document.createElement('div');
                el.className = 'ai-chat-stats-card';
                var label = document.createElement('div');
                label.className = 'ai-chat-stats-card-label';
                label.textContent = card.label + ' (' + card.data.calls + ' ครั้ง)';
                var value = document.createElement('div');
                value.className = 'ai-chat-stats-card-value';
                value.textContent = '↓' + card.data.prompt + ' ↑' + card.data.completion;
                el.appendChild(label);
                el.appendChild(value);
                if (cardIdx === 1) {
                    var charge = chargeOf(byModel);
                    if (charge !== null) {
                        var chargeEl = document.createElement('div');
                        chargeEl.className = 'ai-chat-stats-card-label';
                        chargeEl.textContent = 'หักเครดิตรวม ~' + formatThb(charge);
                        el.appendChild(chargeEl);
                    }
                }
                grid.appendChild(el);
            });
            body.appendChild(grid);

            if (!byModel.length) return;

            var table = document.createElement('table');
            table.className = 'ai-chat-stats-table';
            var thead = document.createElement('thead');
            var headRow = document.createElement('tr');
            ['โมเดล', 'ครั้ง', '↓ in', '↑ out', 'ต้นทุน ($)', 'หักเครดิต (฿)'].forEach(function (h, i) {
                var th = document.createElement('th');
                th.textContent = h;
                if (i > 0) th.className = 'num';
                headRow.appendChild(th);
            });
            thead.appendChild(headRow);
            table.appendChild(thead);

            var tbody = document.createElement('tbody');
            byModel.forEach(function (row) {
                var tr = document.createElement('tr');
                var cost = calculateCostUsd(row.model, row.prompt, row.completion);
                var thb = calculateChargeThb(row.model, row.prompt, row.completion);
                [
                    row.model,
                    row.calls,
                    row.prompt,
                    row.completion,
                    cost !== null ? formatCost(cost) : '-',
                    thb !== null ? formatThb(thb) : '-',
                ].forEach(function (v, i) {
                    var td = document.createElement('td');
                    td.textContent = v;
                    if (i > 0) td.className = 'num';
                    tr.appendChild(td);
                });
                tbody.appendChild(tr);
            });
            table.appendChild(tbody);
            body.appendChild(table);
        }).catch(function (error) {
            body.textContent = error.message;
        });
    };

    // ── Settings ────────────────────────────────────────────────

    AiChat.prototype.fillSettingsForm = function () {
        this.el.settingHistoryMode.value = this.settings.historyMode;
        this.el.settingMaxHistory.value = this.settings.maxHistoryMessages;
        this.el.settingMaxTokens.value = this.settings.maxResponseTokens || '';
        this.el.settingMaxHistoryField.style.display =
            this.settings.historyMode === 'recent' ? '' : 'none';
    };

    AiChat.prototype.saveSettingsForm = function () {
        var maxHistory = parseInt(this.el.settingMaxHistory.value, 10);
        var maxTokens = parseInt(this.el.settingMaxTokens.value, 10);

        this.settings = {
            historyMode: this.el.settingHistoryMode.value === 'all' ? 'all' : 'recent',
            maxHistoryMessages: Math.min(100, Math.max(1, isNaN(maxHistory) ? 10 : maxHistory)),
            maxResponseTokens: isNaN(maxTokens) ? null : Math.min(32768, Math.max(16, maxTokens)),
        };
        saveJson(STORAGE_SETTINGS, this.settings);
        this.el.settingsModal.classList.remove('open');
        this.updateContextPreview();
    };

    // ── Toast ───────────────────────────────────────────────────

    AiChat.prototype.showToast = function (message, durationMs) {
        var self = this;
        this.el.toast.textContent = message;
        this.el.toast.classList.add('open');
        clearTimeout(this.toastTimer);
        this.toastTimer = setTimeout(function () {
            self.el.toast.classList.remove('open');
        }, durationMs || 5000);
    };

    // ── Boot ────────────────────────────────────────────────────

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { new AiChat(); });
    } else {
        new AiChat();
    }
})();

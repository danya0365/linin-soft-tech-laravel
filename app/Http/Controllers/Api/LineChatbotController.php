<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatService;
use App\Services\LineMessagingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * LINE Chatbot Controller
 * 
 * รับ Webhook events จาก LINE และตอบกลับอัตโนมัติ
 * ใช้ ChatService สำหรับ business logic (Single Source of Truth)
 */
class LineChatbotController extends Controller
{
    protected LineMessagingService $lineService;
    protected ChatService $chatService;

    public function __construct(LineMessagingService $lineService, ChatService $chatService)
    {
        $this->lineService = $lineService;
        $this->chatService = $chatService;
    }

    /**
     * รับ Webhook events จาก LINE
     */
    public function webhook(Request $request)
    {
        $body = $request->getContent();
        $signature = $request->header('X-Line-Signature');

        Log::debug('LINE Webhook received', [
            'body' => $body,
            'signature' => $signature
        ]);

        // ตรวจสอบ Signature
        if (!$signature || !$this->lineService->verifySignature($body, $signature)) {
            Log::warning('LINE Webhook: Invalid signature');
            return response()->json(['error' => 'Invalid signature'], 401);
        }

        $data = json_decode($body, true);
        $events = $data['events'] ?? [];

        // LINE Verification: Respond 200 even if no events
        if (empty($events)) {
            Log::info('LINE Webhook: No events found or empty (Connection test)');
            return response()->json(['status' => 'ok']);
        }

        foreach ($events as $event) {
            try {
                $this->handleEvent($event);
            } catch (\Exception $e) {
                Log::error('LINE Webhook: Error handling event', [
                    'error' => $e->getMessage(),
                    'event' => $event,
                    'trace' => $e->getTraceAsString()
                ]);

                // ตอบกลับ error ไปยัง User (ถ้ามี replyToken)
                if (isset($event['replyToken'])) {
                    try {
                        $this->lineService->replyMessage($event['replyToken'], [
                            $this->lineService->textMessage("⚠️ เกิดข้อผิดพลาดชั่วคราวในการประมวลผล\nกรุณาลองใหม่อีกครั้ง หรือพิมพ์ \"เมนู\"")
                        ]);
                    } catch (\Exception $inner) {
                        Log::error('LINE Webhook: Failed to send error reply', ['error' => $inner->getMessage()]);
                    }
                }
            }
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * จัดการ Event
     */
    protected function handleEvent(array $event): void
    {
        $replyToken = $event['replyToken'] ?? null;
        $userId = $event['source']['userId'] ?? null;

        if (!$replyToken || !$userId) {
            return;
        }

        // ตรวจสอบว่า User ผูกบัญชีหรือยัง
        $user = \App\Models\User::where('line_user_id', $userId)->first();

        // กรณีรับข้อความ (Message Event)
        if ($event['type'] === 'message' && $event['message']['type'] === 'text') {
            $text = trim($event['message']['text']);

            // ถ้ายังไม่ได้ผูกบัญชี
            if (!$user) {
                // ตรวจสอบคำสั่งลงทะเบียน: "ลงทะเบียน [email]"
                if (str_starts_with($text, 'ลงทะเบียน')) {
                    $this->handleRegistration($replyToken, $userId, $text);
                    return;
                }

                // แจ้งให้ลงทะเบียน
                $this->sendResponse($replyToken, [
                    'type' => 'text',
                    'text' => "⛔ คุณยังไม่ได้ผูกบัญชีกับระบบ\n\nกรุณาพิมพ์คำสั่งเพื่อยืนยันตัวตน:\n\nลงทะเบียน [อีเมลของคุณ]\n\nตัวอย่าง:\nลงทะเบียน employee@example.com"
                ]);
                return;
            }
        } elseif (!$user) {
            // Event อื่นๆ (Postback, etc.) ถ้ายังไม่ผูกบัญชี ให้แจ้งเตือนและจบการทำงาน
            $this->sendResponse($replyToken, [
                'type' => 'text',
                'text' => "⛔ กรุณาพิมพ์ \"ลงทะเบียน [อีเมล]\" เพื่อยืนยันตัวตนก่อนใช้งาน"
            ]);
            return;
        }

        // อนุญาตให้ใช้งานได้ตามปกติ
        switch ($event['type']) {
            case 'message':
                $this->handleMessage($event);
                break;
            case 'postback':
                $this->handlePostback($event);
                break;
            case 'follow':
                $this->handleFollow($event);
                break;
        }
    }

    /**
     * จัดการการลงทะเบียนผูกบัญชี
     */
    protected function handleRegistration(string $replyToken, string $userId, string $text): void
    {
        // Clean format: Normalize whitespace (incl. newlines) to single space
        // แก้ปัญหา User พิมพ์เว้นบรรทัด หรือมีช่องว่างเกิน
        $text = preg_replace('/\s+/u', ' ', trim($text));
        
        $parts = explode(' ', $text);
        $email = $parts[1] ?? '';

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->sendResponse($replyToken, [
                'type' => 'text',
                'text' => "❌ รูปแบบอีเมลไม่ถูกต้อง\n\nตัวอย่าง:\nลงทะเบียน employee@example.com"
            ]);
            return;
        }

        // ค้นหา User จาก Email
        $user = \App\Models\User::where('email', $email)->first();

        if (!$user) {
            $this->sendResponse($replyToken, [
                'type' => 'text',
                'text' => "❌ ไม่พบอีเมลนี้ในระบบ\nกรุณาติดต่อผู้ดูแลระบบ"
            ]);
            return;
        }

        if ($user->line_user_id) {
            $this->sendResponse($replyToken, [
                'type' => 'text',
                'text' => "❌ อีเมลนี้ถูกผูกกับ LINE Account อื่นไปแล้ว"
            ]);
            return;
        }

        // บันทึก line_user_id
        $user->line_user_id = $userId;
        $user->line_registered_at = now();
        $user->save();

        $this->sendResponse($replyToken, [
            'type' => 'text',
            'text' => "✅ ลงทะเบียนสำเร็จ!\n\nสวัสดีคุณ {$user->name}\nตอนนี้คุณสามารถใช้งาน Chatbot ได้แล้วครับ\n\n(พิมพ์ \"เมนู\" เพื่อเริ่มใช้งาน)"
        ]);
        
        // ส่งเมนูหลักให้เลย
        $response = $this->chatService->getMainMenu();
        $this->sendResponse($replyToken, $response);
    }

    /**
     * จัดการ Message Event
     */
    protected function handleMessage(array $event): void
    {
        $replyToken = $event['replyToken'];
        $message = $event['message'];

        if ($message['type'] !== 'text') {
            $response = $this->chatService->getMainMenu('กรุณาเลือกเมนูด้านล่าง หรือพิมพ์ "เมนู"');
            $this->sendResponse($replyToken, $response);
            return;
        }

        $text = trim($message['text']);

        // ใช้ ChatService สำหรับ business logic
        $response = $this->chatService->processCommand($text);
        $this->sendResponse($replyToken, $response);
    }

    /**
     * จัดการ Postback Event
     */
    protected function handlePostback(array $event): void
    {
        $replyToken = $event['replyToken'];
        $data = $event['postback']['data'] ?? '';

        // Parse postback data (format: action=value)
        parse_str($data, $params);
        $action = $params['action'] ?? '';
        unset($params['action']);

        // ใช้ ChatService สำหรับ business logic
        $response = $this->chatService->processAction($action, $params);
        $this->sendResponse($replyToken, $response);
    }

    /**
     * จัดการ Follow Event (เมื่อ user เพิ่ม bot เป็นเพื่อน)
     */
    protected function handleFollow(array $event): void
    {
        $replyToken = $event['replyToken'];
        $response = $this->chatService->getMainMenu("🎉 ยินดีต้อนรับสู่ LinenSoftTech!\n\nผมพร้อมช่วยเหลือคุณเรื่องข้อมูลโรงซักรีด กรุณาเลือกเมนูด้านล่าง:");
        $this->sendResponse($replyToken, $response);
    }

    /**
     * ส่ง response กลับไปยัง LINE
     * แปลง ChatService response เป็น LINE message format
     */
    protected function sendResponse(string $replyToken, array $response): void
    {
        $messages = $this->convertToLineMessages($response);
        $this->lineService->replyMessage($replyToken, $messages);
    }

    /**
     * แปลง ChatService response เป็น LINE message format
     */
    protected function convertToLineMessages(array $response): array
    {
        $type = $response['type'] ?? 'text';

        switch ($type) {
            case 'text':
                $text = $response['text'] ?? 'ไม่มีข้อมูล';
                return [$this->lineService->textMessage($text)];

            case 'menu':
                $text = $response['text'] ?? 'กรุณาเลือกเมนู';
                $quickReplyItems = $this->buildQuickReplyItems($response['quickReplies'] ?? []);
                
                // ถ้าไม่มี Quick Reply ให้ส่งเป็น Text ธรรมดาแทน (LINE Error if quickReply.items is empty)
                if (empty($quickReplyItems)) {
                    return [$this->lineService->textMessage($text)];
                }
                
                return [$this->lineService->quickReply($text, $quickReplyItems)];

            case 'card':
                return [$this->buildFlexMessage($response)];

            default:
                $fallbackText = is_string($response) ? $response : json_encode($response);
                return [$this->lineService->textMessage($fallbackText)];
        }
    }

    /**
     * สร้าง Quick Reply Items จาก ChatService format
     */
    protected function buildQuickReplyItems(array $replies): array
    {
        $items = [];
        foreach ($replies as $reply) {
            if (is_string($reply)) {
                // Simple text reply
                $label = $reply;
                if (mb_strlen($label) > 20) {
                    $label = mb_substr($label, 0, 17) . '...';
                }
                $items[] = $this->lineService->quickReplyItem($label, 'message', $reply);
            } else {
                // Action reply
                $label = $reply['label'] ?? '';
                $action = $reply['action'] ?? '';
                $data = $reply['data'] ?? [];

                // LINE limit: Label must be max 20 characters
                if (mb_strlen($label) > 20) {
                    $label = mb_substr($label, 0, 17) . '...';
                }

                if ($action) {
                    // Build postback data string
                    $postbackData = 'action=' . $action;
                    foreach ($data as $key => $value) {
                        $postbackData .= '&' . $key . '=' . $value;
                    }
                    $items[] = $this->lineService->quickReplyItem($label, 'postback', $postbackData);
                } else {
                    $items[] = $this->lineService->quickReplyItem($label, 'message');
                }
            }
        }
        return $items;
    }

    /**
     * สร้าง Flex Message จาก ChatService card format
     */
    protected function buildFlexMessage(array $card): array
    {
        $bodyContents = [];

        foreach ($card['rows'] ?? [] as $row) {
            if (isset($row['type']) && $row['type'] === 'separator') {
                $bodyContents[] = $this->lineService->separator();
            } elseif (isset($row['bold']) && $row['bold']) {
                if (empty($row['value'])) {
                    // Section Header (Bold Label only)
                    $bodyContents[] = [
                        'type' => 'text',
                        'text' => $row['label'],
                        'size' => 'sm',
                        'weight' => 'bold',
                        'margin' => 'md',
                    ];
                } else {
                    // Highlighted Row (Bold Label + Value)
                    $bodyContents[] = [
                        'type' => 'box',
                        'layout' => 'horizontal',
                        'contents' => [
                            [
                                'type' => 'text',
                                'text' => $row['label'],
                                'size' => 'sm',
                                'weight' => 'bold',
                                'color' => '#111111',
                                'flex' => 0,
                            ],
                            [
                                'type' => 'text',
                                'text' => $row['value'],
                                'size' => 'sm',
                                'color' => $row['valueColor'] ?? '#111111',
                                'align' => 'end',
                                'weight' => 'bold',
                            ],
                        ],
                        'margin' => 'md',
                    ];
                }
            } else {
                $bodyContents[] = $this->lineService->infoRow(
                    $row['label'] ?? '',
                    $row['value'] ?? '',
                    $row['valueColor'] ?? '#111111'
                );
            }
        }

        if (empty($bodyContents)) {
            $bodyContents[] = $this->lineService->infoRow('ข้อมูล', 'ไม่พบข้อมูลที่จะแสดงในขณะนี้');
        }

        $bubble = $this->lineService->bubbleContainer(
            $card['title'] ?? 'ข้อมูล',
            $card['subtitle'] ?? '',
            $bodyContents,
            $card['headerColor'] ?? '#1DB446'
        );

        return $this->lineService->flexMessage($card['title'] ?? 'ข้อมูล', $bubble);
    }
}

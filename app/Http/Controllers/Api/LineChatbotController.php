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

        if (!$replyToken) {
            return;
        }

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
                $items[] = $this->lineService->quickReplyItem($reply, 'message');
            } else {
                // Action reply
                $label = $reply['label'] ?? '';
                $action = $reply['action'] ?? '';
                $data = $reply['data'] ?? [];

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
                $bodyContents[] = [
                    'type' => 'text',
                    'text' => $row['label'],
                    'size' => 'sm',
                    'weight' => 'bold',
                    'margin' => 'md',
                ];
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

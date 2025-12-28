<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * LINE Messaging API Service
 * 
 * จัดการการส่งข้อความผ่าน LINE Messaging API
 */
class LineMessagingService
{
    protected string $channelAccessToken;
    protected string $channelSecret;
    protected string $apiBaseUrl = 'https://api.line.me/v2/bot';

    public function __construct()
    {
        $this->channelAccessToken = config('services.line.channel_access_token');
        $this->channelSecret = config('services.line.channel_secret');
    }

    /**
     * ตรวจสอบ Signature ของ request จาก LINE
     */
    public function verifySignature(string $body, string $signature): bool
    {
        $hash = base64_encode(hash_hmac('sha256', $body, $this->channelSecret, true));
        return hash_equals($hash, $signature);
    }

    /**
     * ส่งข้อความตอบกลับ
     */
    public function replyMessage(string $replyToken, array $messages): bool
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->channelAccessToken,
                'Content-Type' => 'application/json',
            ])->post($this->apiBaseUrl . '/message/reply', [
                'replyToken' => $replyToken,
                'messages' => $messages,
            ]);

            if ($response->failed()) {
                Log::error('LINE Reply Message Failed', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);
                return false;
            }

            return true;
        } catch (\Exception $e) {
            Log::error('LINE Reply Message Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * สร้าง Text Message
     */
    public function textMessage(string $text): array
    {
        return [
            'type' => 'text',
            'text' => $text,
        ];
    }

    /**
     * สร้าง Quick Reply Options
     */
    public function quickReply(string $text, array $items): array
    {
        return [
            'type' => 'text',
            'text' => $text,
            'quickReply' => [
                'items' => $items,
            ],
        ];
    }

    /**
     * สร้าง Quick Reply Item
     */
    public function quickReplyItem(string $label, string $action, string $data = null): array
    {
        if ($action === 'message') {
            return [
                'type' => 'action',
                'action' => [
                    'type' => 'message',
                    'label' => $label,
                    'text' => $data ?? $label,
                ],
            ];
        }

        return [
            'type' => 'action',
            'action' => [
                'type' => 'postback',
                'label' => $label,
                'data' => $data ?? $label,
            ],
        ];
    }

    /**
     * สร้าง Flex Message สำหรับแสดงข้อมูลสวยงาม
     */
    public function flexMessage(string $altText, array $contents): array
    {
        return [
            'type' => 'flex',
            'altText' => $altText,
            'contents' => $contents,
        ];
    }

    /**
     * สร้าง Bubble Container สำหรับ Flex Message
     */
    public function bubbleContainer(
        string $title,
        string $subtitle = '',
        array $bodyContents = [],
        string $headerColor = '#1DB446'
    ): array {
        $bubble = [
            'type' => 'bubble',
            'size' => 'kilo',
            'header' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => [
                    [
                        'type' => 'text',
                        'text' => $title,
                        'color' => '#ffffff',
                        'size' => 'lg',
                        'weight' => 'bold',
                    ],
                ],
                'backgroundColor' => $headerColor,
                'paddingAll' => '15px',
            ],
            'body' => [
                'type' => 'box',
                'layout' => 'vertical',
                'contents' => $bodyContents,
                'paddingAll' => '15px',
            ],
        ];

        if ($subtitle) {
            $bubble['header']['contents'][] = [
                'type' => 'text',
                'text' => $subtitle,
                'color' => '#ffffff99',
                'size' => 'sm',
            ];
        }

        return $bubble;
    }

    /**
     * สร้าง Row สำหรับ Body ของ Flex Message
     */
    public function infoRow(string $label, string $value, string $valueColor = '#111111'): array
    {
        return [
            'type' => 'box',
            'layout' => 'horizontal',
            'contents' => [
                [
                    'type' => 'text',
                    'text' => $label,
                    'size' => 'sm',
                    'color' => '#555555',
                    'flex' => 0,
                ],
                [
                    'type' => 'text',
                    'text' => $value,
                    'size' => 'sm',
                    'color' => $valueColor,
                    'align' => 'end',
                    'weight' => 'bold',
                ],
            ],
            'margin' => 'md',
        ];
    }

    /**
     * สร้าง Separator
     */
    public function separator(): array
    {
        return [
            'type' => 'separator',
            'margin' => 'lg',
        ];
    }
}

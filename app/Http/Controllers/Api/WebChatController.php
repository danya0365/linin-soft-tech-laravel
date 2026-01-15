<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ChatService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Web Chat Controller
 * 
 * API endpoint สำหรับ mini chat บนเว็บ
 * ใช้ ChatService เพื่อ single source of truth กับ LINE chatbot
 */
class WebChatController extends Controller
{
    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    /**
     * รับ text message และ return response
     */
    public function message(Request $request)
    {
        $request->validate([
            'text' => 'required|string|max:500',
        ]);

        try {
            $text = $request->input('text');
            $response = $this->chatService->processCommand($text);

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('WebChat Message Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    /**
     * รับ action (postback) และ return response
     */
    public function action(Request $request)
    {
        $request->validate([
            'action' => 'required|string|max:100',
            'params' => 'nullable|array',
        ]);

        try {
            $action = $request->input('action');
            $params = $request->input('params', []);
            $response = $this->chatService->processAction($action, $params);

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('WebChat Action Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }

    /**
     * ดึง welcome message พร้อม main menu
     */
    public function welcome()
    {
        try {
            $response = $this->chatService->getMainMenu('🎉 ยินดีต้อนรับสู่ LinenSoftTech!\n\nผมพร้อมช่วยเหลือคุณเรื่องข้อมูลโรงซักรีด กรุณาเลือกเมนูด้านล่าง:');

            return response()->json([
                'success' => true,
                'data' => $response,
            ]);
        } catch (\Exception $e) {
            Log::error('WebChat Welcome Error', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'error' => 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง',
            ], 500);
        }
    }
}

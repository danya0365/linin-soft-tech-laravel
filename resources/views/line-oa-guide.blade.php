<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="คู่มือการใช้งาน LINE OA สำหรับระบบ LinenSoftTech - ระบบจัดการโรงซักรีดอุตสาหกรรม">
    
    <title>{{ __('คู่มือ LINE OA - LinenSoftTech') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Kanit', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #06C755 0%, #00B900 100%);
        }
        
        .card-hover {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        }
        
        .step-number {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #06C755 0%, #00B900 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.25rem;
            flex-shrink: 0;
        }
        
        .menu-icon {
            width: 60px;
            height: 60px;
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.75rem;
            margin-bottom: 0.75rem;
        }
        
        .flow-arrow {
            color: #06C755;
        }
        
        .chat-bubble {
            background: #E5E5EA;
            border-radius: 20px 20px 20px 4px;
            padding: 12px 16px;
            max-width: 80%;
        }
        
        .chat-bubble-bot {
            background: #06C755;
            color: white;
            border-radius: 20px 20px 4px 20px;
        }
        
        .line-green {
            color: #06C755;
        }
        
        .bg-line-green {
            background-color: #06C755;
        }
    </style>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="max-w-6xl mx-auto px-4 py-8 sm:py-12">
            <div class="text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-2xl shadow-lg mb-4">
                    <svg class="w-12 h-12 text-green-500" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                    </svg>
                </div>
                <h1 class="text-3xl sm:text-4xl font-bold mb-2">คู่มือการใช้งาน LINE OA</h1>
                <p class="text-lg sm:text-xl opacity-90">ระบบ LinenSoftTech - จัดการโรงซักรีดอุตสาหกรรม</p>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-6xl mx-auto px-4 py-8">
        
        <!-- Getting Started Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    🚀
                </span>
                เริ่มต้นใช้งาน
            </h2>
            
            <div class="bg-white rounded-2xl shadow-md p-6 sm:p-8">
                <div class="grid md:grid-cols-3 gap-6">
                    <!-- Step 1 -->
                    <div class="flex flex-col items-center text-center">
                        <div class="step-number mb-4">1</div>
                        <h3 class="font-semibold text-gray-800 mb-2">เพิ่มเพื่อน LINE OA</h3>
                        <p class="text-gray-600 text-sm">สแกน QR Code หรือค้นหา @linensofttech</p>
                        <div class="mt-4 w-32 h-32 bg-gray-100 rounded-xl flex items-center justify-center">
                            <span class="text-gray-400 text-sm">QR Code</span>
                        </div>
                    </div>
                    
                    <!-- Step 2 -->
                    <div class="flex flex-col items-center text-center">
                        <div class="step-number mb-4">2</div>
                        <h3 class="font-semibold text-gray-800 mb-2">พิมพ์ "เมนู"</h3>
                        <p class="text-gray-600 text-sm">พิมพ์คำว่า "เมนู" หรือ "menu" เพื่อเริ่มต้น</p>
                        <div class="mt-4 bg-gray-100 rounded-xl p-4 w-full">
                            <div class="chat-bubble inline-block">เมนู</div>
                        </div>
                    </div>
                    
                    <!-- Step 3 -->
                    <div class="flex flex-col items-center text-center">
                        <div class="step-number mb-4">3</div>
                        <h3 class="font-semibold text-gray-800 mb-2">เลือกเมนูที่ต้องการ</h3>
                        <p class="text-gray-600 text-sm">กดปุ่มเมนูด้านล่างเพื่อดูข้อมูล</p>
                        <div class="mt-4 flex flex-wrap justify-center gap-2">
                            <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full text-sm">📊 สรุปวันนี้</span>
                            <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full text-sm">👥 ลูกค้า</span>
                            <span class="px-3 py-1 bg-orange-100 text-orange-700 rounded-full text-sm">📦 สต๊อก</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Menu Overview Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    📋
                </span>
                เมนูทั้งหมด
            </h2>
            
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <!-- Menu 1: Summary -->
                <div class="bg-white rounded-2xl shadow-md p-6 card-hover">
                    <div class="menu-icon bg-green-100">📊</div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">สรุปวันนี้</h3>
                    <p class="text-gray-600 text-sm mb-3">ดูภาพรวมการทำงานประจำวัน</p>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">จำนวนลูกค้า</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">งานวันนี้</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">พลังงาน</span>
                    </div>
                </div>
                
                <!-- Menu 2: Customer -->
                <div class="bg-white rounded-2xl shadow-md p-6 card-hover">
                    <div class="menu-icon bg-blue-100">👥</div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">ลูกค้า</h3>
                    <p class="text-gray-600 text-sm mb-3">ค้นหาและดูข้อมูลลูกค้า</p>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">น้ำหนักเปียก</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">น้ำหนักแห้ง</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">ยอดเงิน</span>
                    </div>
                </div>
                
                <!-- Menu 3: Stock -->
                <div class="bg-white rounded-2xl shadow-md p-6 card-hover">
                    <div class="menu-icon bg-orange-100">📦</div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">สต๊อก</h3>
                    <p class="text-gray-600 text-sm mb-3">ตรวจสอบยอดคงเหลือวัตถุดิบ</p>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">เคมี</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">ถุงพลาสติก</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">วัสดุทั่วไป</span>
                    </div>
                </div>
                
                <!-- Menu 4: Energy -->
                <div class="bg-white rounded-2xl shadow-md p-6 card-hover">
                    <div class="menu-icon bg-yellow-100">⚡</div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">พลังงาน</h3>
                    <p class="text-gray-600 text-sm mb-3">ติดตามการใช้พลังงาน</p>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">น้ำ</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">ไฟฟ้า</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">แก็ส</span>
                    </div>
                </div>
                
                <!-- Menu 5: Employee -->
                <div class="bg-white rounded-2xl shadow-md p-6 card-hover">
                    <div class="menu-icon bg-purple-100">👷</div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">พนักงาน</h3>
                    <p class="text-gray-600 text-sm mb-3">ดูรายชื่อและสถิติการทำงาน</p>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">รายชื่อ</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">สถิติวันนี้</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">สถิติเดือน</span>
                    </div>
                </div>
                
                <!-- Menu 6: Machine -->
                <div class="bg-white rounded-2xl shadow-md p-6 card-hover">
                    <div class="menu-icon bg-gray-200">⚙️</div>
                    <h3 class="font-semibold text-gray-800 text-lg mb-2">เครื่องจักร</h3>
                    <p class="text-gray-600 text-sm mb-3">ดูรายการเครื่องจักรทั้งหมด</p>
                    <div class="flex flex-wrap gap-1">
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">เครื่องซัก</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">เครื่องอบ</span>
                        <span class="px-2 py-0.5 bg-gray-100 text-gray-600 rounded text-xs">ความจุ</span>
                    </div>
                </div>
            </div>
        </section>

        <!-- Use Case Details -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    📖
                </span>
                ขั้นตอนการใช้งานแต่ละเมนู
            </h2>

            <!-- Accordion -->
            <div class="space-y-4">
                
                <!-- Use Case 1: Summary -->
                <details class="bg-white rounded-2xl shadow-md overflow-hidden group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <span class="text-2xl mr-4">📊</span>
                            <div>
                                <h3 class="font-semibold text-gray-800">สรุปวันนี้</h3>
                                <p class="text-gray-500 text-sm">1 ขั้นตอน - แสดงผลทันที</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 border-t border-gray-100 pt-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-1">
                                <p class="text-gray-600 mb-4">พิมพ์ <strong>"📊 สรุปวันนี้"</strong> หรือ <strong>"summary"</strong></p>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-sm text-gray-500 mb-2">Bot จะตอบกลับด้วย:</p>
                                    <ul class="text-sm text-gray-700 space-y-1">
                                        <li>📅 วันที่ปัจจุบัน</li>
                                        <li>👥 จำนวนลูกค้าทั้งหมด</li>
                                        <li>🧺 จำนวนงานวันนี้</li>
                                        <li>⚡ สรุปการใช้พลังงาน</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Use Case 2: Customer -->
                <details class="bg-white rounded-2xl shadow-md overflow-hidden group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <span class="text-2xl mr-4">👥</span>
                            <div>
                                <h3 class="font-semibold text-gray-800">ลูกค้า</h3>
                                <p class="text-gray-500 text-sm">3 ขั้นตอน - เลือกกลุ่ม → เลือกลูกค้า → ดูข้อมูล</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 border-t border-gray-100 pt-4">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">1</span>
                                <div>
                                    <p class="font-medium text-gray-800">พิมพ์ "👥 ลูกค้า"</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงปุ่มเลือกกลุ่มลูกค้า</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">2</span>
                                <div>
                                    <p class="font-medium text-gray-800">กดเลือกกลุ่ม (เช่น โรงพยาบาล)</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงรายชื่อลูกค้าในกลุ่ม</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">3</span>
                                <div>
                                    <p class="font-medium text-gray-800">กดเลือกลูกค้า</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงข้อมูลลูกค้า: ชื่อ, น้ำหนักเปียก/แห้ง, ยอดเงินรวม</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Use Case 3: Stock -->
                <details class="bg-white rounded-2xl shadow-md overflow-hidden group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <span class="text-2xl mr-4">📦</span>
                            <div>
                                <h3 class="font-semibold text-gray-800">สต๊อก</h3>
                                <p class="text-gray-500 text-sm">2 ขั้นตอน - เลือกประเภท → ดูยอดคงเหลือ</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 border-t border-gray-100 pt-4">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">1</span>
                                <div>
                                    <p class="font-medium text-gray-800">พิมพ์ "📦 สต๊อก"</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงปุ่มเลือกประเภทวัตถุดิบ</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-orange-100 text-orange-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">2</span>
                                <div>
                                    <p class="font-medium text-gray-800">กดเลือกประเภท (เช่น เคมี/ผงซักฟอก)</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงรายการวัตถุดิบและยอดคงเหลือ</p>
                                    <div class="mt-2 p-3 bg-orange-50 rounded-lg text-sm">
                                        <span class="text-red-500 font-medium">⚠️ หมายเหตุ:</span> ยอดที่เหลือน้อยกว่า 20% จะแสดงเป็น<span class="text-red-500 font-medium">สีแดง</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Use Case 4: Energy -->
                <details class="bg-white rounded-2xl shadow-md overflow-hidden group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <span class="text-2xl mr-4">⚡</span>
                            <div>
                                <h3 class="font-semibold text-gray-800">พลังงาน</h3>
                                <p class="text-gray-500 text-sm">2 ขั้นตอน - เลือกประเภท → ดูประวัติ 7 วัน</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 border-t border-gray-100 pt-4">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">1</span>
                                <div>
                                    <p class="font-medium text-gray-800">พิมพ์ "⚡ พลังงาน"</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงปุ่มเลือกประเภทพลังงาน (น้ำ, ไฟ, แก็ส, ชีวมวล, น้ำมันเตา)</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-yellow-100 text-yellow-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">2</span>
                                <div>
                                    <p class="font-medium text-gray-800">กดเลือกประเภทพลังงาน</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงสรุปรวม 7 วัน และประวัติการใช้รายวัน</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Use Case 5: Employee -->
                <details class="bg-white rounded-2xl shadow-md overflow-hidden group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <span class="text-2xl mr-4">👷</span>
                            <div>
                                <h3 class="font-semibold text-gray-800">พนักงาน</h3>
                                <p class="text-gray-500 text-sm">3 ขั้นตอน - เลือกแผนก → เลือกพนักงาน → ดูสถิติ</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 border-t border-gray-100 pt-4">
                        <div class="space-y-4">
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">1</span>
                                <div>
                                    <p class="font-medium text-gray-800">พิมพ์ "👷 พนักงาน"</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงปุ่มเลือกแผนก (ซัก, อบ, รีด, พับแพ็ค, จัดเก็บ)</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">2</span>
                                <div>
                                    <p class="font-medium text-gray-800">กดเลือกแผนก</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงรายชื่อพนักงานในแผนก</p>
                                </div>
                            </div>
                            <div class="flex items-start">
                                <span class="flex-shrink-0 w-8 h-8 bg-purple-100 text-purple-600 rounded-full flex items-center justify-center text-sm font-medium mr-3">3</span>
                                <div>
                                    <p class="font-medium text-gray-800">กดเลือกพนักงาน</p>
                                    <p class="text-gray-500 text-sm">Bot แสดงสถิติการทำงาน:</p>
                                    <ul class="mt-2 text-sm text-gray-600 space-y-1">
                                        <li>📅 จำนวน Operation วันนี้</li>
                                        <li>📆 จำนวน Operation สัปดาห์นี้</li>
                                        <li>📅 จำนวน Operation เดือนนี้</li>
                                        <li>🔧 แยกตามประเภทงาน (ซัก/อบ/รีด/พับแพ็ค/จัดเก็บ)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>

                <!-- Use Case 6: Machine -->
                <details class="bg-white rounded-2xl shadow-md overflow-hidden group">
                    <summary class="flex items-center justify-between p-6 cursor-pointer hover:bg-gray-50">
                        <div class="flex items-center">
                            <span class="text-2xl mr-4">⚙️</span>
                            <div>
                                <h3 class="font-semibold text-gray-800">เครื่องจักร</h3>
                                <p class="text-gray-500 text-sm">1 ขั้นตอน - แสดงผลทันที</p>
                            </div>
                        </div>
                        <svg class="w-5 h-5 text-gray-400 transform group-open:rotate-180 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                        </svg>
                    </summary>
                    <div class="px-6 pb-6 border-t border-gray-100 pt-4">
                        <div class="flex items-start space-x-4">
                            <div class="flex-1">
                                <p class="text-gray-600 mb-4">พิมพ์ <strong>"⚙️ เครื่องจักร"</strong> หรือ <strong>"machine"</strong></p>
                                <div class="bg-gray-50 rounded-xl p-4">
                                    <p class="text-sm text-gray-500 mb-2">Bot จะตอบกลับด้วย:</p>
                                    <ul class="text-sm text-gray-700 space-y-1">
                                        <li>🧺 รายการเครื่องซัก + ความจุ (กก.)</li>
                                        <li>🌡️ รายการเครื่องอบ + ความจุ (กก.)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
            </div>
        </section>

        <!-- Keywords Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    ⌨️
                </span>
                คำสั่งที่พิมพ์ได้
            </h2>
            
            <div class="bg-white rounded-2xl shadow-md p-6">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead>
                            <tr class="border-b border-gray-200">
                                <th class="text-left py-3 px-4 text-gray-600 font-medium">เมนู</th>
                                <th class="text-left py-3 px-4 text-gray-600 font-medium">คำสั่ง (พิมพ์ได้)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr>
                                <td class="py-3 px-4">📋 เมนูหลัก</td>
                                <td class="py-3 px-4">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm">เมนู</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">menu</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">help</code>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4">📊 สรุปวันนี้</td>
                                <td class="py-3 px-4">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm">📊 สรุปวันนี้</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">summary</code>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4">👥 ลูกค้า</td>
                                <td class="py-3 px-4">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm">👥 ลูกค้า</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">customer</code>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4">📦 สต๊อก</td>
                                <td class="py-3 px-4">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm">📦 สต๊อก</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">stock</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">inventory</code>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4">⚡ พลังงาน</td>
                                <td class="py-3 px-4">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm">⚡ พลังงาน</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">energy</code>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4">👷 พนักงาน</td>
                                <td class="py-3 px-4">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm">👷 พนักงาน</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">employee</code>
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-4">⚙️ เครื่องจักร</td>
                                <td class="py-3 px-4">
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm">⚙️ เครื่องจักร</code>
                                    <code class="px-2 py-1 bg-gray-100 rounded text-sm ml-1">machine</code>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        <!-- Tips Section -->
        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6 flex items-center">
                <span class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    💡
                </span>
                เคล็ดลับการใช้งาน
            </h2>
            
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
                    <h3 class="font-semibold text-green-800 mb-2">✅ ใช้บน Mobile</h3>
                    <p class="text-green-700 text-sm">Quick Reply (ปุ่มเลือก) จะแสดงเฉพาะบน LINE App ในมือถือเท่านั้น</p>
                </div>
                
                <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5">
                    <h3 class="font-semibold text-blue-800 mb-2">🔄 กลับเมนู</h3>
                    <p class="text-blue-700 text-sm">พิมพ์ "เมนู" ได้ทุกเมื่อเพื่อกลับไปเมนูหลัก</p>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-5">
                    <h3 class="font-semibold text-yellow-800 mb-2">⚡ ใช้ Emoji ได้</h3>
                    <p class="text-yellow-700 text-sm">สามารถพิมพ์พร้อม Emoji เช่น "📊 สรุปวันนี้" หรือไม่มีก็ได้</p>
                </div>
                
                <div class="bg-purple-50 border border-purple-200 rounded-2xl p-5">
                    <h3 class="font-semibold text-purple-800 mb-2">🌐 ภาษาอังกฤษ</h3>
                    <p class="text-purple-700 text-sm">รองรับคำสั่งภาษาอังกฤษ เช่น "customer", "stock", "energy"</p>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8">
        <div class="max-w-6xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center mb-4">
                <svg class="w-8 h-8 text-green-400 mr-2" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M19.365 9.863c.349 0 .63.285.63.631 0 .345-.281.63-.63.63H17.61v1.125h1.755c.349 0 .63.283.63.63 0 .344-.281.629-.63.629h-2.386c-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63h2.386c.346 0 .627.285.627.63 0 .349-.281.63-.63.63H17.61v1.125h1.755zm-3.855 3.016c0 .27-.174.51-.432.596-.064.021-.133.031-.199.031-.211 0-.391-.09-.51-.25l-2.443-3.317v2.94c0 .344-.279.629-.631.629-.346 0-.626-.285-.626-.629V8.108c0-.27.173-.51.43-.595.06-.023.136-.033.194-.033.195 0 .375.104.495.254l2.462 3.33V8.108c0-.345.282-.63.63-.63.345 0 .63.285.63.63v4.771zm-5.741 0c0 .344-.282.629-.631.629-.345 0-.627-.285-.627-.629V8.108c0-.345.282-.63.63-.63.346 0 .628.285.628.63v4.771zm-2.466.629H4.917c-.345 0-.63-.285-.63-.629V8.108c0-.345.285-.63.63-.63.348 0 .63.285.63.63v4.141h1.756c.348 0 .629.283.629.63 0 .344-.282.629-.629.629M24 10.314C24 4.943 18.615.572 12 .572S0 4.943 0 10.314c0 4.811 4.27 8.842 10.035 9.608.391.082.923.258 1.058.59.12.301.079.766.038 1.08l-.164 1.02c-.045.301-.24 1.186 1.049.645 1.291-.539 6.916-4.078 9.436-6.975C23.176 14.393 24 12.458 24 10.314"/>
                </svg>
                <span class="text-lg font-semibold">LinenSoftTech LINE OA</span>
            </div>
            <p class="text-gray-400 text-sm">ระบบจัดการโรงซักรีดอุตสาหกรรม</p>
            <p class="text-gray-500 text-xs mt-4">© {{ date('Y') }} LinenSoftTech. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>

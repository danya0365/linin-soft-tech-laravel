@extends('layouts.app')
@section('template_title')
    AI Credits
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-7xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'เครดิต AI']]" />

    {{-- Summary cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-6">
        <div class="rounded-xl p-4 bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow">
            <p class="text-xs opacity-80">เครดิตคงเหลือรวมทุก user</p>
            <p class="text-2xl font-bold">฿{{ number_format($summary['total_balance'], 2) }}</p>
        </div>
        <div class="rounded-xl p-4 bg-gradient-to-r from-slate-500 to-gray-600 text-white shadow">
            <p class="text-xs opacity-80">ต้นทุน AI รวม (ที่ใช้ไปแล้ว)</p>
            <p class="text-2xl font-bold">฿{{ number_format($summary['total_cost'], 2) }}</p>
        </div>
        <div class="rounded-xl p-4 bg-gradient-to-r from-emerald-500 to-teal-600 text-white shadow">
            <p class="text-xs opacity-80">รายได้ค่าคอมรวม</p>
            <p class="text-2xl font-bold">฿{{ number_format($summary['total_commission'], 2) }}</p>
        </div>
    </div>

    <x-ui.card>
        <x-slot:header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-gray-100">เครดิต AI Chat</h2>
                <form method="GET" action="{{ route('ai-credits.index') }}" class="flex gap-2">
                    <input type="text" name="q" value="{{ $search }}" placeholder="ค้นหาชื่อ / อีเมล"
                           class="rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-3 py-2 text-sm">
                    <x-ui.button type="submit" variant="primary" icon="fa fa-search">ค้นหา</x-ui.button>
                </form>
            </div>
        </x-slot:header>

        @if ($message = Session::get('success'))
            <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
        @endif

        <div class="overflow-x-auto rounded-lg shadow">
            <table class="w-full text-sm text-left border-collapse bg-white dark:bg-gray-800">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20 text-center">ID</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider">User</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-48 text-right">เครดิตคงเหลือ</th>
                        <th class="px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-40 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($users as $index => $user)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent dark:hover:from-gray-700/50 dark:hover:to-transparent transition-all duration-200">
                            <td class="px-6 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold">
                                    {{ $i + $index + 1 }}
                                </span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ $user->name }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <span class="text-sm font-bold {{ (float) $user->ai_credit_balance <= 0 ? 'text-red-600 dark:text-red-400' : 'text-gray-900 dark:text-gray-100' }}">
                                    ฿{{ number_format((float) $user->ai_credit_balance, 2) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <x-ui.button :href="route('ai-credits.show', $user)" variant="primary" icon="fa fa-coins">จัดการ</x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-8 text-center text-gray-500 dark:text-gray-400">
                                <div class="flex flex-col items-center gap-2">
                                    <i class="fa fa-inbox text-4xl text-gray-300 dark:text-gray-600"></i>
                                    <p>ไม่พบ user</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class="mt-6">{{ $users->links() }}</div>
</div>
@endsection

@extends('layouts.app')
@section('template_title')
    AI Credits — {{ $user->name }}
@endsection
@section('content')
<div class="container mx-auto px-4 py-6 max-w-5xl">
    <x-crud.breadcrumb :items="[['label' => 'Admin', 'route' => route('admin')],['label' => 'เครดิต AI', 'route' => route('ai-credits.index')],['label' => $user->name]]" />

    @if ($message = Session::get('success'))
        <x-ui.alert variant="success" dismissible="true">{{ $message }}</x-ui.alert>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
        {{-- Balance card --}}
        <div class="rounded-xl p-6 bg-gradient-to-r from-indigo-500 to-purple-600 text-white shadow">
            <p class="text-sm opacity-80">{{ $user->name }} ({{ $user->email }})</p>
            <p class="text-xs opacity-70 mt-1">เครดิตคงเหลือ</p>
            <p class="text-4xl font-bold mt-1 {{ (float) $user->ai_credit_balance <= 0 ? 'text-red-200' : '' }}">
                ฿{{ number_format((float) $user->ai_credit_balance, 2) }}
            </p>
        </div>

        {{-- Top-up / adjust form --}}
        <x-ui.card>
            <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">เติม / ปรับยอดเครดิต</h3>
            <form method="POST" action="{{ route('ai-credits.transactions.store', $user) }}" class="space-y-3">
                @csrf
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">จำนวน (บาท) — ใส่ค่าลบเพื่อปรับยอดลง</label>
                    <input type="number" name="amount" step="0.01" required value="{{ old('amount') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-3 py-2 text-sm"
                           placeholder="เช่น 100 หรือ -50">
                    @error('amount')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-xs text-gray-600 dark:text-gray-400 mb-1">หมายเหตุ (ไม่บังคับ)</label>
                    <input type="text" name="note" maxlength="255" value="{{ old('note') }}"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-gray-100 px-3 py-2 text-sm"
                           placeholder="เช่น เติมประจำเดือน มิ.ย.">
                </div>
                <div class="flex justify-end">
                    <x-ui.button type="submit" variant="primary" icon="fa fa-save">บันทึก</x-ui.button>
                </div>
            </form>
        </x-ui.card>
    </div>

    {{-- Transactions --}}
    <x-ui.card>
        <x-slot:header>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">ประวัติธุรกรรม</h3>
        </x-slot:header>
        <div class="overflow-x-auto rounded-lg shadow">
            <table class="w-full text-sm text-left border-collapse bg-white dark:bg-gray-800">
                <thead class="bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 border-b-2 border-gray-300 dark:border-gray-600">
                    <tr>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">วันที่</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase text-center">ประเภท</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase text-right">จำนวน</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase text-right">ต้นทุน</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase text-right">ค่าคอม</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase text-right">คงเหลือ</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">โดย</th>
                        <th class="px-4 py-3 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase">หมายเหตุ</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($transactions as $tx)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300 whitespace-nowrap">
                                {{ $tx->created_at->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if ($tx->type === \App\Models\AiCreditTransaction::TYPE_TOPUP)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300">เติม</span>
                                @elseif ($tx->type === \App\Models\AiCreditTransaction::TYPE_USAGE)
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300">ใช้งาน</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-xs font-semibold bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">ปรับยอด</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-sm font-semibold {{ (float) $tx->amount < 0 ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400' }}">
                                {{ (float) $tx->amount > 0 ? '+' : '' }}{{ number_format((float) $tx->amount, 4) }}
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-gray-500 dark:text-gray-400">
                                {{ $tx->cost_thb !== null ? number_format((float) $tx->cost_thb, 4) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right text-xs text-gray-500 dark:text-gray-400">
                                {{ $tx->commission_thb !== null ? number_format((float) $tx->commission_thb, 4) : '-' }}
                            </td>
                            <td class="px-4 py-3 text-right text-sm text-gray-900 dark:text-gray-100">
                                ฿{{ number_format((float) $tx->balance_after, 2) }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-600 dark:text-gray-300">
                                {{ $tx->creator->name ?? 'ระบบ' }}
                            </td>
                            <td class="px-4 py-3 text-xs text-gray-500 dark:text-gray-400">
                                {{ $tx->note ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-gray-500 dark:text-gray-400">ยังไม่มีธุรกรรม</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-4">{{ $transactions->links() }}</div>
    </x-ui.card>
</div>
@endsection

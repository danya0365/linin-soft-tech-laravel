@extends('layouts.admin')

@section('content')
<x-admin.page
    title="API Tokens"
    subtitle="สร้างและเพิกถอน token สำหรับแอปภายนอก / มือถือ"
    icon="fa-key"
    icon-color="text-indigo-600"
    :breadcrumbs="[['label' => 'API Tokens']]"
>
    <div class="mb-4 flex flex-wrap justify-end gap-2">
        <a href="{{ route('docs.external-api') }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 text-slate-800 rounded-lg text-sm hover:bg-slate-200 dark:bg-gray-700 dark:text-gray-100 dark:hover:bg-gray-600">
            <i class="fa fa-book"></i> API Spec
        </a>
        <a href="{{ route('docs.external-api.swagger') }}" target="_blank" rel="noopener"
           class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 text-white rounded-lg text-sm hover:bg-emerald-700">
            <i class="fa fa-flask"></i> Swagger Try it out
        </a>
        <a href="{{ route('admin.api-tokens.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm hover:bg-indigo-700">
            <i class="fa fa-plus"></i> สร้าง Token
        </a>
    </div>

    @if ($message = Session::get('success'))
        <div class="mb-4 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 text-emerald-800 dark:text-emerald-200 px-4 py-3 text-sm">
            {{ $message }}
        </div>
    @endif
    @if ($message = Session::get('error'))
        <div class="mb-4 rounded-lg bg-red-50 dark:bg-red-900/30 text-red-800 dark:text-red-200 px-4 py-3 text-sm">
            {{ $message }}
        </div>
    @endif

    <div class="overflow-x-auto rounded-xl border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-600">
                <tr>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase w-16 text-center">#</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">ชื่อ</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">Abilities</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">ใช้ล่าสุด</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase">สร้างเมื่อ</th>
                    <th class="px-4 py-3 text-xs font-semibold text-gray-600 dark:text-gray-300 uppercase w-28 text-center">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                @forelse($tokens as $index => $token)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/40">
                        <td class="px-4 py-3 text-center text-gray-500">{{ $i + $index + 1 }}</td>
                        <td class="px-4 py-3 font-medium text-gray-900 dark:text-gray-100">{{ $token->name }}</td>
                        <td class="px-4 py-3">
                            <div class="flex flex-wrap gap-1">
                                @foreach((array) $token->abilities as $ability)
                                    <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium
                                        {{ $ability === '*' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-200' : 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                        {{ $ability }}
                                    </span>
                                @endforeach
                            </div>
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            {{ $token->last_used_at ? $token->last_used_at->format('Y-m-d H:i') : '—' }}
                        </td>
                        <td class="px-4 py-3 text-gray-500 dark:text-gray-400">
                            {{ $token->created_at?->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <form action="{{ route('admin.api-tokens.destroy', $token->id) }}" method="POST"
                                  onsubmit="return confirm('เพิกถอน token นี้? แอปที่ใช้จะเข้าไม่ได้ทันที');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1.5 text-xs rounded-lg bg-red-600 text-white hover:bg-red-700">
                                    Revoke
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-10 text-center text-gray-500 dark:text-gray-400">
                            ยังไม่มี token — กด “สร้าง Token” เพื่อเริ่มใช้งาน External API
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $tokens->links() }}
    </div>
</x-admin.page>
@endsection

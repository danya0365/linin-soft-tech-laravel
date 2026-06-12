<?php

namespace App\Http\Controllers;

use App\Models\AiCreditTransaction;
use App\Models\User;
use App\Services\AiCreditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * หลังบ้าน: จัดการเครดิต AI Chat (admin เท่านั้น)
 *
 * - index: รายชื่อ user + ยอดเครดิต + สรุปต้นทุน/รายได้ค่าคอม
 * - show: ยอดคงเหลือ + ฟอร์มเติม/ปรับ + ประวัติธุรกรรม
 * - storeTransaction: เติม (บวก) หรือปรับยอด (ลบได้)
 */
class AiCreditController extends Controller
{
    public function __construct(protected AiCreditService $credits)
    {
    }

    public function index(Request $request)
    {
        $search = trim((string) $request->input('q', ''));

        $users = User::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($w) use ($search) {
                    $w->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderByDesc('ai_credit_balance')
            ->paginate()
            ->withQueryString();

        $summary = [
            'total_balance' => (float) User::sum('ai_credit_balance'),
            'total_cost' => (float) AiCreditTransaction::where('type', AiCreditTransaction::TYPE_USAGE)->sum('cost_thb'),
            'total_commission' => (float) AiCreditTransaction::where('type', AiCreditTransaction::TYPE_USAGE)->sum('commission_thb'),
        ];

        return view('ai-credit.index', compact('users', 'summary', 'search'))
            ->with('i', (request()->input('page', 1) - 1) * $users->perPage());
    }

    public function show(User $user)
    {
        $transactions = $user->aiCreditTransactions()
            ->with('creator')
            ->orderByDesc('id')
            ->paginate(20);

        return view('ai-credit.show', compact('user', 'transactions'));
    }

    public function storeTransaction(Request $request, User $user)
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'not_in:0', 'min:-100000', 'max:100000'],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        $amount = round((float) $validated['amount'], 4);
        $note = $validated['note'] ?? null;

        if ($amount > 0) {
            $this->credits->topUp($user->id, $amount, Auth::id(), $note);
            $message = 'เติมเครดิต ฿' . number_format($amount, 2) . ' ให้ ' . $user->name . ' สำเร็จ';
        } else {
            $this->credits->adjust($user->id, $amount, Auth::id(), $note);
            $message = 'ปรับยอดเครดิต ' . number_format($amount, 2) . ' บาท ของ ' . $user->name . ' สำเร็จ';
        }

        return redirect()->route('ai-credits.show', $user)->with('success', $message);
    }
}

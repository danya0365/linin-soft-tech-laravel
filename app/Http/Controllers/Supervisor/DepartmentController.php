<?php

namespace App\Http\Controllers\Supervisor;

use App\Enums\ExpenseType;
use App\Http\Controllers\Controller;
use App\Managers\ExpenseManager;
use App\Models\Department;
use App\Models\DepartmentDailyCostLog;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index()
    {
        return view('supervisor.departments.index');
    }

    public function submitDailyExpense()
    {
        if (request()->isMethod('post')) {

            request()->validate(['department_id' => 'required', 'cost' => 'required', 'daily_date' => 'required']);

            $departmentDailyCostLog = new DepartmentDailyCostLog;
            $departmentDailyCostLog->department_id = request()->get('department_id');
            $departmentDailyCostLog->cost = request()->get('cost');
            $departmentDailyCostLog->daily_date = request()->get('daily_date');
            $departmentDailyCostLog->message = request()->get('message');

            $request = request();
            if ($request->hasFile('image_upload')) {
                if ($request->file('image_upload')->isValid()) {
                    $photo = $request->file('image_upload');
                    $fileName = $photo->getClientOriginalName();
                    $fileName = str_replace(' ', '_', $fileName);
                    $date = \Carbon\Carbon::now()->format('Y-m-d');
                    $storeDir = "$date/$fileName";
                    $storePath = $photo->storeAs('images', $storeDir);
                    $departmentDailyCostLog->image_url = $storePath;
                }
            }

            $departmentDailyCostLog->save();

            ExpenseManager::create(ExpenseType::DepartmentSalary(), $departmentDailyCostLog, $departmentDailyCostLog->cost);

            return redirect()->back()->with('success', 'DepartmentDailyCostLog submit successfully');
        }
        $departments = Department::get();
        return view('supervisor.departments.submit-daily-expense', ['departments' => $departments]);
    }

    public function dailyExpenseLogs()
    {
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];

        $sortOrderSelected = request()->get('sort_order', 'id-desc');
        $departmentSelected = request()->get('department_id');

        $query = DepartmentDailyCostLog::with('department');
        $query->whereNotNull("department_id")->whereNotNull("cost")->whereNotNull("daily_date");

        if ($departmentSelected) {
            $query->where(function ($query) use ($departmentSelected) {
                $query->where('department_id', $departmentSelected);
            });
        }

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
        }
        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $departmentDailyCostLogs = $query->paginate();

        $departmentDailyCostSums = (function () {
            $query = DepartmentDailyCostLog::with('department')->select(
                DB::raw('sum(cost) as total_cost'),
                'department_id'
            )
                ->whereNotNull('department_id')
                ->groupBy('department_id');

            $dateStartAt = request()->get('date_start_at');
            $dateEndAt = request()->get('date_end_at');
            if ($dateStartAt && $dateEndAt) {
                $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
            }
            return $query->get();
        })();

        $departments = Department::get();
        return view(
            'supervisor.departments.daily-expense-logs',
            [
                'departments' => $departments,
                'departmentDailyCostLogs' => $departmentDailyCostLogs,
                'departmentDailyCostSums' => $departmentDailyCostSums,
                'departmentSelected' => $departmentSelected,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt,
            ]
        );
    }

    public function deleteDailyExpenseLog($id)
    {
        $departmentDailyCostLog = DepartmentDailyCostLog::find($id);
        $departmentDailyCostLog->delete();
        ExpenseManager::delete($departmentDailyCostLog);

        return redirect()->back()->with('success', 'DepartmentDailyCostLog deleted successfully');
    }
}

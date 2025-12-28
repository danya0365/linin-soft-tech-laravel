<?php

namespace App\Http\Controllers\Worker;

use App\Enums\ExpenseType;
use App\Http\Controllers\Controller;
use App\Managers\ExpenseManager;
use App\Models\Inventory;
use App\Models\InventoryGroup;
use App\Models\InventoryStockLog;
use Illuminate\Support\Facades\DB;

class StockController extends Controller
{
    public function index()
    {
        return redirect(route('worker.stock.select-inventory-group'));
    }

    public function selectInventoryGroup()
    {
        $inventoryGroups = InventoryGroup::all();
        return view('worker.stocks.select-inventory-group', ['inventoryGroups' => $inventoryGroups]);
    }

    public function showInventoryByGroup($inventoryGroupId)
    {
        $inventoryGroup = InventoryGroup::find($inventoryGroupId);
        $inventories = Inventory::where("inventory_group_id", $inventoryGroup->id)->paginate(100);
        return view(
            'worker.stocks.show-inventory-by-group',
            [
                'inventoryGroup' => $inventoryGroup,
                'inventories' => $inventories
            ]
        );
    }

    public function createInventoryByGroup($inventoryGroupId)
    {
        $inventoryGroup = InventoryGroup::find($inventoryGroupId);
        $inventory = new Inventory();

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'inventory_group_id' => 'required',
                    'name' => 'required',
                    'unit' => 'required',
                ]
            );

            $inventory->inventory_group_id = request()->get('inventory_group_id');
            $inventory->name = request()->get('name');
            $inventory->unit = request()->get('unit');
            $inventory->total_quantity = 0;
            $inventory->remain_quantity = 0;
            $inventory->save();

            return redirect(route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventoryGroup->id]));
        }
        return view(
            'worker.stocks.create-inventory-by-group',
            [
                'inventoryGroup' => $inventoryGroup,
                'inventory' => $inventory
            ]
        );
    }

    public function editInventory($inventoryId)
    {
        $inventory = Inventory::with('inventoryGroup')->find($inventoryId);

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'name' => 'required',
                    'unit' => 'required',
                ]
            );

            $inventory->name = request()->get('name');
            $inventory->unit = request()->get('unit');
            $inventory->save();

            return redirect(route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventory_group_id]));
        }
        return view(
            'worker.stocks.edit-inventory',
            [
                'inventory' => $inventory
            ]
        );
    }

    public function deleteInventory($inventoryId)
    {
        $inventory = Inventory::find($inventoryId);

        $inventoryStockLog = InventoryStockLog::where('inventory_id', $inventoryId)->first();

        if ($inventoryStockLog) {
            return redirect()->back()->with('error', 'ไม่สามารถลบได้ เนื่องจากมีประวัติการใช้งาน');
        }

        $inventory->delete();

        return redirect()->back()->with('success', 'ลบเรียบร้อย');
    }

    public function deleteInventoryLog($inventoryLogId)
    {
        $inventoryStockLog = InventoryStockLog::find($inventoryLogId);

        $inventory = Inventory::find($inventoryStockLog->inventory_id);

        if ($inventoryStockLog->type == 'import') {
            $inventory->total_quantity -= $inventoryStockLog->quantity;
            if ($inventory->total_quantity < 0) {
                return redirect()->back()->with('error', 'ไม่สามารถลบได้');
            }
            $inventory->remain_quantity -= $inventoryStockLog->quantity;
            if ($inventory->remain_quantity < 0) {
                return redirect()->back()->with('error', 'ไม่สามารถลบได้');
            }
            $inventory->save();
        }

        if ($inventoryStockLog->type == 'export') {
            $inventory->remain_quantity += $inventoryStockLog->quantity;
            if ($inventory->remain_quantity > $inventory->total_quantity) {
                return redirect()->back()->with('error', 'ไม่สามารถลบได้');
            }
            $inventory->save();
            ExpenseManager::delete($inventoryStockLog);
        }

        $inventoryStockLog->delete();

        return redirect()->back()->with('error', 'ลบเรียบร้อย');
    }

    public function getInventoryIncreaseStock($inventoryId)
    {
        $inventory = Inventory::with('inventoryGroup')->find($inventoryId);

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'increase_quantity' => 'required|numeric',
                    'created_at' => 'required'
                ]
            );

            $increaseQuantity = request()->get('increase_quantity');

            $inventoryStockLog = new InventoryStockLog;
            $inventoryStockLog->inventory_id = $inventoryId;
            $inventoryStockLog->type = 'import';
            $inventoryStockLog->quantity = $increaseQuantity;
            $inventoryStockLog->employee_id = null; // TODO: add select employee screen
            $inventoryStockLog->cost = 0;
            $inventoryStockLog->timestamps = false;
            $inventoryStockLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $inventoryStockLog->updated_at = \Carbon\Carbon::now();
            $inventoryStockLog->save();

            $inventory->total_quantity += $increaseQuantity;
            $inventory->remain_quantity += $increaseQuantity;
            $inventory->save();

            return redirect(route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id]));
        }

        return view(
            'worker.stocks.inventory-increase-stock',
            [
                'inventory' => $inventory
            ]
        );
    }

    public function getInventoryDecreaseStock($inventoryId)
    {
        $inventory = Inventory::with('inventoryGroup')->find($inventoryId);

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'decrease_quantity' => 'required|numeric',
                    'cost' => 'required|numeric',
                    'created_at' => 'required'
                ]
            );

            $decreaseQuantity = request()->get('decrease_quantity');

            $inventoryStockLog = new InventoryStockLog;
            $inventoryStockLog->inventory_id = $inventoryId;
            $inventoryStockLog->type = 'export';
            $inventoryStockLog->quantity = $decreaseQuantity;
            $inventoryStockLog->employee_id = null; // TODO: add select employee screen
            $inventoryStockLog->cost = request()->get('cost');
            $inventoryStockLog->timestamps = false;
            $inventoryStockLog->created_at = \Carbon\Carbon::parse(request()->get('created_at'));
            $inventoryStockLog->updated_at = \Carbon\Carbon::now();
            $inventoryStockLog->save();

            $inventory->remain_quantity -= $decreaseQuantity;
            $inventory->save();

            ExpenseManager::create(ExpenseType::Inventory(), $inventoryStockLog, $inventoryStockLog->cost, $inventoryStockLog->created_at);

            return redirect(route('worker.stock.show-inventory-by-group', ['inventoryGroupId' => $inventory->inventoryGroup->id]));
        }

        return view(
            'worker.stocks.inventory-decrease-stock',
            [
                'inventory' => $inventory
            ]
        );
    }

    public function showInventoryLogsById($inventoryId)
    {
        $inventory = Inventory::with('inventoryGroup')->find($inventoryId);
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];

        $sortOrderSelected = request()->get('sort_order', 'id-desc');

        $query = InventoryStockLog::with(['inventory' => function ($query) {
            $query->with('inventoryGroup');
        }])->where("inventory_id", $inventory->id);

        $dateStartAt = request()->get('date_start_at');
        $dateEndAt = request()->get('date_end_at');
        if ($dateStartAt && $dateEndAt) {
            $query->whereBetween('created_at', [$dateStartAt . ' 00:00:00', $dateEndAt . ' 23:59:59']);
        }

        if ($sortOrderSelected) {
            list($sort, $order) = explode('-', $sortOrderSelected);
            $query->orderBy($sort, $order);
        }

        $inventoryLogs = $query->paginate();

        return view(
            'worker.stocks.inventory-logs-by-id',
            [
                'inventory' => $inventory,
                'inventoryLogs' => $inventoryLogs,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }

    public function showInventoriesLogs()
    {
        $sortOrders = [
            ['var' => 'id-desc', 'name' => 'ใหม่ที่สุด - Newest'],
            ['var' => 'id-asc', 'name' => 'เก่าที่สุด - Oldest'],
        ];

        $sortOrderSelected = request()->get('sort_order', 'id-desc');
        $inventoryGroupSelected = request()->get('inventory_group_id');

        $query = InventoryStockLog::with(['inventory' => function ($query) {
            $query->with('inventoryGroup');
        }]);

        if ($inventoryGroupSelected) {
            $query->where(function ($query) use ($inventoryGroupSelected) {
                $query->whereHas('inventory', function ($query) use ($inventoryGroupSelected) {
                    $query->where('inventory_group_id', $inventoryGroupSelected);
                });
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

        $inventoryLogs = $query->paginate();
        $inventoryGroups = InventoryGroup::get();

        return view(
            'worker.stocks.inventories-logs',
            [
                'inventoryLogs' => $inventoryLogs,
                'inventoryGroups' => $inventoryGroups,
                'sortOrders' => $sortOrders,
                'sortOrderSelected' => $sortOrderSelected,
                'inventoryGroupSelected' => $inventoryGroupSelected,
                'dateStartAt' => $dateStartAt,
                'dateEndAt' => $dateEndAt
            ]
        );
    }
}
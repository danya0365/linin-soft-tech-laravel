<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
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
        $inventories = Inventory::where("inventory_group_id", $inventoryGroup->id)->paginate();
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
                    'total_quantity' => 'required',
                ]
            );

            $inventory->inventory_group_id = request()->get('inventory_group_id');
            $inventory->name = request()->get('name');
            $inventory->unit = request()->get('unit');
            $inventory->total_quantity = request()->get('total_quantity');
            $inventory->remain_quantity = request()->get('total_quantity');
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

    public function getInventoryIncreaseStock($inventoryId)
    {
        $inventory = Inventory::with('inventoryGroup')->find($inventoryId);

        if (request()->isMethod('post')) {

            request()->validate(
                [
                    'increase_quantity' => 'required|numeric'
                ]
            );

            $increaseQuantity = request()->get('increase_quantity');

            $inventoryStockLog = new InventoryStockLog;
            $inventoryStockLog->inventory_id = $inventoryId;
            $inventoryStockLog->type = 'import';
            $inventoryStockLog->quantity = $increaseQuantity;
            $inventoryStockLog->employee_id = null; // TODO: add select employee screen
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
        return view(
            'worker.stocks.inventory-decrease-stock',
            [
                'inventory' => $inventory
            ]
        );
    }
}
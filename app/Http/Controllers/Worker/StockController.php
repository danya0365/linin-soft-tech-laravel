<?php

namespace App\Http\Controllers\Worker;

use App\Http\Controllers\Controller;
use App\Models\Inventory;
use App\Models\InventoryGroup;
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
}
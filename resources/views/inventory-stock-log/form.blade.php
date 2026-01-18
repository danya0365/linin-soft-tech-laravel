<x-crud.form-group name="employee_id" label="Employee Id" type="select" :value="$inventoryStockLog->employee_id ?? old('employee_id')" placeholder="Enter Employee Id" />
<x-crud.form-group name="inventory_id" label="Inventory Id" type="select" :value="$inventoryStockLog->inventory_id ?? old('inventory_id')" placeholder="Enter Inventory Id" />
<x-crud.form-group name="type" label="Type" type="text" :value="$inventoryStockLog->type ?? old('type')" placeholder="Enter Type" />
<x-crud.form-group name="quantity" label="Quantity" type="text" :value="$inventoryStockLog->quantity ?? old('quantity')" placeholder="Enter Quantity" />
<x-crud.form-group name="cost" label="Cost" type="number" :value="$inventoryStockLog->cost ?? old('cost')" placeholder="Enter Cost" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($inventoryStockLog->id) ? 'Update' : 'Create' }} Inventory Stock Log
    </x-ui.button>
    <x-ui.button :href="route('inventory-stock-logs.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
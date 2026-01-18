<x-crud.form-group name="inventory_group_id" label="Inventory Group Id" type="select" :value="$inventory->inventory_group_id ?? old('inventory_group_id')" placeholder="Enter Inventory Group Id" />
<x-crud.form-group name="name" label="Name" type="text" :value="$inventory->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="unit" label="Unit" type="text" :value="$inventory->unit ?? old('unit')" placeholder="Enter Unit" />
<x-crud.form-group name="total_quantity" label="Total Quantity" type="text" :value="$inventory->total_quantity ?? old('total_quantity')" placeholder="Enter Total Quantity" />
<x-crud.form-group name="remain_quantity" label="Remain Quantity" type="text" :value="$inventory->remain_quantity ?? old('remain_quantity')" placeholder="Enter Remain Quantity" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($inventory->id) ? 'Update' : 'Create' }} Inventory
    </x-ui.button>
    <x-ui.button :href="route('inventories.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
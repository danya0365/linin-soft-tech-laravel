<x-crud.form-group name="type_name" label="Type Name" type="text" :value="$income->type_name ?? old('type_name')" placeholder="Enter Type Name" />
<x-crud.form-group name="table_name" label="Table Name" type="text" :value="$income->table_name ?? old('table_name')" placeholder="Enter Table Name" />
<x-crud.form-group name="table_id" label="Table Id" type="select" :value="$income->table_id ?? old('table_id')" placeholder="Enter Table Id" />
<x-crud.form-group name="amount" label="Amount" type="text" :value="$income->amount ?? old('amount')" placeholder="Enter Amount" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($income->id) ? 'Update' : 'Create' }} Income
    </x-ui.button>
    <x-ui.button :href="route('incomes.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
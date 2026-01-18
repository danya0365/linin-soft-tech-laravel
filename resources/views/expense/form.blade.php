<x-crud.form-group name="type_name" label="Type Name" type="text" :value="$expense->type_name ?? old('type_name')" placeholder="Enter Type Name" />
<x-crud.form-group name="table_name" label="Table Name" type="text" :value="$expense->table_name ?? old('table_name')" placeholder="Enter Table Name" />
<x-crud.form-group name="table_id" label="Table Id" type="select" :value="$expense->table_id ?? old('table_id')" placeholder="Enter Table Id" />
<x-crud.form-group name="amount" label="Amount" type="text" :value="$expense->amount ?? old('amount')" placeholder="Enter Amount" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($expense->id) ? 'Update' : 'Create' }} Expense
    </x-ui.button>
    <x-ui.button :href="route('expenses.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
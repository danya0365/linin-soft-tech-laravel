<x-crud.form-group name="customer_id" label="Customer Id" type="select" :value="$customerOperationDailySummary->customer_id ?? old('customer_id')" placeholder="Enter Customer Id" />
<x-crud.form-group name="operation_date" label="Operation Date" type="date" :value="$customerOperationDailySummary->operation_date ?? old('operation_date')" placeholder="Enter Operation Date" />
<x-crud.form-group name="total_wet_weight" label="Total Wet Weight" type="number" :value="$customerOperationDailySummary->total_wet_weight ?? old('total_wet_weight')" placeholder="Enter Total Wet Weight" />
<x-crud.form-group name="total_dry_weight" label="Total Dry Weight" type="number" :value="$customerOperationDailySummary->total_dry_weight ?? old('total_dry_weight')" placeholder="Enter Total Dry Weight" />
<x-crud.form-group name="total_iron_piece" label="Total Iron Piece" type="number" :value="$customerOperationDailySummary->total_iron_piece ?? old('total_iron_piece')" placeholder="Enter Total Iron Piece" />
<x-crud.form-group name="total_packing_piece" label="Total Packing Piece" type="number" :value="$customerOperationDailySummary->total_packing_piece ?? old('total_packing_piece')" placeholder="Enter Total Packing Piece" />
<x-crud.form-group name="total_edit_collect_weight" label="Total Edit Collect Weight" type="number" :value="$customerOperationDailySummary->total_edit_collect_weight ?? old('total_edit_collect_weight')" placeholder="Enter Total Edit Collect Weight" />
<x-crud.form-group name="total_collect_weight" label="Total Collect Weight" type="number" :value="$customerOperationDailySummary->total_collect_weight ?? old('total_collect_weight')" placeholder="Enter Total Collect Weight" />
<x-crud.form-group name="total_billing_weight" label="Total Billing Weight" type="number" :value="$customerOperationDailySummary->total_billing_weight ?? old('total_billing_weight')" placeholder="Enter Total Billing Weight" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($customerOperationDailySummary->id) ? 'Update' : 'Create' }} Customer Operation Daily Summary
    </x-ui.button>
    <x-ui.button :href="route('customer-operation-daily-summaries.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
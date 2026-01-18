{{-- Customer Form Fields --}}

<x-crud.form-group 
    name="name"
    label="Name"
    :value="$customer->name ?? old('name')"
    placeholder="Enter customer name"
    required
/>

<x-crud.form-group 
    name="customer_group_id"
    label="Customer Group"
    type="select"
    :value="$customer->customer_group_id ?? old('customer_group_id')"
    :options="App\Models\CustomerGroup::pluck('name', 'id')->toArray()"
    help="Select a customer group for this customer"
/>

<x-crud.form-group 
    name="total_wet_weight"
    label="Total Wet Weight"
    type="number"
    :value="$customer->total_wet_weight ?? old('total_wet_weight')"
    placeholder="0.00"
/>

<x-crud.form-group 
    name="total_dry_weight"
    label="Total Dry Weight"
    type="number"
    :value="$customer->total_dry_weight ?? old('total_dry_weight')"
    placeholder="0.00"
/>

<x-crud.form-group 
    name="total_billing_weight"
    label="Total Billing Weight"
    type="number"
    :value="$customer->total_billing_weight ?? old('total_billing_weight')"
    placeholder="0.00"
/>

<x-crud.form-group 
    name="total_edit_weight"
    label="Total Edit Weight"
    type="number"
    :value="$customer->total_edit_weight ?? old('total_edit_weight')"
    placeholder="0.00"
/>

<x-crud.form-group 
    name="total_billing_payment"
    label="Total Billing Payment"
    type="number"
    :value="$customer->total_billing_payment ?? old('total_billing_payment')"
    placeholder="0.00"
/>

{{-- Form Actions --}}
<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button 
        type="submit"
        variant="primary"
        icon="fa fa-save"
    >
        {{ isset($customer->id) ? 'Update' : 'Create' }} Customer
    </x-ui.button>
    
    <x-ui.button 
        :href="route('customers.index')"
        variant="secondary"
        icon="fa fa-times"
    >
        Cancel
    </x-ui.button>
</div>
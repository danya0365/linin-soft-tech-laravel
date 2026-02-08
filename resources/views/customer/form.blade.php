<x-crud.form-group name="name" label="Name" type="text" :value="$customer->name ?? old('name')" placeholder="Enter Name" />
@php
    $customerGroupOptions = [];
    foreach (\App\Models\CustomerGroup::get() as $customerGroup) {
        $customerGroupOptions[$customerGroup->id] = $customerGroup->name;
    }
@endphp
<x-crud.form-group 
    name="customer_group_id" 
    label="Customer Group" 
    type="select" 
    :value="$customer->customer_group_id ?? old('customer_group_id')" 
    placeholder="เลือก"
    :options="$customerGroupOptions" 
/>

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($customer->id) ? 'Update' : 'Create' }} Customer
    </x-ui.button>
    <x-ui.button :href="route('customers.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
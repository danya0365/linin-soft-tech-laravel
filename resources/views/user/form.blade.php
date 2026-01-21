<x-crud.form-group name="name" label="Name" type="text" :value="$user->name ?? old('name')" placeholder="Enter Name" required />
<x-crud.form-group name="email" label="Email" type="email" :value="$user->email ?? old('email')" placeholder="Enter Email" required />
{{-- Password field: Leave blank to keep existing password when editing --}}
<x-crud.form-group name="password" label="Password" type="password" :value="''" placeholder="Enter Password (optional for edit)" />

{{-- Role Selection --}}
<x-crud.form-group 
    name="role" 
    label="Role" 
    type="select" 
    :value="$user->role ?? old('role')" 
    placeholder="เลือก"
    :options="$userRoles ?? []" 
/>

{{-- Customer Account Selection --}}
@php
    $customerAccountOptions = [];
    if (isset($customerAccounts)) {
        foreach ($customerAccounts as $customerAccount) {
            $customerAccountOptions[$customerAccount->id] = $customerAccount->name;
        }
    }
@endphp
<x-crud.form-group 
    name="customer_account" 
    label="Customer ID" 
    type="select" 
    :value="$user->customer_account ?? old('customer_account')" 
    placeholder="เลือก"
    :options="$customerAccountOptions" 
/>

{{-- Access Permissions - Using Radio Buttons (Yes/No) --}}
<x-crud.form-group 
    name="is_can_access_admin" 
    label="Access Admin" 
    type="radio" 
    :value="$user->is_can_access_admin ?? old('is_can_access_admin', '0')" 
    :options="['1' => 'Yes', '0' => 'No']" 
/>
<x-crud.form-group 
    name="is_can_access_manager" 
    label="Access Manager" 
    type="radio" 
    :value="$user->is_can_access_manager ?? old('is_can_access_manager', '0')" 
    :options="['1' => 'Yes', '0' => 'No']" 
/>
<x-crud.form-group 
    name="is_can_access_supervisor" 
    label="Access Supervisor" 
    type="radio" 
    :value="$user->is_can_access_supervisor ?? old('is_can_access_supervisor', '0')" 
    :options="['1' => 'Yes', '0' => 'No']" 
/>
<x-crud.form-group 
    name="is_can_access_customer" 
    label="Access Customer" 
    type="radio" 
    :value="$user->is_can_access_customer ?? old('is_can_access_customer', '0')" 
    :options="['1' => 'Yes', '0' => 'No']" 
/>
<x-crud.form-group 
    name="is_can_access_worker" 
    label="Access Worker" 
    type="radio" 
    :value="$user->is_can_access_worker ?? old('is_can_access_worker', '0')" 
    :options="['1' => 'Yes', '0' => 'No']" 
/>

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($user->id) ? 'Update' : 'Create' }} User
    </x-ui.button>
    <x-ui.button :href="route('users.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
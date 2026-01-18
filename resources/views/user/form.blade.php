<x-crud.form-group name="name" label="Name" type="text" :value="$user->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="email" label="Email" type="email" :value="$user->email ?? old('email')" placeholder="Enter Email" />
<x-crud.form-group name="password" label="Password" type="password" :value="$user->password ?? old('password')" placeholder="Enter Password" />
<x-crud.form-group name="role" label="Role" type="text" :value="$user->role ?? old('role')" placeholder="Enter Role" />
<x-crud.form-group name="customer_account" label="Customer Account" type="text" :value="$user->customer_account ?? old('customer_account')" placeholder="Enter Customer Account" />
<x-crud.form-group name="is_can_access_admin" label="Is Can Access Admin" type="checkbox" :value="$user->is_can_access_admin ?? old('is_can_access_admin')" placeholder="Enter Is Can Access Admin" />
<x-crud.form-group name="is_can_access_manager" label="Is Can Access Manager" type="checkbox" :value="$user->is_can_access_manager ?? old('is_can_access_manager')" placeholder="Enter Is Can Access Manager" />
<x-crud.form-group name="is_can_access_supervisor" label="Is Can Access Supervisor" type="checkbox" :value="$user->is_can_access_supervisor ?? old('is_can_access_supervisor')" placeholder="Enter Is Can Access Supervisor" />
<x-crud.form-group name="is_can_access_customer" label="Is Can Access Customer" type="checkbox" :value="$user->is_can_access_customer ?? old('is_can_access_customer')" placeholder="Enter Is Can Access Customer" />
<x-crud.form-group name="is_can_access_worker" label="Is Can Access Worker" type="checkbox" :value="$user->is_can_access_worker ?? old('is_can_access_worker')" placeholder="Enter Is Can Access Worker" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($user->id) ? 'Update' : 'Create' }} User
    </x-ui.button>
    <x-ui.button :href="route('users.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
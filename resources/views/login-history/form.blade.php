<x-crud.form-group name="user_id" label="User Id" type="select" :value="$loginHistory->user_id ?? old('user_id')" placeholder="Enter User Id" />
<x-crud.form-group name="name" label="Name" type="text" :value="$loginHistory->name ?? old('name')" placeholder="Enter Name" />
<x-crud.form-group name="email" label="Email" type="email" :value="$loginHistory->email ?? old('email')" placeholder="Enter Email" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($loginHistory->id) ? 'Update' : 'Create' }} Login History
    </x-ui.button>
    <x-ui.button :href="route('login-history.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
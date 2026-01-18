<x-crud.form-group name="operation_id" label="Operation Id" type="select" :value="$operationLog->operation_id ?? old('operation_id')" placeholder="Enter Operation Id" />
<x-crud.form-group name="employee_id" label="Employee Id" type="select" :value="$operationLog->employee_id ?? old('employee_id')" placeholder="Enter Employee Id" />
<x-crud.form-group name="action_name" label="Action Name" type="text" :value="$operationLog->action_name ?? old('action_name')" placeholder="Enter Action Name" />
<x-crud.form-group name="old_values" label="Old Values" type="text" :value="$operationLog->old_values ?? old('old_values')" placeholder="Enter Old Values" />
<x-crud.form-group name="new_values" label="New Values" type="text" :value="$operationLog->new_values ?? old('new_values')" placeholder="Enter New Values" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($operationLog->id) ? 'Update' : 'Create' }} Operation Log
    </x-ui.button>
    <x-ui.button :href="route('operation-logs.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
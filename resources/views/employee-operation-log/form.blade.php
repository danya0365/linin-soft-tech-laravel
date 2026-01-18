<x-crud.form-group name="employee_id" label="Employee Id" type="select" :value="$employeeOperationLog->employee_id ?? old('employee_id')" placeholder="Enter Employee Id" />
<x-crud.form-group name="operation_type" label="Operation Type" type="text" :value="$employeeOperationLog->operation_type ?? old('operation_type')" placeholder="Enter Operation Type" />
<x-crud.form-group name="action_type" label="Action Type" type="text" :value="$employeeOperationLog->action_type ?? old('action_type')" placeholder="Enter Action Type" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($employeeOperationLog->id) ? 'Update' : 'Create' }} Employee Operation Log
    </x-ui.button>
    <x-ui.button :href="route('employee-operation-log.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
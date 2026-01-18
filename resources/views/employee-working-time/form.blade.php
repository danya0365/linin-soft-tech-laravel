<x-crud.form-group name="employee_id" label="Employee Id" type="select" :value="$employeeWorkingTime->employee_id ?? old('employee_id')" placeholder="Enter Employee Id" />
<x-crud.form-group name="working_date" label="Working Date" type="date" :value="$employeeWorkingTime->working_date ?? old('working_date')" placeholder="Enter Working Date" />
<x-crud.form-group name="time_duration" label="Time Duration" type="text" :value="$employeeWorkingTime->time_duration ?? old('time_duration')" placeholder="Enter Time Duration" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($employeeWorkingTime->id) ? 'Update' : 'Create' }} Employee Working Time
    </x-ui.button>
    <x-ui.button :href="route('employee-working-times.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
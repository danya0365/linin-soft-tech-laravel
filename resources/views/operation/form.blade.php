<x-crud.form-group name="operation_type" label="Operation Type" type="text" :value="$operation->operation_type ?? old('operation_type')" placeholder="Enter Operation Type" />
<x-crud.form-group name="employee_id" label="Employee Id" type="select" :value="$operation->employee_id ?? old('employee_id')" placeholder="Enter Employee Id" />
<x-crud.form-group name="customer_id" label="Customer Id" type="select" :value="$operation->customer_id ?? old('customer_id')" placeholder="Enter Customer Id" />
<x-crud.form-group name="wash_employee_id" label="Wash Employee Id" type="select" :value="$operation->wash_employee_id ?? old('wash_employee_id')" placeholder="Enter Wash Employee Id" />
<x-crud.form-group name="dry_employee_id" label="Dry Employee Id" type="select" :value="$operation->dry_employee_id ?? old('dry_employee_id')" placeholder="Enter Dry Employee Id" />
<x-crud.form-group name="iron_employee_id" label="Iron Employee Id" type="select" :value="$operation->iron_employee_id ?? old('iron_employee_id')" placeholder="Enter Iron Employee Id" />
<x-crud.form-group name="packing_employee_id" label="Packing Employee Id" type="select" :value="$operation->packing_employee_id ?? old('packing_employee_id')" placeholder="Enter Packing Employee Id" />
<x-crud.form-group name="collect_employee_id" label="Collect Employee Id" type="select" :value="$operation->collect_employee_id ?? old('collect_employee_id')" placeholder="Enter Collect Employee Id" />
<x-crud.form-group name="job_case" label="Job Case" type="text" :value="$operation->job_case ?? old('job_case')" placeholder="Enter Job Case" />
<x-crud.form-group name="washing_machine_id" label="Washing Machine Id" type="select" :value="$operation->washing_machine_id ?? old('washing_machine_id')" placeholder="Enter Washing Machine Id" />
<x-crud.form-group name="dryer_machine_id" label="Dryer Machine Id" type="select" :value="$operation->dryer_machine_id ?? old('dryer_machine_id')" placeholder="Enter Dryer Machine Id" />
<x-crud.form-group name="total_wet_weight" label="Total Wet Weight" type="number" :value="$operation->total_wet_weight ?? old('total_wet_weight')" placeholder="Enter Total Wet Weight" />
<x-crud.form-group name="total_dry_weight" label="Total Dry Weight" type="number" :value="$operation->total_dry_weight ?? old('total_dry_weight')" placeholder="Enter Total Dry Weight" />
<x-crud.form-group name="total_iron_piece" label="Total Iron Piece" type="number" :value="$operation->total_iron_piece ?? old('total_iron_piece')" placeholder="Enter Total Iron Piece" />
<x-crud.form-group name="total_packing_piece" label="Total Packing Piece" type="number" :value="$operation->total_packing_piece ?? old('total_packing_piece')" placeholder="Enter Total Packing Piece" />
<x-crud.form-group name="colors" label="Colors" type="text" :value="$operation->colors ?? old('colors')" placeholder="Enter Colors" />
<x-crud.form-group name="search_tags" label="Search Tags" type="text" :value="$operation->search_tags ?? old('search_tags')" placeholder="Enter Search Tags" />
<x-crud.form-group name="status" label="Status" type="text" :value="$operation->status ?? old('status')" placeholder="Enter Status" />

<div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
    <x-ui.button type="submit" variant="primary" icon="fa fa-save">
        {{ isset($operation->id) ? 'Update' : 'Create' }} Operation
    </x-ui.button>
    <x-ui.button :href="route('operation.index')" variant="secondary" icon="fa fa-times">Cancel</x-ui.button>
</div>
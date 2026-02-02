@extends('layouts.worker')

@section('content')
<x-worker.page 
    title="บันทึกการใช้ไฟฟ้า" 
    subtitle="Electricity Consumption Log" 
    icon="fa-solid fa-bolt"
    :breadcrumbs="[
        ['label' => 'Energy Resource', 'route' => route('worker.energy-resource')],
        ['label' => 'พนักงาน: ' . $energyResourceLog->employee->name, 'route' => route('worker.energy-resource.select-employee', ['energyResourceLogId' => $energyResourceLog->id])],
        ['label' => 'บันทึกการใช้ไฟฟ้า']
    ]"
>
    <x-worker.card title="เพิ่มบันทึกการใช้ไฟฟ้า">
        <form action="{{ request()->url() }}" method="POST" role="form" enctype="multipart/form-data">
            @csrf
            
            <x-crud.form-group 
                name="value" 
                label="ปริมาณที่ใช้ไฟฟ้า (kw/hour)" 
                type="number" 
                step="0.01" 
                placeholder="0.00"
                :value="old('value')"
            />

            <x-crud.form-group 
                name="cost" 
                label="จำนวนเงิน - Cost (Thai Baht)" 
                type="number" 
                step="0.01" 
                placeholder="0.00"
                :value="old('cost')"
            />

            <x-crud.form-group 
                name="created_at" 
                label="วันที่" 
                type="date" 
                :value="old('created_at')"
            />

            <div class="flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700">
                <x-ui.button type="submit" variant="primary" icon="fa-solid fa-save">
                    บันทึกข้อมูล
                </x-ui.button>
                <x-ui.button type="reset" variant="secondary" icon="fa-solid fa-rotate-left">
                    รีเซ็ต
                </x-ui.button>
            </div>
        </form>
    </x-worker.card>
</x-worker.page>
@endsection
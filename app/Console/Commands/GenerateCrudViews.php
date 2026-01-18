<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class GenerateCrudViews extends Command
{
    protected $signature = 'crud:generate-views {module} {--force}';
    protected $description = 'Generate Tailwind CRUD views for a module';

    public function handle()
    {
        $module = $this->argument('module');
        $force = $this->option('force');
        
        $this->info("Generating CRUD views for: {$module}");
        
        // Analyze existing form to extract fields
        $formPath = resource_path("views/{$module}/form.blade.php");
        if (!File::exists($formPath)) {
            $this->error("Form file not found: {$formPath}");
            return 1;
        }
        
        $formContent = File::get($formPath);
        $fields = $this->extractFields($formContent);
        
        $this->info("Found " . count($fields) . " fields");
        
        // Generate views
        $this->generateIndex($module, $fields, $force);
        $this->generateCreate($module, $force);
        $this->generateEdit($module, $force);
        $this->generateForm($module, $fields, $force);
        $this->generateShow($module, $fields, $force);
        
        $this->info("✅ Successfully generated all views for {$module}");
        return 0;
    }
    
    protected function extractFields($content)
    {
        $fields = [];
        
        // Extract Form::label or form fields
        preg_match_all('/Form::label\([\'"](\w+)[\'"]\)/', $content, $matches);
        if (!empty($matches[1])) {
            foreach ($matches[1] as $field) {
                $fields[] = [
                    'name' => $field,
                    'label' => Str::title(str_replace('_', ' ', $field)),
                    'type' => $this->guessFieldType($field),
                ];
            }
        }
        
        return $fields;
    }
    
    protected function guessFieldType($fieldName)
    {
        if (str_contains($fieldName, '_id')) return 'select';
        if (str_contains($fieldName, 'date')) return 'date';
        if (str_contains($fieldName, 'email')) return 'email';
        if (str_contains($fieldName, 'password')) return 'password';
        if (str_contains($fieldName, 'weight') || str_contains($fieldName, 'price') || str_contains($fieldName, 'cost') || str_contains($fieldName, 'piece')) return 'number';
        if (str_contains($fieldName, 'description') || str_contains($fieldName, 'message')) return 'textarea';
        return 'text';
    }
    
    protected function generateIndex($module, $fields, $force)
    {
        $variableName = Str::camel(Str::plural($module));
        $routePrefix = Str::slug($module);
        $title = Str::title(str_replace('-', ' ', $module));
        
        $headers = ['No'];
        $columns = [];
        foreach ($fields as $field) {
            if ($field['name'] !== 'id' && !str_contains($field['name'], 'password')) {
                $headers[] = $field['label'];
                $columns[] = $field['name'];
            }
        }
        $headers[] = 'Actions';
        
        $headersStr = "'" . implode("', '", $headers) . "'";
        $columnsStr = "'" . implode("', '", $columns) . "'";
        
        $content = "@extends('layouts.app')
@section('template_title')
    {$title}
@endsection
@section('content')
<div class=\"container mx-auto px-4 py-6 max-w-7xl\">
    <x-crud.breadcrumb :items=\"[['label' => 'Admin', 'route' => 'admin'],['label' => '{$title}']]\" />
    <x-ui.card>
        <x-slot:header>
            <x-crud.page-header title=\"{$title}\" :createRoute=\"route('{$routePrefix}.create')\" />
        </x-slot:header>
        @if (\$message = Session::get('success'))
            <x-ui.alert variant=\"success\" dismissible=\"true\">{{ \$message }}</x-ui.alert>
        @endif
        <x-crud.data-table 
            :headers=\"[{$headersStr}]\"
            :data=\"\${$variableName}\"
            :columns=\"[{$columnsStr}]\"
            resource=\"{$routePrefix}\"
            :startIndex=\"\$i\"
        />
    </x-ui.card>
    <div class=\"mt-6\">{{ \${$variableName}->links() }}</div>
</div>
@endsection";
        
        $this->writeFile("views/{$module}/index.blade.php", $content, $force);
    }
    
    protected function generateCreate($module, $force)
    {
        $routePrefix = Str::slug($module);
        $title = Str::title(str_replace('-', ' ', $module));
        
        $content = "@extends('layouts.app')
@section('template_title')
    Create {$title}
@endsection
@section('content')
<div class=\"container mx-auto px-4 py-6 max-w-4xl\">
    <x-crud.breadcrumb :items=\"[['label' => 'Admin', 'route' => 'admin'],['label' => '{$title}', 'route' => '{$routePrefix}.index'],['label' => 'Create']]\" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class=\"text-xl font-semibold text-gray-900 dark:text-white\">Create {$title}</h2></x-slot:header>
        <form method=\"POST\" action=\"{{ route('{$routePrefix}.store') }}\" role=\"form\" enctype=\"multipart/form-data\">
            @csrf
            @include('{$module}.form')
        </form>
    </x-ui.card>
</div>
@endsection";
        
        $this->writeFile("views/{$module}/create.blade.php", $content, $force);
    }
    
    protected function generateEdit($module, $force)
    {
        $modelVar = Str::camel(Str::singular($module));
        $routePrefix = Str::slug($module);
        $title = Str::title(str_replace('-', ' ', $module));
        
        $content = "@extends('layouts.app')
@section('template_title')
    Update {$title}
@endsection
@section('content')
<div class=\"container mx-auto px-4 py-6 max-w-4xl\">
    <x-crud.breadcrumb :items=\"[['label' => 'Admin', 'route' => 'admin'],['label' => '{$title}', 'route' => '{$routePrefix}.index'],['label' => 'Edit']]\" />
    @includeif('partials.errors')
    <x-ui.card>
        <x-slot:header><h2 class=\"text-xl font-semibold text-gray-900 dark:text-white\">Update {$title}</h2></x-slot:header>
        <form method=\"POST\" action=\"{{ route('{$routePrefix}.update', \${$modelVar}->id) }}\" role=\"form\" enctype=\"multipart/form-data\">
            {{ method_field('PATCH') }}
            @csrf
            @include('{$module}.form')
        </form>
    </x-ui.card>
</div>
@endsection";
        
        $this->writeFile("views/{$module}/edit.blade.php", $content, $force);
    }
    
    protected function generateForm($module, $fields, $force)
    {
        $modelVar = Str::camel(Str::singular($module));
        $routePrefix = Str::slug($module);
        $title = Str::title(str_replace('-', ' ', $module));
        
        $formGroups = [];
        foreach ($fields as $field) {
            if ($field['name'] === 'id') continue;
            
            $formGroups[] = "<x-crud.form-group name=\"{$field['name']}\" label=\"{$field['label']}\" type=\"{$field['type']}\" :value=\"\${$modelVar}->{$field['name']} ?? old('{$field['name']}')\" placeholder=\"Enter {$field['label']}\" />";
        }
        
        $formGroupsStr = implode("\n", $formGroups);
        
        $content = "{$formGroupsStr}

<div class=\"flex items-center justify-end gap-3 mt-6 pt-6 border-t border-gray-200 dark:border-gray-700\">
    <x-ui.button type=\"submit\" variant=\"primary\" icon=\"fa fa-save\">
        {{ isset(\${$modelVar}->id) ? 'Update' : 'Create' }} {$title}
    </x-ui.button>
    <x-ui.button :href=\"route('{$routePrefix}.index')\" variant=\"secondary\" icon=\"fa fa-times\">Cancel</x-ui.button>
</div>";
        
        $this->writeFile("views/{$module}/form.blade.php", $content, $force);
    }
    
    protected function generateShow($module, $fields, $force)
    {
        $modelVar = Str::camel(Str::singular($module));
        $routePrefix = Str::slug($module);
        $title = Str::title(str_replace('-', ' ', $module));
        
        $detailFields = [];
        foreach ($fields as $field) {
            if ($field['name'] === 'id') continue;
            $detailFields[] = "<div><p class=\"text-sm text-gray-500 dark:text-gray-400 mb-1\">{$field['label']}</p><p class=\"font-semibold text-gray-900 dark:text-white\">{{ \${$modelVar}->{$field['name']} ?? '-' }}</p></div>";
        }
        
        $detailFieldsStr = implode("\n            ", $detailFields);
        
        $content = "@extends('layouts.app')
@section('template_title')
    Show {$title}
@endsection
@section('content')
<div class=\"container mx-auto px-4 py-6 max-w-4xl\">
    <x-crud.breadcrumb :items=\"[['label' => 'Admin', 'route' => 'admin'],['label' => '{$title}', 'route' => '{$routePrefix}.index'],['label' => 'Details']]\" />
    <x-ui.card>
        <x-slot:header>
            <div class=\"flex items-center justify-between\">
                <h2 class=\"text-xl font-semibold text-gray-900 dark:text-white\">{$title} Details</h2>
                <div class=\"flex items-center gap-2\">
                    <x-ui.button :href=\"route('{$routePrefix}.edit', \${$modelVar}->id)\" variant=\"success\" size=\"sm\" icon=\"fa fa-edit\">Edit</x-ui.button>
                    <x-ui.button :href=\"route('{$routePrefix}.index')\" variant=\"secondary\" size=\"sm\" icon=\"fa fa-arrow-left\">Back</x-ui.button>
                </div>
            </div>
        </x-slot:header>
        <div class=\"grid md:grid-cols-2 gap-6\">
            {$detailFieldsStr}
        </div>
    </x-ui.card>
</div>
@endsection";
        
        $this->writeFile("views/{$module}/show.blade.php", $content, $force);
    }
    
    protected function writeFile($path, $content, $force)
    {
        $fullPath = resource_path($path);
        
        if (File::exists($fullPath) && !$force) {
            $this->warn("Skipped (exists): {$path}");
            return;
        }
        
        File::ensureDirectoryExists(dirname($fullPath));
        File::put($fullPath, $content);
        $this->info("Created: {$path}");
    }
}

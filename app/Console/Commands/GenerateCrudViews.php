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
        
        // Extract fields directly from Model's $fillable
        $fields = $this->extractFieldsFromModel($module);
        
        // ERROR: If no fields found, stop execution
        if (empty($fields)) {
            $this->error("❌ ERROR: No fields found for module '{$module}'!");
            $this->error("Please ensure the Model has a \$fillable array defined.");
            $this->error("");
            $this->error("Script execution stopped. Please fix the issue and try again.");
            return 1; // Exit with error code
        }
        
        $this->info("✓ Found " . count($fields) . " fields from Model's \$fillable");
        
        // Generate ALL views including form
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
        $foundFields = [];
        
        // Pattern 1: Form::label('field_name')
        preg_match_all('/Form::label\([\'"](\w+)[\'"]\)/', $content, $matches1);
        if (!empty($matches1[1])) {
            foreach ($matches1[1] as $field) {
                $foundFields[$field] = true;
            }
        }
        
        // Pattern 2: <x-crud.form-group name="field_name"
        preg_match_all('/<x-crud\.form-group[^>]+name=[\'"](\w+)[\'"]/', $content, $matches2);
        if (!empty($matches2[1])) {
            foreach ($matches2[1] as $field) {
                $foundFields[$field] = true;
            }
        }
        
        // Pattern 3: name="field_name"
        preg_match_all('/name=[\'"](\w+)[\'"]/', $content, $matches3);
        if (!empty($matches3[1])) {
            foreach ($matches3[1] as $field) {
                if (!in_array($field, ['_token', '_method'])) {
                    $foundFields[$field] = true;
                }
            }
        }
        
        // Convert to array
        foreach (array_keys($foundFields) as $field) {
            $fields[] = [
                'name' => $field,
                'label' => Str::title(str_replace('_', ' ', $field)),
                'type' => $this->guessFieldType($field),
            ];
        }
        
        return $fields;
    }
    
    protected function extractFieldsFromModel($module)
    {
        $fields = [];
        
        // Convert module name to Model class name
        // e.g., 'customer-operation-daily-summary' -> 'CustomerOperationDailySummary'
        $modelName = Str::studly(Str::singular($module));
        $modelClass = "App\\Models\\{$modelName}";
        
        // Check if model exists
        if (!class_exists($modelClass)) {
            $this->warn("Model class not found: {$modelClass}");
            return [];
        }
        
        // Try to get $fillable from the model
        try {
            $model = new $modelClass();
            $fillable = $model->getFillable();
            
            if (empty($fillable)) {
                $this->warn("Model {$modelClass} has no \$fillable attributes defined");
                return [];
            }
            
            $this->info("✓ Found " . count($fillable) . " fields in Model's \$fillable array");
            
            foreach ($fillable as $fieldName) {
                // Skip system fields
                if (in_array($fieldName, ['created_at', 'updated_at', 'deleted_at'])) {
                    continue;
                }
                
                $fields[] = [
                    'name' => $fieldName,
                    'label' => Str::title(str_replace('_', ' ', $fieldName)),
                    'type' => $this->guessFieldType($fieldName),
                ];
            }
            
        } catch (\Exception $e) {
            $this->error("Error instantiating model {$modelClass}: " . $e->getMessage());
            return [];
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
        if (str_contains($fieldName, 'description') || str_contains($fieldName, 'message') || str_contains($fieldName, 'note')) return 'textarea';
        if (str_contains($fieldName, 'is_') || str_contains($fieldName, 'has_')) return 'checkbox';
        return 'text';
    }
    
    protected function generateIndex($module, $fields, $force)
    {
        $variableName = Str::camel(Str::plural($module));
        $modelVar = Str::camel(Str::singular($module));
        $routePrefix = Str::slug(Str::plural($module));
        $title = Str::title(str_replace('-', ' ', $module));
        
        // Build beautiful detail display with ALL fields in a grid layout
        $detailParts = [];
        foreach ($fields as $field) {
            if ($field['name'] !== 'id' && !str_contains($field['name'], 'password')) {
                $label = $field['label'];
                $detailParts[] = "                                        <div class=\"mb-2\">
                                            <span class=\"text-xs font-semibold text-gray-600 dark:text-gray-400 uppercase tracking-wide\">{$label}:</span>
                                            <span class=\"ml-2 text-sm text-gray-900 dark:text-gray-100\">{{ \${$modelVar}->{$field['name']} ?? '-' }}</span>
                                        </div>";
            }
        }
        $detailContent = implode("\n", $detailParts);
        
        if (empty($detailParts)) {
            $detailContent = "                                        <div class=\"text-sm text-gray-900 dark:text-gray-100\">{{ \${$modelVar}->name ?? \${$modelVar}->id ?? 'Item #' . \$loop->iteration }}</div>";
        }
        
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
        
        <div class=\"overflow-x-auto rounded-lg shadow\">
            <table class=\"w-full text-sm text-left border-collapse bg-white dark:bg-gray-800\">
                <thead class=\"bg-gradient-to-r from-gray-100 to-gray-50 dark:from-gray-700 dark:to-gray-800 border-b-2 border-gray-300 dark:border-gray-600\">
                    <tr>
                        <th class=\"px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-20 text-center\">ID</th>
                        <th class=\"px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider\">Detail</th>
                        <th class=\"px-6 py-4 text-xs font-bold text-gray-700 dark:text-gray-300 uppercase tracking-wider w-64 text-center\">Actions</th>
                    </tr>
                </thead>
                <tbody class=\"divide-y divide-gray-200 dark:divide-gray-700\">
                    @forelse(\${$variableName} as \$index => \${$modelVar})
                        <tr class=\"hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent dark:hover:from-gray-700/50 dark:hover:to-transparent transition-all duration-200\">
                            <td class=\"px-6 py-4 text-center\">
                                <span class=\"inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 text-sm font-semibold\">
                                    {{ \$i + \$index + 1 }}
                                </span>
                            </td>
                            <td class=\"px-6 py-4\">
                                <div class=\"space-y-1\">
{$detailContent}
                                </div>
                            </td>
                            <td class=\"px-6 py-4\">
                                <x-crud.action-buttons :model=\"\${$modelVar}\" resource=\"{$routePrefix}\" />
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan=\"3\" class=\"px-6 py-8 text-center text-gray-500 dark:text-gray-400\">
                                <div class=\"flex flex-col items-center gap-2\">
                                    <i class=\"fa fa-inbox text-4xl text-gray-300 dark:text-gray-600\"></i>
                                    <p>No {$title} available</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.card>
    <div class=\"mt-6\">{{ \${$variableName}->links() }}</div>
</div>
@endsection";
        
        $this->writeFile("views/{$module}/index.blade.php", $content, $force);
    }
    
    protected function generateCreate($module, $force)
    {
        $routePrefix = Str::slug(Str::plural($module));
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
        $routePrefix = Str::slug(Str::plural($module));
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
        $routePrefix = Str::slug(Str::plural($module));
        $title = Str::title(str_replace('-', ' ', $module));
        
        $formGroups = [];
        foreach ($fields as $field) {
            if ($field['name'] === 'id') continue;
            
            // Special handling for password field
            if (str_contains($field['name'], 'password')) {
                $formGroups[] = "{{-- Password field: Leave blank to keep existing password when editing --}}";
                $formGroups[] = "<x-crud.form-group name=\"{$field['name']}\" label=\"{$field['label']}\" type=\"{$field['type']}\" :value=\"''\" placeholder=\"Enter {$field['label']} (optional for edit)\" />";
            } else {
                $formGroups[] = "<x-crud.form-group name=\"{$field['name']}\" label=\"{$field['label']}\" type=\"{$field['type']}\" :value=\"\${$modelVar}->{$field['name']} ?? old('{$field['name']}')\" placeholder=\"Enter {$field['label']}\" />";
            }
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
        $routePrefix = Str::slug(Str::plural($module));
        $title = Str::title(str_replace('-', ' ', $module));
        
        $detailFields = [];
        foreach ($fields as $field) {
            // Skip ID and password fields in show view
            if ($field['name'] === 'id' || str_contains($field['name'], 'password')) continue;
            
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

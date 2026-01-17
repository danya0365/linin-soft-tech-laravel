#!/bin/bash

# Script to automatically fix jQuery defer issues in Blade files
# แก้ไข inline jQuery scripts ให้ใช้ @push('scripts')

echo "🔧 Starting jQuery defer fix script..."
echo ""

# List of files to fix
FILES=(
    "resources/views/truck/create-note.blade.php"
    "resources/views/washing-machine/create-note.blade.php"
    "resources/views/dryer-machine/create-note.blade.php"
    "resources/views/worker/energy-resources/logs.blade.php"
    "resources/views/worker/energy-resources/summaries/electricity.blade.php"
    "resources/views/worker/energy-resources/summaries/water.blade.php"
    "resources/views/worker/energy-resources/summaries/gas.blade.php"
    "resources/views/worker/energy-resources/summaries/petrol.blade.php"
    "resources/views/worker/energy-resources/summaries/fuel-oil.blade.php"
    "resources/views/worker/energy-resources/summaries/biomass.blade.php"
    "resources/views/worker/stocks/inventory-logs-by-id.blade.php"
    "resources/views/worker/stocks/inventories-logs.blade.php"
    "resources/views/worker/stocks/show-inventory-by-group.blade.php"
    "resources/views/worker/operations/dry/select-linen-product.blade.php"
    "resources/views/worker/operations/collect/select-linen-product.blade.php"
    "resources/views/worker/operations/deliver/select-collect-operation.blade.php"
)

FIXED_COUNT=0
SKIP_COUNT=0

for FILE in "${FILES[@]}"; do
    if [ ! -f "$FILE" ]; then
        echo "⚠️  File not found: $FILE"
        ((SKIP_COUNT++))
        continue
    fi
    
    # Check if file already has @push('scripts')
    if grep -q "@push('scripts')" "$FILE"; then
        echo "✅ Already fixed: $FILE"
        ((SKIP_COUNT++))
        continue
    fi
    
    # Check if file has <script> tags
    if ! grep -q "<script>" "$FILE"; then
        echo "ℹ️  No scripts found: $FILE"
        ((SKIP_COUNT++))
        continue
    fi
    
    echo "🔨 Fixing: $FILE"
    
    # Create backup
    cp "$FILE" "$FILE.backup"
    
    # Create temp file with fixes
    # This is a simplified fix - for complex cases, manual fix is better
    python3 << 'PYTHON_SCRIPT'
import sys
import re

file_path = sys.argv[1]

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Find the last @endsection
if '@endsection' in content:
    # Find script tags
    script_pattern = r'<script>(.*?)</script>'
    scripts = re.findall(script_pattern, content, re.DOTALL)
    
    if scripts:
        # Remove script tags from content
        for script in scripts:
            content = content.replace(f'<script>{script}</script>', '', 1)
        
        # Create new script section with @push
        new_script = "\n@push('scripts')\n<script>\ndocument.addEventListener('DOMContentLoaded', function() {\n    if (typeof jQuery === 'undefined') return;\n    \n"
        
        for script in scripts:
            new_script += script + "\n"
        
        new_script += "});\n</script>\n@endpush\n"
        
        # Insert before @endsection
        content = content.replace('@endsection', new_script + '@endsection', 1)
        
        with open(file_path, 'w', encoding='utf-8') as f:
            f.write(content)
        
        print(f"✅ Fixed successfully")
    else:
        print(f"ℹ️  No inline scripts to fix")

PYTHON_SCRIPT
    
    python3 - "$FILE" 2>/dev/null
    
    if [ $? -eq 0 ]; then
        ((FIXED_COUNT++))
        rm "$FILE.backup"
    else
        echo "❌ Error fixing file, restoring backup"
        mv "$FILE.backup" "$FILE"
    fi
    
    echo""
done

echo "📊 Summary:"
echo "  - Fixed: $FIXED_COUNT files"
echo "  - Skipped: $SKIP_COUNT files"
echo "  - Total processed: $((FIXED_COUNT + SKIP_COUNT)) files"
echo ""
echo "✅ Done! Please review the changes before committing."
echo ""
echo "🔍 To verify, run:"
echo "   grep -r '@push' resources/views/ | wc -l"

#!/bin/bash

# Quick fix for energy summary files with same pattern

FILES=(
    "resources/views/worker/energy-resources/summaries/biomass.blade.php"
    "resources/views/worker/energy-resources/summaries/electricity.blade.php"
    "resources/views/worker/energy-resources/summaries/fuel-oil.blade.php"
    "resources/views/worker/energy-resources/summaries/gas.blade.php"
    "resources/views/worker/energy-resources/summaries/petrol.blade.php"
    "resources/views/worker/energy-resources/summaries/water.blade.php"
)

for file in "${FILES[@]}"; do
    echo "Processing: $file"
    
    # Check if already fixed
    if grep -q "@push('scripts')" "$file"; then
        echo "  ✅ Already fixed"
        continue
    fi
    
    # Create backup
    cp "$file" "$file.bak2"
    
    # Use perl for better multiline support
    perl -i -0777 -pe 's/<script>\s*\$\(function\{\s*(.*?)\s*\}\)\s*<\/script>\s*\@endsection/@push('\''scripts'\'')\n<script>\ndocument.addEventListener('\''DOMContentLoaded'\'', function() {\n    if (typeof jQuery === '\''undefined'\'') return;\n    \$(function{\n        $1\n    })\n});\n<\/script>\n@endpush\n@endsection/gs' "$file"
    
    echo "  ✅ Fixed!"
done

echo ""
echo "Done! Backups saved with .bak2 extension"

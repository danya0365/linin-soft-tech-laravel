#!/usr/bin/env python3
"""
Auto-fix jQuery defer issues in Blade files
Wraps inline jQuery scripts with @push('scripts') and DOMContentLoaded
"""

import re
import os
import sys

FILES_TO_FIX = [
    "resources/views/supervisor/employees/employee-summary.blade.php",
    "resources/views/worker/employees/employee-summary.blade.php",
    "resources/views/worker/energy-resources/logs.blade.php",
    "resources/views/worker/energy-resources/summaries/biomass.blade.php",
    "resources/views/worker/energy-resources/summaries/electricity.blade.php",
    "resources/views/worker/energy-resources/summaries/fuel-oil.blade.php",
    "resources/views/worker/energy-resources/summaries/gas.blade.php",
    "resources/views/worker/energy-resources/summaries/petrol.blade.php",
    "resources/views/worker/energy-resources/summaries/water.blade.php",
    "resources/views/worker/stocks/inventory-logs-by-id.blade.php",
    "resources/views/worker/stocks/inventories-logs.blade.php",
    "resources/views/worker/stocks/show-inventory-by-group.blade.php",
    "resources/views/worker/operations/dry/select-linen-product.blade.php",
    "resources/views/worker/operations/collect/select-linen-product.blade.php",
    "resources/views/worker/operations/deliver/select-collect-operation.blade.php",
]

def fix_blade_file(filepath):
    """Fix a single Blade file"""
    
    if not os.path.exists(filepath):
        print(f"⚠️  File not found: {filepath}")
        return False
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Check if already fixed
    if "@push('scripts')" in content:
        print(f"✅ Already fixed: {filepath}")
        return False
    
    # Check if has script tags
    if '<script>' not in content or '$(function' not in content:
        print(f"ℹ️  No jQuery scripts: {filepath}")
        return False
    
    # Find script block before @endsection
    pattern = r'(<script>.*?\$\(function.*?</script>)\s*@endsection'
    match = re.search(pattern, content, re.DOTALL)
    
    if not match:
        print(f"⚠️  Pattern not found: {filepath}")
        return False
    
    script_block = match.group(1)
    
    # Extract script content (without tags)
    script_content = re.search(r'<script>(.*?)</script>', script_block, re.DOTALL)
    if not script_content:
        return False
    
    inner_script = script_content.group(1)
    
    # Create new wrapped script
    new_script = f"""
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {{
    if (typeof jQuery === 'undefined') return;
    {inner_script}
}});
</script>
@endpush
@endsection"""
    
    # Replace
    new_content = re.sub(pattern, new_script, content, flags=re.DOTALL)
    
    # Create backup
    backup_path = filepath + '.bak'
    with open(backup_path, 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Write new content
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print(f"✅ Fixed: {filepath}")
    return True

def main():
    print("🔧 Starting auto-fix for jQuery defer issues...")
    print()
    
    fixed_count = 0
    skip_count = 0
    
    for filepath in FILES_TO_FIX:
        if fix_blade_file(filepath):
            fixed_count += 1
        else:
            skip_count += 1
        print()
    
    print("=" * 60)
    print(f"📊 Summary:")
    print(f"  - Fixed: {fixed_count} files")
    print(f"  - Skipped: {skip_count} files")
    print(f"  - Total: {fixed_count + skip_count} files")
    print()
    print("✅ Done! Backup files created with .bak extension")
    print()

if __name__ == "__main__":
    main()

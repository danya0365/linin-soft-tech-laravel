#!/usr/bin/env python3
"""
Batch fix jQuery defer issues - Final version
"""

import re
import os

# All remaining files to fix
FILES_TO_FIX = [
    "resources/views/worker/operations/wash/employee-summary.blade.php",
    "resources/views/worker/operations/dry/employee-summary.blade.php",
    "resources/views/worker/operations/iron/employee-summary.blade.php",
    "resources/views/worker/operations/packing/employee-summary.blade.php",
    "resources/views/worker/operations/collect/employee-summary.blade.php",
    "resources/views/worker/operations/deliver/employee-summary.blade.php",
    "resources/views/worker/operations/wash/select-linen-product.blade.php",
    "resources/views/worker/operations/dry/select-linen-product.blade.php",
    "resources/views/worker/operations/iron/select-linen-product.blade.php",
    "resources/views/worker/operations/packing/select-linen-product.blade.php",
    "resources/views/worker/operations/collect/select-linen-product.blade.php",
    "resources/views/worker/operations/deliver/select-collect-operation.blade.php",
]

def fix_file(filepath):
    """Fix a single file - simpler version"""
    
    if not os.path.exists(filepath):
        print(f"⚠️  Not found: {filepath}")
        return False
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Already fixed?
    if "@push('scripts')" in content:
        print(f"✅ Already fixed: {filepath}")
        return False
    
    # No jQuery?
    if '<script>' not in content or '$(function' not in content:
        print(f"ℹ️  No jQuery: {filepath}")
        return False
    
    # Find and wrap the last script block before @endsection
    # Pattern: find </div>\n</div>\n<script>.....</script>\n@endsection
    pattern = r'(\s*</div>\s*</div>\s*)<script>(.*?)</script>\s*@endsection'
    
    def replace_func(match):
        closing_divs = match.group(1)
        script_content = match.group(2)
        
        # Wrap it
        return f'''{closing_divs}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {{
    if (typeof jQuery === 'undefined') return;
    {script_content}
}});
</script>
@endpush
@endsection'''
    
    new_content = re.sub(pattern, replace_func, content, flags=re.DOTALL)
    
    if new_content == content:
        print(f"⚠️  Pattern not matched: {filepath}")
        return False
    
    # Backup
    with open(filepath + '.bak3', 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Write
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print(f"✅ Fixed: {filepath}")
    return True

def main():
    print("🔧 Batch fixing jQuery defer issues...")
    print()
    
    fixed = 0
    skipped = 0
    
    for filepath in FILES_TO_FIX:
        if fix_file(filepath):
            fixed += 1
        else:
            skipped += 1
    
    print()
    print("=" * 60)
    print(f"📊 Summary:")
    print(f"  ✅ Fixed: {fixed} files")
    print(f"  ⏭️  Skipped: {skipped} files")
    print(f"  📁 Total: {fixed + skipped} files")
    print()
    print("✅ Done!")

if __name__ == "__main__":
    main()

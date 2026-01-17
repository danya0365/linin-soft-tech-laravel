#!/usr/bin/env python3
"""
Fix remaining employee-summary files with same pattern
"""

import re
import os

FILES = [
    "resources/views/worker/operations/collect/employee-summary.blade.php",
    "resources/views/worker/operations/deliver/employee-summary.blade.php",
    "resources/views/worker/operations/dry/employee-summary.blade.php",
    "resources/views/worker/operations/iron/employee-summary.blade.php",
    "resources/views/worker/operations/packing/employee-summary.blade.php",
]

def fix_file(filepath):
    if not os.path.exists(filepath):
        print(f"⚠️  Not found: {filepath}")
        return False
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if "@push('scripts')" in content:
        print(f"✅ Already fixed: {filepath}")
        return False
    
    # Pattern for employee summary files
    # Match: </div>\n</div>\n<script type="text/javascript">...(jQuery code)...</script>\n\n@endsection
    pattern = r'(</div>\s*</div>\s*)<script type="text/javascript">\s*\$\(function\(\)\{(.*?)\}\)\s*</script>\s*@endsection'
    
    def replace_func(match):
        closing_divs = match.group(1)
        script_content = match.group(2)
        
        return f'''{closing_divs}

@push('scripts')
<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function() {{
    if (typeof jQuery === 'undefined') return;
    $(function(){{
{script_content}    }})
}});
</script>
@endpush

@endsection'''
    
    new_content = re.sub(pattern, replace_func, content, flags=re.DOTALL)
    
    if new_content == content:
        print(f"⚠️  Pattern not matched: {filepath}")
        return False
    
    # Backup
    with open(filepath + '.bak4', 'w', encoding='utf-8') as f:
        f.write(content)
    
    # Write
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(new_content)
    
    print(f"✅ Fixed: {filepath}")
    return True

def main():
    print("🔧 Fixing employee-summary files...")
    print()
    
    fixed = 0
    for filepath in FILES:
        if fix_file(filepath):
            fixed += 1
    
    print()
    print(f"📊 Fixed: {fixed}/{len(FILES)} files")
    print("✅ Done!")

if __name__ == "__main__":
    main()

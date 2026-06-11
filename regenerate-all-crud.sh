#!/bin/bash

# Regenerate all CRUD modules with empty/incomplete forms
# This script will regenerate views using the Model's $fillable array

echo "🔄 Regenerating CRUD views for all affected modules..."
echo "=========================================="

modules=(
    "linen-product"
    "operation"
    "note"
    "expense"
    "linen-type"
    "department-daily-cost-log"
    "employee-operation-log"
    "customer-group"
    "energy-resource-log"
    "income"
    "department"
    "truck"
    "inventory-group"
    "energy-resource"
    "washing-machine"
    "inventory-stock-log"
    "inventory"
    "employee-working-time"
    "login-history"
    "dryer-machine"
    "operation-log"
)

success_count=0
error_count=0
errors=()

for module in "${modules[@]}"; do
    echo ""
    echo "📝 Processing: $module"
    echo "----------------------------------------"
    
    if php artisan crud:generate-views "$module" --force; then
        ((success_count++))
        echo "✅ Success: $module"
    else
        ((error_count++))
        errors+=("$module")
        echo "❌ Failed: $module"
    fi
done

echo ""
echo "=========================================="
echo "📊 Summary:"
echo "  ✅ Successful: $success_count"
echo "  ❌ Failed: $error_count"

if [ $error_count -gt 0 ]; then
    echo ""
    echo "⚠️  Modules with errors:"
    for err_module in "${errors[@]}"; do
        echo "  - $err_module"
    done
    exit 1
else
    echo ""
    echo "🎉 All modules regenerated successfully!"
    exit 0
fi

#!/bin/bash

# CRUD Views Batch Redesign Script
# This script analyzes each CRUD folder and generates field information

echo "=== CRUD Module Field Analyzer ==="
echo ""

VIEWS_DIR="/Users/marosdeeuma/linin-soft-tech/resources/views"

# List of modules to process (excluding already completed ones)
MODULES=(
    "customer-group"
    "customer-operation-daily-summary"
    "employee"
    "employee-working-time"
    "employee-operation-log"
    "department"
    "department-daily-cost-log"
    "inventory"
    "inventory-group"
    "inventory-stock-log"
    "energy-resource"
    "energy-resource-log"
    "linen-product"
    "linen-type"
    "operation"
    "operation-log"
    "note"
    "login-history"
    "expense"
    "income"
    "user"
)

for module in "${MODULES[@]}"; do
    echo "=== Analyzing: $module ==="
    
    # Check if form.blade.php exists
    FORM_FILE="$VIEWS_DIR/$module/form.blade.php"
    if [ -f "$FORM_FILE" ]; then
        echo "Form fields found:"
        # Extract field names from Form::label or form-group
        grep -E "(Form::label|name=)" "$FORM_FILE" | head -20
        echo ""
    fi
    
    # Check index for table columns
    INDEX_FILE="$VIEWS_DIR/$module/index.blade.php"
    if [ -f "$INDEX_FILE" ]; then
        echo "Table columns:"
        grep -E "<th>" "$INDEX_FILE" | head -15
        echo ""
    fi
    
    echo "---"
    echo ""
done

echo "Analysis complete!"

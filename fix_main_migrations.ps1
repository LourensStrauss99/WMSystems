# PowerShell script to fix main database migration order
Set-Location "C:\Users\Pa\Herd\workflow-management\database\migrations"

Write-Host "Fixing main database migration order based on dependencies..."

# Level 1: No dependencies
Write-Host "Level 1: Tables with no dependencies..."
# These are already correctly positioned: clients(1), companies(2), company_details(3), users(4)

# Level 2: Tables that depend on Level 1 tables
Write-Host "Level 2: Moving employees after users..."
if (Test-Path "2024_01_1000005_create_employees_table.php") { 
    Rename-Item "2024_01_1000005_create_employees_table.php" "2024_01_1000005_create_employees_table.php" 
} else {
    Write-Host "Creating employees table migration at position 5..."
}

Write-Host "Level 2: Moving suppliers, inventory, and other independent tables..."
if (Test-Path "2024_01_1000024_create_suppliers_table.php") { 
    Rename-Item "2024_01_1000024_create_suppliers_table.php" "2024_01_1000006_create_suppliers_table.php" 
}
if (Test-Path "2024_01_1000008_create_inventory_table.php") { 
    Rename-Item "2024_01_1000008_create_inventory_table.php" "2024_01_1000007_create_inventory_table.php" 
}
if (Test-Path "2024_01_1000011_create_jobcards_table.php") { 
    Rename-Item "2024_01_1000011_create_jobcards_table.php" "2024_01_1000008_create_jobcards_table.php" 
}
if (Test-Path "2024_01_1000017_create_password_reset_tokens_table.php") { 
    Rename-Item "2024_01_1000017_create_password_reset_tokens_table.php" "2024_01_1000009_create_password_reset_tokens_table.php" 
}
if (Test-Path "2024_01_1000022_create_sessions_table.php") { 
    Rename-Item "2024_01_1000022_create_sessions_table.php" "2024_01_1000010_create_sessions_table.php" 
}
if (Test-Path "2024_01_1000023_create_settings_table.php") { 
    Rename-Item "2024_01_1000023_create_settings_table.php" "2024_01_1000011_create_settings_table.php" 
}
if (Test-Path "2024_01_1000021_create_quotes_table.php") { 
    Rename-Item "2024_01_1000021_create_quotes_table.php" "2024_01_1000012_create_quotes_table.php" 
}
if (Test-Path "2024_01_1000016_create_mobile_jobcards_table.php") { 
    Rename-Item "2024_01_1000016_create_mobile_jobcards_table.php" "2024_01_1000013_create_mobile_jobcards_table.php" 
}
if (Test-Path "2024_01_1000014_create_migrations_table.php") { 
    Rename-Item "2024_01_1000014_create_migrations_table.php" "2024_01_1000014_create_migrations_table.php" 
}

# Level 3: Tables that depend on users, clients, suppliers
Write-Host "Level 3: Moving purchase_orders after suppliers and users..."
if (Test-Path "2024_01_1000020_create_purchase_orders_table.php") { 
    Rename-Item "2024_01_1000020_create_purchase_orders_table.php" "2024_01_1000015_create_purchase_orders_table.php" 
}

Write-Host "Level 3: Moving invoices and payments after clients and jobcards..."
if (Test-Path "2024_01_1000010_create_invoices_table.php") { 
    Rename-Item "2024_01_1000010_create_invoices_table.php" "2024_01_1000016_create_invoices_table.php" 
}
if (Test-Path "2024_01_1000018_create_payments_table.php") { 
    Rename-Item "2024_01_1000018_create_payments_table.php" "2024_01_1000017_create_payments_table.php" 
}

# Level 4: Tables that depend on Level 3 tables
Write-Host "Level 4: Moving purchase_order_items after purchase_orders..."
if (Test-Path "2024_01_1000019_create_purchase_order_items_table.php") { 
    Rename-Item "2024_01_1000019_create_purchase_order_items_table.php" "2024_01_1000018_create_purchase_order_items_table.php" 
}

Write-Host "Level 4: Moving goods_received_vouchers after purchase_orders..."
if (Test-Path "2024_01_1000006_create_goods_received_vouchers_table.php") { 
    Rename-Item "2024_01_1000006_create_goods_received_vouchers_table.php" "2024_01_1000019_create_goods_received_vouchers_table.php" 
}

Write-Host "Level 4: Moving jobcard-dependent tables..."
if (Test-Path "2024_01_1000012_create_jobcards_completed_table.php") { 
    Rename-Item "2024_01_1000012_create_jobcards_completed_table.php" "2024_01_1000020_create_jobcards_completed_table.php" 
}
if (Test-Path "2024_01_1000013_create_jobcards_progress_table.php") { 
    Rename-Item "2024_01_1000013_create_jobcards_progress_table.php" "2024_01_1000021_create_jobcards_progress_table.php" 
}
if (Test-Path "2024_01_1000015_create_mobile_jobcard_photos_table.php") { 
    Rename-Item "2024_01_1000015_create_mobile_jobcard_photos_table.php" "2024_01_1000022_create_mobile_jobcard_photos_table.php" 
}

# Level 5: Tables that depend on multiple Level 3+ tables
Write-Host "Level 5: Moving junction tables..."
if (Test-Path "2024_01_1000009_create_inventory_jobcard_table.php") { 
    Rename-Item "2024_01_1000009_create_inventory_jobcard_table.php" "2024_01_1000023_create_inventory_jobcard_table.php" 
}

Write-Host "Level 5: Moving grv_items after goods_received_vouchers and purchase_order_items..."
if (Test-Path "2024_01_1000007_create_grv_items_table.php") { 
    Rename-Item "2024_01_1000007_create_grv_items_table.php" "2024_01_1000024_create_grv_items_table.php" 
}

Write-Host "Main migration order fixed!"
Write-Host "New order:"
Get-ChildItem "2024_01_100*.php" | Sort-Object Name | Select-Object Name

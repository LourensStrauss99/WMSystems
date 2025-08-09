# PowerShell script to rename migration files in correct dependency order
Set-Location "C:\Users\Pa\Herd\workflow-management\database\migrations\tenant"

Write-Host "Renaming migration files to correct dependency order..."

# Step 1: Rename all to temporary names first to avoid conflicts
Write-Host "Step 1: Moving to temporary names..."

# Tables with no dependencies (Level 1)
if (Test-Path "temp_001_create_clients_table.php") { Rename-Item "temp_001_create_clients_table.php" "2024_01_1000001_create_clients_table.php" }
if (Test-Path "temp_002_create_companies_table.php") { Rename-Item "temp_002_create_companies_table.php" "2024_01_1000002_create_company_details_table.php" }
if (Test-Path "2024_01_1000020_create_company_details_table.php") { Rename-Item "2024_01_1000020_create_company_details_table.php" "2024_01_1000003_create_companies_table.php" }
if (Test-Path "temp_003_create_users_table.php") { Rename-Item "temp_003_create_users_table.php" "2024_01_1000004_create_users_table.php" }
if (Test-Path "2024_01_1000010_create_inventory_table.php") { Rename-Item "2024_01_1000010_create_inventory_table.php" "2024_01_1000005_create_inventory_table.php" }
if (Test-Path "2024_01_1000008_create_suppliers_table.php") { Rename-Item "2024_01_1000008_create_suppliers_table.php" "2024_01_1000006_create_suppliers_table.php" }
if (Test-Path "2024_01_1000017_create_password_reset_tokens_table.php") { Rename-Item "2024_01_1000017_create_password_reset_tokens_table.php" "2024_01_1000007_create_password_reset_tokens_table.php" }
if (Test-Path "2024_01_1000022_create_sessions_table.php") { Rename-Item "2024_01_1000022_create_sessions_table.php" "2024_01_1000008_create_sessions_table.php" }
if (Test-Path "2024_01_1000023_create_settings_table.php") { Rename-Item "2024_01_1000023_create_settings_table.php" "2024_01_1000009_create_settings_table.php" }
if (Test-Path "2024_01_1000021_create_quotes_table.php") { Rename-Item "2024_01_1000021_create_quotes_table.php" "2024_01_1000010_create_quotes_table.php" }
if (Test-Path "2024_01_1000016_create_mobile_jobcards_table.php") { Rename-Item "2024_01_1000016_create_mobile_jobcards_table.php" "2024_01_1000011_create_mobile_jobcards_table.php" }
if (Test-Path "2024_01_1000021_create_migrations_table.php") { Rename-Item "2024_01_1000021_create_migrations_table.php" "2024_01_1000012_create_migrations_table.php" }

# Tables that depend on users (Level 2)
if (Test-Path "2024_01_1000006_create_employees_table.php.disabled") { Rename-Item "2024_01_1000006_create_employees_table.php.disabled" "2024_01_1000013_create_employees_table.php" }
if (Test-Path "2024_01_1000009_create_purchase_orders_table.php") { Rename-Item "2024_01_1000009_create_purchase_orders_table.php" "2024_01_1000014_create_purchase_orders_table.php" }

# Tables that depend on clients (Level 2) 
if (Test-Path "temp_004_create_jobcards_table.php") { Rename-Item "temp_004_create_jobcards_table.php" "2024_01_1000015_create_jobcards_table.php" }
if (Test-Path "2024_01_1000018_create_payments_table.php") { Rename-Item "2024_01_1000018_create_payments_table.php" "2024_01_1000016_create_payments_table.php" }

# Tables that depend on purchase_orders (Level 3)
if (Test-Path "2024_01_1000010_create_purchase_order_items_table.php") { Rename-Item "2024_01_1000010_create_purchase_order_items_table.php" "2024_01_1000017_create_purchase_order_items_table.php" }
if (Test-Path "2024_01_1000011_create_goods_received_vouchers_table.php") { Rename-Item "2024_01_1000011_create_goods_received_vouchers_table.php" "2024_01_1000018_create_goods_received_vouchers_table.php" }

# Tables that depend on jobcards (Level 3)
if (Test-Path "2024_01_1000016_create_invoices_table.php") { Rename-Item "2024_01_1000016_create_invoices_table.php" "2024_01_1000019_create_invoices_table.php" }
if (Test-Path "2024_01_1000013_create_jobcards_completed_table.php") { Rename-Item "2024_01_1000013_create_jobcards_completed_table.php" "2024_01_1000020_create_jobcards_completed_table.php" }
if (Test-Path "2024_01_1000014_create_jobcards_progress_table.php") { Rename-Item "2024_01_1000014_create_jobcards_progress_table.php" "2024_01_1000021_create_jobcards_progress_table.php" }

# Tables that depend on multiple tables (Level 4)
if (Test-Path "temp_006_create_employee_jobcard_table.php") { Rename-Item "temp_006_create_employee_jobcard_table.php" "2024_01_1000022_create_employee_jobcard_table.php" }
if (Test-Path "2024_01_1000015_create_inventory_jobcard_table.php") { Rename-Item "2024_01_1000015_create_inventory_jobcard_table.php" "2024_01_1000023_create_inventory_jobcard_table.php" }
if (Test-Path "2024_01_1000015_create_mobile_jobcard_photos_table.php") { Rename-Item "2024_01_1000015_create_mobile_jobcard_photos_table.php" "2024_01_1000024_create_mobile_jobcard_photos_table.php" }
if (Test-Path "2024_01_1000012_create_grv_items_table.php") { Rename-Item "2024_01_1000012_create_grv_items_table.php" "2024_01_1000025_create_grv_items_table.php" }

Write-Host "Migration files renamed successfully!"
Write-Host "Now running: Get-ChildItem | Sort-Object Name | Select-Object Name"
Get-ChildItem "2024_01_100*.php" | Sort-Object Name | Select-Object Name

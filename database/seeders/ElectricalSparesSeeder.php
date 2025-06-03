<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ElectricalSparesSeeder extends Seeder
{
    public function run()
    {
        $items = [
            
            ['id' => 6, 'name' => 'Double Pole Breaker 20A', 'short_description' => 'EL DP Breaker', 'buying_price' => 120.00, 'selling_price' => 165.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-002', 'stock_level' => 10, 'min_level' => 2],
            ['id' => 7, 'name' => 'Earth Leakage 63A', 'short_description' => 'EL Earth Leak', 'buying_price' => 350.00, 'selling_price' => 475.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-003', 'stock_level' => 7, 'min_level' => 2],
            ['id' => 8, 'name' => 'Light Switch One Gang', 'short_description' => 'EL 1-Gang Switch', 'buying_price' => 25.00, 'selling_price' => 40.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-004', 'stock_level' => 30, 'min_level' => 2],
            ['id' => 9, 'name' => 'Light Switch Two Gang', 'short_description' => 'EL 2-Gang Switch', 'buying_price' => 35.00, 'selling_price' => 55.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-005', 'stock_level' => 25, 'min_level' => 2],
            ['id' => 10, 'name' => 'Wall Socket 16A', 'short_description' => 'EL 16A Socket', 'buying_price' => 45.00, 'selling_price' => 70.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-006', 'stock_level' => 28, 'min_level' => 2],
            ['id' => 11, 'name' => 'Extension Cord 5M', 'short_description' => 'EL 5M Cord', 'buying_price' => 60.00, 'selling_price' => 95.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-007', 'stock_level' => 12, 'min_level' => 2],
            ['id' => 12, 'name' => 'Junction Box Small', 'short_description' => 'EL Small J-Box', 'buying_price' => 15.00, 'selling_price' => 25.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-008', 'stock_level' => 40, 'min_level' => 2],
            ['id' => 13, 'name' => 'LED Downlight 9W', 'short_description' => 'EL 9W LED DL', 'buying_price' => 70.00, 'selling_price' => 110.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-009', 'stock_level' => 18, 'min_level' => 2],
            ['id' => 14, 'name' => 'Ceiling Rose', 'short_description' => 'EL Ceiling Rose', 'buying_price' => 12.00, 'selling_price' => 20.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-010', 'stock_level' => 35, 'min_level' => 2],
            ['id' => 15, 'name' => 'Electrical Tape Black', 'short_description' => 'EL Black Tape', 'buying_price' => 8.00, 'selling_price' => 15.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-011', 'stock_level' => 50, 'min_level' => 2],
            ['id' => 16, 'name' => 'Cable Ties Assorted', 'short_description' => 'EL Cable Ties', 'buying_price' => 20.00, 'selling_price' => 30.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-012', 'stock_level' => 45, 'min_level' => 2],
            ['id' => 17, 'name' => 'Conduit Pipe 20mm 3M', 'short_description' => 'EL 20mm Conduit', 'buying_price' => 30.00, 'selling_price' => 50.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-013', 'stock_level' => 10, 'min_level' => 2],
            ['id' => 18, 'name' => 'Conduit Bends 20mm', 'short_description' => 'EL 20mm Bends', 'buying_price' => 5.00, 'selling_price' => 10.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-014', 'stock_level' => 60, 'min_level' => 2],
            ['id' => 19, 'name' => 'Wall Box Single', 'short_description' => 'EL Single Wall Box', 'buying_price' => 10.00, 'selling_price' => 18.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-015', 'stock_level' => 30, 'min_level' => 2],
            ['id' => 20, 'name' => 'Wall Box Double', 'short_description' => 'EL Double Wall Box', 'buying_price' => 15.00, 'selling_price' => 25.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-016', 'stock_level' => 25, 'min_level' => 2],
            ['id' => 21, 'name' => 'Distribution Board 12 Way', 'short_description' => 'EL 12W DB', 'buying_price' => 400.00, 'selling_price' => 550.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-017', 'stock_level' => 5, 'min_level' => 2],
            ['id' => 22, 'name' => 'Doorbell Kit Wireless', 'short_description' => 'EL Wireless Doorbell', 'buying_price' => 180.00, 'selling_price' => 250.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-018', 'stock_level' => 8, 'min_level' => 2],
            ['id' => 23, 'name' => 'Multimeter Digital', 'short_description' => 'EL Digital Multimeter', 'buying_price' => 250.00, 'selling_price' => 350.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-019', 'stock_level' => 3, 'min_level' => 2],
            ['id' => 24, 'name' => 'Cable Lug 6mm', 'short_description' => 'EL 6mm Cable Lug', 'buying_price' => 3.00, 'selling_price' => 7.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-020', 'stock_level' => 100, 'min_level' => 2],
            ['id' => 25, 'name' => 'Crimping Tool', 'short_description' => 'EL Crimper', 'buying_price' => 150.00, 'selling_price' => 210.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-021', 'stock_level' => 4, 'min_level' => 2],
            ['id' => 26, 'name' => 'PVC Insulation Tape', 'short_description' => 'EL PVC Tape', 'buying_price' => 10.00, 'selling_price' => 18.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-022', 'stock_level' => 50, 'min_level' => 2],
            ['id' => 27, 'name' => 'Heat Shrink Tubing Assortment', 'short_description' => 'EL Heat Shrink', 'buying_price' => 40.00, 'selling_price' => 65.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-023', 'stock_level' => 20, 'min_level' => 2],
            ['id' => 28, 'name' => 'Terminal Block Connector', 'short_description' => 'EL Terminal Block', 'buying_price' => 7.00, 'selling_price' => 12.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-024', 'stock_level' => 80, 'min_level' => 2],
            ['id' => 29, 'name' => 'Circuit Breaker Lockout Kit', 'short_description' => 'EL Breaker Lockout', 'buying_price' => 90.00, 'selling_price' => 130.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-025', 'stock_level' => 6, 'min_level' => 2],
            ['id' => 30, 'name' => 'Wire Stripper Tool', 'short_description' => 'EL Wire Stripper', 'buying_price' => 80.00, 'selling_price' => 115.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-026', 'stock_level' => 9, 'min_level' => 2],
            ['id' => 31, 'name' => 'Soldering Iron Kit', 'short_description' => 'EL Soldering Kit', 'buying_price' => 120.00, 'selling_price' => 170.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-027', 'stock_level' => 7, 'min_level' => 2],
            ['id' => 32, 'name' => 'Solder Wire', 'short_description' => 'EL Solder Wire', 'buying_price' => 25.00, 'selling_price' => 40.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-028', 'stock_level' => 30, 'min_level' => 2],
            ['id' => 33, 'name' => 'Electrical Conduit Saddle 20mm', 'short_description' => 'EL 20mm Conduit Saddle', 'buying_price' => 4.00, 'selling_price' => 8.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-029', 'stock_level' => 70, 'min_level' => 2],
            ['id' => 34, 'name' => 'Flush Mount Box Double', 'short_description' => 'EL Flush Double Box', 'buying_price' => 20.00, 'selling_price' => 35.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-030', 'stock_level' => 22, 'min_level' => 2],
            ['id' => 35, 'name' => 'Surface Mount Box Single', 'short_description' => 'EL Surface Single Box', 'buying_price' => 18.00, 'selling_price' => 30.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-031', 'stock_level' => 28, 'min_level' => 2],
            ['id' => 36, 'name' => 'Cable Gland M20', 'short_description' => 'EL M20 Cable Gland', 'buying_price' => 9.00, 'selling_price' => 16.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-032', 'stock_level' => 55, 'min_level' => 2],
            ['id' => 37, 'name' => 'Timer Switch Analog', 'short_description' => 'EL Analog Timer', 'buying_price' => 110.00, 'selling_price' => 150.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-033', 'stock_level' => 10, 'min_level' => 2],
            ['id' => 38, 'name' => 'Photoelectric Sensor', 'short_description' => 'EL Photo Sensor', 'buying_price' => 130.00, 'selling_price' => 180.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-034', 'stock_level' => 8, 'min_level' => 2],
            ['id' => 39, 'name' => 'Motion Sensor Passive Infrared', 'short_description' => 'EL PIR Sensor', 'buying_price' => 95.00, 'selling_price' => 135.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-035', 'stock_level' => 11, 'min_level' => 2],
            ['id' => 40, 'name' => 'Voltage Tester Pen', 'short_description' => 'EL Voltage Pen', 'buying_price' => 50.00, 'selling_price' => 80.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-036', 'stock_level' => 15, 'min_level' => 2],
            ['id' => 41, 'name' => 'Surge Protector Plug', 'short_description' => 'EL Surge Plug', 'buying_price' => 65.00, 'selling_price' => 95.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-037', 'stock_level' => 14, 'min_level' => 2],
            ['id' => 42, 'name' => 'Conduit Clips 20mm', 'short_description' => 'EL 20mm Clips', 'buying_price' => 3.50, 'selling_price' => 7.50, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-038', 'stock_level' => 80, 'min_level' => 2],
            ['id' => 43, 'name' => 'Din Rail', 'short_description' => 'EL Din Rail', 'buying_price' => 20.00, 'selling_price' => 35.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-039', 'stock_level' => 20, 'min_level' => 2],
            ['id' => 44, 'name' => 'Indicator Light Red', 'short_description' => 'EL Red Indicator', 'buying_price' => 6.00, 'selling_price' => 10.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-040', 'stock_level' => 60, 'min_level' => 2],
            ['id' => 45, 'name' => 'Push Button Switch NO', 'short_description' => 'EL NO Push Button', 'buying_price' => 12.00, 'selling_price' => 20.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-041', 'stock_level' => 40, 'min_level' => 2],
            ['id' => 46, 'name' => 'Emergency Stop Button', 'short_description' => 'EL E-Stop Button', 'buying_price' => 70.00, 'selling_price' => 100.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-042', 'stock_level' => 5, 'min_level' => 2],
            ['id' => 47, 'name' => 'Enclosure IP55', 'short_description' => 'EL IP55 Enclosure', 'buying_price' => 150.00, 'selling_price' => 210.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-043', 'stock_level' => 6, 'min_level' => 2],
            ['id' => 48, 'name' => 'Cable Cutter', 'short_description' => 'EL Cable Cutter', 'buying_price' => 100.00, 'selling_price' => 145.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-044', 'stock_level' => 8, 'min_level' => 2],
            ['id' => 49, 'name' => 'Insulated Screwdriver Set', 'short_description' => 'EL Insulated Screwdrivers', 'buying_price' => 180.00, 'selling_price' => 250.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-045', 'stock_level' => 4, 'min_level' => 2],
            ['id' => 50, 'name' => 'Earth Rod', 'short_description' => 'EL Earth Rod', 'buying_price' => 90.00, 'selling_price' => 130.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-046', 'stock_level' => 10, 'min_level' => 2],
            ['id' => 51, 'name' => 'Earth Clamp', 'short_description' => 'EL Earth Clamp', 'buying_price' => 25.00, 'selling_price' => 40.00, 'supplier' => 'Builders', 'goods_received_voucher' => 'V20250603-047', 'stock_level' => 30, 'min_level' => 2],
            ['id' => 52, 'name' => 'Cable Tray Section 1M', 'short_description' => 'EL 1M Cable Tray', 'buying_price' => 110.00, 'selling_price' => 150.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-048', 'stock_level' => 7, 'min_level' => 2],
            ['id' => 53, 'name' => 'Cable Tray Joiner', 'short_description' => 'EL Tray Joiner', 'buying_price' => 30.00, 'selling_price' => 50.00, 'supplier' => 'Voltex', 'goods_received_voucher' => 'V20250603-049', 'stock_level' => 20, 'min_level' => 2],
            ['id' => 54, 'name' => 'LED Floodlight 50W', 'short_description' => 'EL 50W Floodlight', 'buying_price' => 200.00, 'selling_price' => 280.00, 'supplier' => 'ACME Electric', 'goods_received_voucher' => 'V20250603-050', 'stock_level' => 9, 'min_level' => 2],
        ];

        DB::table('inventory')->insert($items);
    }
}
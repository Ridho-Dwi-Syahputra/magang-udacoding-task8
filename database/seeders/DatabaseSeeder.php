<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@inventaris.test'],
            ['name' => 'Admin Gudang', 'password' => 'password123'],
        );

        $kategori = [
            'Sembako' => 'Kebutuhan pokok sehari-hari.',
            'Minuman' => 'Minuman kemasan dan bahan minuman.',
            'Kebersihan' => 'Sabun, deterjen, dan alat kebersihan.',
            'Snack' => 'Makanan ringan.',
        ];

        foreach ($kategori as $nama => $keterangan) {
            Category::firstOrCreate(['name' => $nama], ['description' => $keterangan]);
        }

        $barang = [
            ['Sembako', 'Beras Premium 5kg', 'SMB-001', 40, 75000],
            ['Sembako', 'Minyak Goreng 2L', 'SMB-002', 25, 36000],
            ['Sembako', 'Gula Pasir 1kg', 'SMB-003', 60, 18500],
            ['Sembako', 'Tepung Terigu 1kg', 'SMB-004', 8, 13000],
            ['Sembako', 'Telur Ayam 1kg', 'SMB-005', 30, 29000],
            ['Minuman', 'Air Mineral 600ml', 'MNM-001', 120, 3500],
            ['Minuman', 'Teh Kotak 250ml', 'MNM-002', 48, 5000],
            ['Minuman', 'Kopi Sachet 1 Renceng', 'MNM-003', 35, 12000],
            ['Minuman', 'Susu UHT 1L', 'MNM-004', 6, 19500],
            ['Kebersihan', 'Sabun Batang', 'KBR-001', 90, 8000],
            ['Kebersihan', 'Deterjen Bubuk 800g', 'KBR-002', 22, 24000],
            ['Kebersihan', 'Pembersih Lantai 800ml', 'KBR-003', 18, 17000],
            ['Kebersihan', 'Sikat Cuci Piring', 'KBR-004', 4, 9500],
            ['Snack', 'Keripik Singkong 100g', 'SNK-001', 55, 9000],
            ['Snack', 'Biskuit Kaleng', 'SNK-002', 12, 42000],
            ['Snack', 'Wafer Cokelat', 'SNK-003', 70, 6500],
            ['Snack', 'Kacang Kulit 200g', 'SNK-004', 3, 15000],
            ['Sembako', 'Mie Instan 1 Dus', 'SMB-006', 15, 115000],
            ['Sembako', 'Garam Halus 500g', 'SMB-007', 80, 4000],
            ['Minuman', 'Sirup Marjan 460ml', 'MNM-005', 20, 26000],
        ];

        foreach ($barang as [$namaKategori, $nama, $sku, $stok, $harga]) {
            $kat = Category::where('name', $namaKategori)->first();

            Item::firstOrCreate(['sku' => $sku], [
                'category_id' => $kat->id,
                'name' => $nama,
                'stock' => $stok,
                'price' => $harga,
                'description' => null,
            ]);
        }
    }
}

<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class KategoriFotoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $now = now();

        $kategoriFoto = [
            ['nama_kategori' => 'Alam dan Pemandangan', 'deskripsi_kategori' => 'Kategori ini mencakup foto-foto alam yang indah, termasuk pemandangan pegunungan, danau, pantai, hutan, dan fenomena alam lainnya.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Potret dan Orang', 'deskripsi_kategori' => 'Kategori ini berfokus pada foto-foto manusia, termasuk potret individu, keluarga, teman, dan situasi sosial atau acara khusus lainnya.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Hewan dan Satwa liar', 'deskripsi_kategori' => 'Foto-foto hewan dan satwa liar, baik di habitat alaminya maupun di kebun binatang, termasuk spesies yang langka, unik, atau menarik.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Arsitektur dan Bangunan', 'deskripsi_kategori' => 'Foto-foto arsitektur dan bangunan mencakup gedung-gedung, monumen, landmark, struktur sejarah, dan desain arsitektur yang menarik.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Makanan dan Minuman', 'deskripsi_kategori' => 'Kategori ini memperlihatkan foto-foto makanan dan minuman yang lezat dan menggiurkan, termasuk hidangan masakan lokal maupun internasional.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Objek dan Produk', 'deskripsi_kategori' => 'Foto-foto objek dan produk mencakup gambar barang-barang atau produk tertentu, baik dalam konteks promosi, penjualan, atau dokumentasi.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Kesenian dan Budaya', 'deskripsi_kategori' => 'Foto-foto ini menyoroti aspek kesenian dan budaya dari berbagai komunitas, termasuk seni pertunjukan, festival, tradisi, dan warisan budaya.', 'created_at' => $now, 'updated_at' => $now],
            ['nama_kategori' => 'Transportasi dan Kendaraan', 'deskripsi_kategori' => 'Kategori ini mencakup foto-foto transportasi dan kendaraan, seperti mobil, pesawat, kapal, kereta api, dan sepeda, baik dalam kondisi penggunaan maupun di dalam lingkungan yang menarik.', 'created_at' => $now, 'updated_at' => $now],
        ];

        DB::table('kategori_foto')->insert($kategoriFoto);
    }
}

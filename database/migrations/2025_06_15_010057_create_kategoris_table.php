<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kategori;
use Illuminate\Support\Str;

class KategoriSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategoris = [
            [
                'nama' => 'Teknologi',
                'slug' => 'teknologi',
                'deskripsi' => 'Berita seputar teknologi dan inovasi'
            ],
            [
                'nama' => 'Olahraga',
                'slug' => 'olahraga',
                'deskripsi' => 'Berita seputar dunia olahraga'
            ],
            [
                'nama' => 'Politik',
                'slug' => 'politik',
                'deskripsi' => 'Berita politik dan pemerintahan'
            ],
            [
                'nama' => 'Ekonomi',
                'slug' => 'ekonomi',
                'deskripsi' => 'Berita ekonomi dan bisnis'
            ],
            [
                'nama' => 'Hiburan',
                'slug' => 'hiburan',
                'deskripsi' => 'Berita hiburan dan selebriti'
            ],
            [
                'nama' => 'Kesehatan',
                'slug' => 'kesehatan',
                'deskripsi' => 'Berita kesehatan dan gaya hidup'
            ]
        ];

        foreach ($kategoris as $kategori) {
            Kategori::create($kategori);
        }
    }
}
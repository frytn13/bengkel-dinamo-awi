<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // KITA MATIKAN FITUR BAWAAN LARAVEL YANG MEMBUAT AKUN DUMMY
        // \App\Models\User::factory(10)->create();
        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // KITA BUAT AKUN ASLI UNTUK PEMILIK USAHA (NUG)
        User::create([
            'name' => 'Nug (Pemilik Usaha)',
            'email' => 'frytn13@gmail.com', // Email pribadimu untuk menerima link Reset Password
            'password' => Hash::make('password123'), // Password default pertama kali
            'email_verified_at' => now(),
        ]);

        // Catatan: Jika nanti kamu punya Seeder untuk Kategori, Satuan, dll,
        // kamu bisa memanggilnya di bawah ini. Contoh:
        // $this->call(CategorySeeder::class);
    }
}

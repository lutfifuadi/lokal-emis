<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\Sekolah;
use App\Models\Guru;
use App\Models\Siswa;
use App\Models\Jurusan;
use App\Models\TahunAjaran;
use App\Models\Kelas;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Permissions
        $permissions = [
            'manage users',
            'crud sekolah',
            'crud siswa',
            'crud guru',
            'approve perubahan',
            'update biodata sendiri',
            'jurnal mengajar',
            'absensi',
            'laporan',
            'api akses',
        ];

        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission);
        }

        // 2. Seed Roles and Assign Permissions
        $superAdminRole = Role::findOrCreate('Super Admin');
        $superAdminRole->syncPermissions($permissions); // All permissions

        $dinasRole = Role::findOrCreate('Dinas');
        $dinasRole->syncPermissions(['laporan']);

        $operatorRole = Role::findOrCreate('Operator');
        $operatorRole->syncPermissions([
            'manage users',
            'crud sekolah',
            'crud siswa',
            'crud guru',
            'approve perubahan',
            'absensi',
            'laporan',
            'api akses',
        ]);

        $kepsekRole = Role::findOrCreate('Kepala Sekolah');
        $kepsekRole->syncPermissions([
            'approve perubahan',
            'absensi',
            'laporan',
        ]);

        $guruRole = Role::findOrCreate('Guru');
        $guruRole->syncPermissions([
            'update biodata sendiri',
            'jurnal mengajar',
            'absensi',
        ]);

        $siswaRole = Role::findOrCreate('Siswa');
        $siswaRole->syncPermissions(['update biodata sendiri']);

        $ortuRole = Role::findOrCreate('Orang Tua');
        $ortuRole->syncPermissions(['update biodata sendiri']);

        // 3. Create Default School
        $sekolah = Sekolah::create([
            'npsn' => '20580123',
            'nama' => 'MAS Abu Darrin',
            'alamat' => 'Jl. Makam K.H. Abu Darrin, Bojonegoro',
            'kontak' => '081234567890',
            'email' => 'info@masabudarrin.sch.id',
        ]);

        // 4. Create default Tahun Ajaran, Jurusan, and Kelas
        $tahunAjaran = TahunAjaran::create([
            'sekolah_id' => $sekolah->id,
            'tahun' => '2025/2026',
            'semester' => 'Ganjil',
            'is_active' => true,
        ]);

        $jurusan = Jurusan::create([
            'sekolah_id' => $sekolah->id,
            'nama' => 'Rekayasa Perangkat Lunak',
            'kode' => 'RPL',
        ]);

        $kelas = Kelas::create([
            'sekolah_id' => $sekolah->id,
            'jurusan_id' => $jurusan->id,
            'nama' => 'XII RPL 1',
            'tingkat' => 12,
            'tahun_ajaran_id' => $tahunAjaran->id,
        ]);

        // 5. Create Test Users & Assign Roles

        // Super Admin
        $superAdminUser = User::create([
            'name' => 'Super Admin EMIS',
            'username' => 'superadmin',
            'email' => 'admin@emis.local',
            'password' => bcrypt('password'),
        ]);
        $superAdminUser->assignRole($superAdminRole);

        // Dinas
        $dinasUser = User::create([
            'name' => 'Dinas Pendidikan',
            'username' => 'dinas',
            'email' => 'dinas@emis.local',
            'password' => bcrypt('password'),
        ]);
        $dinasUser->assignRole($dinasRole);

        // Operator (linked to school)
        $operatorUser = User::create([
            'name' => 'Operator Mansaba',
            'username' => 'operator',
            'email' => 'operator@emis.local',
            'password' => bcrypt('password'),
            'sekolah_id' => $sekolah->id,
        ]);
        $operatorUser->assignRole($operatorRole);

        // Kepsek (linked to school)
        $kepsekUser = User::create([
            'name' => 'Kepala Sekolah Mansaba',
            'username' => 'kepsek',
            'email' => 'kepsek@emis.local',
            'password' => bcrypt('password'),
            'sekolah_id' => $sekolah->id,
        ]);
        $kepsekUser->assignRole($kepsekRole);

        // Guru (linked to school + gurus table)
        $guruUser = User::create([
            'name' => 'Budi Utomo, S.Pd',
            'username' => 'guru',
            'email' => 'guru@emis.local',
            'password' => bcrypt('password'),
            'sekolah_id' => $sekolah->id,
        ]);
        $guruUser->assignRole($guruRole);
        Guru::create([
            'user_id' => $guruUser->id,
            'sekolah_id' => $sekolah->id,
            'nik' => '3522010101010001',
            'nuptk' => '9876543210123456',
            'nama' => $guruUser->name,
        ]);

        // Siswa (linked to school + kelas + siswas table)
        $siswaUser = User::create([
            'name' => 'Achmad Luthfi',
            'username' => 'siswa',
            'email' => 'siswa@emis.local',
            'password' => bcrypt('password'),
            'sekolah_id' => $sekolah->id,
        ]);
        $siswaUser->assignRole($siswaRole);
        Siswa::create([
            'user_id' => $siswaUser->id,
            'sekolah_id' => $sekolah->id,
            'kelas_id' => $kelas->id,
            'nisn' => '0081234567',
            'nik' => '3522010202020002',
            'nama' => $siswaUser->name,
            'alamat' => 'Jl. Pemuda No. 15, Bojonegoro',
            'no_hp' => '089876543210',
            'status' => 'aktif',
        ]);

        // Orang Tua (linked to school + siswas table - child's parents)
        $ortuUser = User::create([
            'name' => 'Slamet Wijaya',
            'username' => 'ortu',
            'email' => 'ortu@emis.local',
            'password' => bcrypt('password'),
            'sekolah_id' => $sekolah->id,
        ]);
        $ortuUser->assignRole($ortuRole);
    }
}

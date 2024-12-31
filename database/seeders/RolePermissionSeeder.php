<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Buat Roles
        $koordinatorRole = Role::create(['name' => 'koordinator']);
        $ketuakaderRole = Role::create(['name' => 'ketua kader']);
        $kaderRole = Role::create(['name' => 'kader']);

        // (Opsional) Jika ingin menambahkan Permissions:
        // Permission::create(['name' => 'edit articles']);
        // Permission::create(['name' => 'delete articles']);
        // Permission::create(['name' => 'view articles']);

        // (Opsional) Berikan Permissions ke Roles
        // $koordinatorRole->givePermissionTo(['edit articles', 'delete articles', 'view articles']);
        // $ketuakaderRole->givePermissionTo(['edit articles', 'view articles']);
        // $kaderRole->givePermissionTo(['view articles']);

        // Berikan Role ke User
        $user = User::find(1); // Pastikan user dengan ID 1 ada di database
        if ($user) {
            $user->assignRole('koordinator'); // Berikan role 'koordinator' ke user
        } else {
            $this->command->error('User dengan ID 1 tidak ditemukan.');
        }
        // (Opsional) Jika ingin menambahkan role ke user lain:
        // $user2 = User::find(2);
        $user2 = User::find(2);
        if ($user2) {
            $user2->assignRole('ketua kader');
        }
        $user3 = User::find(3);
        if ($user3) {
            $user3->assignRole('kader');
        }
    }
}

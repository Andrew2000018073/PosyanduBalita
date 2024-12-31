<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function getAllUsersWithRoles()
    {
        // Ambil semua user beserta role-nya
        $users = User::with('roles')->get();

        // Format data untuk API response
        $data = $users->map(function ($user) {
            // Ambil role user
            $rolesname = $user->getRoleNames()->implode(', '); // Gabungkan role menjadi string
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $rolesname // Role sebagai string
            ];
        });

        return response()->json([
            'success' => true,
            'users' => $data
        ]);
    }


    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|confirmed|min:8',
            'role' => 'required|string|exists:roles,name',
        ]);

        // Buat pengguna baru
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Berikan role ke pengguna
        $user->assignRole($request->role);

        return response()->json([
            'success' => true,
            'message' => 'Pengguna berhasil ditambahkan',
            'user' => $user
        ]);
    }

    public function getRoles()
    {
        $roles = Role::all();

        return response()->json([
            'success' => true,
            'roles' => $roles,
        ]);
    }

    public function updatePeserta($id, Request $request)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Validasi data yang diterima
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'nullable|string|min:6',
            'role' => 'required|string'
        ]);

        // Perbarui data user
        $user->name = $request->name;
        $user->email = $request->email;

        // Update password jika ada
        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        // Update role
        $user->syncRoles($request->role); // Menggunakan method syncRoles jika Anda menggunakan Spatie Role & Permission package

        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'User updated successfully',
            'user' => $user
        ]);
    }

    public function deleteUser($id)
    {
        try {
            // Cari user berdasarkan ID
            $user = User::findOrFail($id);

            // Hapus permanen user
            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'User berhasil dihapus.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}

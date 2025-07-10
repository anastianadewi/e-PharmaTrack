<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $users = User::all(); // Ambil semua data dari tabel users

        $search = $request->input('search');
        $users = \App\Models\User::query()
            ->when($search, function ($query, $search) {
                $query->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('role', 'like', "%{$search}%");
            })
            ->get();

        return view('user.index', compact('users'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:50',
                'username' => 'required|string|max:50|unique:user,username|regex:/^\S*$/u',
                'password' => 'required|string|min:8|max:8',
                'role' => 'required|in:nakes,pengelolaBMN,kepala',
            ]);

            User::create([
                'nama' => $request->nama,
                'username' => $request->username,
                'password' => bcrypt($request->password),
                'role' => $request->role,
            ]);

            return redirect()->route('user')->with('success', 'Data user berhasil ditambahkan');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menambahkan user: ' . $e->getMessage());
        }
    }

    public function destroy($id_user)
    {
        try {
            User::where('id_user', $id_user)->delete();

            return redirect()->route('user')->with('success', 'Data user berhasil dihapus.');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus user: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id_user)
    {
        try {
            $request->validate([
                'nama' => 'required|string|max:50',
                'username' => 'required|string|max:50|unique:user,username,' . $id_user . ',id_user|regex:/^\S*$/u',
                'password' => 'nullable|string|max:8',
                'role' => 'required|string|max:255',
            ]);

            $user = User::where('id_user', $id_user)->firstOrFail();

            $user->nama = $request->nama;
            $user->username = $request->username;
            if ($request->filled('password')) {
                $user->password = bcrypt($request->password);
            }
            $user->role = $request->role;
            $user->save();

            return redirect()->route('user')->with('success', 'Data user berhasil diperbarui.');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui user: ' . $e->getMessage());
        }
    }
}

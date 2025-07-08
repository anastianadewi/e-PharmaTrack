<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function verify(Request $request)
    {
        if (!Hash::check($request->current_password, Auth::user()->password)) {
            return back()
                ->withErrors(['current_password' => 'Password salah'])
                ->withInput(); // agar input tetap muncul
        }

        session()->flash('edit_profile', true); // agar trigger modal edit
        return back();
    }

    public function update(Request $request)
    {
        try{
            $request->validate([
                'nama' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:user,username,' . Auth::user()->id_user . ',id_user',
                'password' => 'nullable|string|min:6',
            ]);

            $user = User::find(Auth::user()->id_user);

            // Ganti ke nama kolom yang sesuai di database kamu
            $user->nama = $request->nama; // Gantilah 'name' ke 'nama'
            $user->username = $request->username;

            if ($request->filled('password')) {
                $user->password = Hash::make($request->password);
            }

            $user->save();

            return redirect()->back()->with('success', 'Profil berhasil diperbarui');
        }
        catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal memperbarui profil: ' . $e->getMessage());
        }
    }
}

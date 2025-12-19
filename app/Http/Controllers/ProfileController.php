<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function edit(Request $request)
    {
        $user = $request->user();
        return view('mypage.profile', compact('user'));
    }

    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'postal_code' => ['nullable', 'regex:/^\d{3}-?\d{4}$/'], // 1234567 or 123-4567
            'address'     => ['nullable', 'string', 'max:255'],
            'building'    => ['nullable', 'string', 'max:255'],
            'profile_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $user->postal_code = isset($validated['postal_code']) && $validated['postal_code'] !== null
            ? str_replace('-', '', $validated['postal_code'])
            : null;

        // 画像アップロード（任意）
        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profiles', 'public');

            // 既存画像があれば削除（任意）
            if ($user->profile_image_path) {
                Storage::disk('public')->delete($user->profile_image_path);
            }

            $user->profile_image_path = $path;
        }

        $user->name = $validated['name'];
        $user->postal_code = $validated['postal_code'] ?? null;
        $user->address = $validated['address'] ?? null;
        $user->building = $validated['building'] ?? null;

        $user->save();

        return redirect()->route('mypage.index')->with('status', 'プロフィールを更新しました。');
    }
}

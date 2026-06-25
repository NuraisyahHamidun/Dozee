<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule;

class ManagerProfileController extends Controller
{
    public function edit()
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }
        $manager = Auth::guard('manager')->user();
        return view('manager.profile', compact('manager'));
    }

    public function update(Request $request)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $manager = Auth::guard('manager')->user();

        if ($request->filled('name')) {
            $baseUsername = \Illuminate\Support\Str::of($request->name)->lower()->replace(' ', '');
            $username = (string) $baseUsername;
            $count = 1;

            while (
                \App\Models\Manager::where('username', $username)->where('manager_id', '!=', $manager->manager_id)->exists()
                || \App\Models\Salesman::where('username', $username)->exists()
            ) {
                $username = $baseUsername . $count;
                $count++;
            }

            $request->merge(['username' => $username]);
        }

        $rules = [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => [
                'required', 'string', 'lowercase', 'email', 'max:255',
                Rule::unique('manager', 'email')->ignore($manager->manager_id, 'manager_id'),
                Rule::unique('salesman', 'email'),
            ],
            'phone_number' => ['required', 'regex:/^[0-9]{3}-[0-9]{7,8}$/'],
            'address'  => ['required', 'string', 'max:500'],
            'profile_picture' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ];

        if ($request->filled('password')) {
            $rules['password'] = [
                'required', 'string', 'min:8',
                'regex:/[a-z]/',
                'regex:/[A-Z]/',
                'regex:/[0-9]/',
                'regex:/[@$!%*?&]/',
                'confirmed',
            ];
        }

        $request->validate($rules, [
            'phone_number.regex' => 'Invalid phone format. Use 012-3456789 or 012-34567890',
        ]);

        $data = [
            'name'         => $request->name,
            'username'     => $request->username,
            'email'        => $request->email,
            'phone_number' => $request->phone_number,
            'address'      => $request->address,
        ];

        if ($request->hasFile('profile_picture')) {
            // Delete old profile picture if exists
            if ($manager->profile_picture && \Illuminate\Support\Facades\Storage::disk('public')->exists($manager->profile_picture)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($manager->profile_picture);
            }

            $path = $request->file('profile_picture')->store('profile_pictures', 'public');
            $data['profile_picture'] = $path;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $manager->update($data);

        return redirect()->route('manager.profile.edit')->with('success', 'Profile updated successfully.');
    }

    public function destroy(Request $request)
    {
        if (!Auth::guard('manager')->check()) {
            abort(403);
        }

        $manager = Auth::guard('manager')->user();
        Auth::guard('manager')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        $manager->delete();

        return redirect('/')->with('success', 'Your account has been deleted.');
    }
}

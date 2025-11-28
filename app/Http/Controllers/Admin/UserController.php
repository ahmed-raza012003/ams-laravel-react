<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\PrismaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index()
    {
        $users = PrismaService::getUsers();
        $roles = PrismaService::getRoles();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'roles' => $roles,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:Users,email',
            'password' => 'required|string|min:8',
            'roleId' => 'required|integer',
        ]);

        DB::table('Users')->insert([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['roleId'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'User created successfully.');
    }

    public function show($id)
    {
        $user = PrismaService::getUser($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        return response()->json($user);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:Users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'roleId' => 'required|integer',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['roleId'],
            'updated_at' => now(),
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        DB::table('Users')->where('id', $id)->update($updateData);

        return redirect()->back()->with('success', 'User updated successfully.');
    }

    public function destroy($id)
    {
        if (auth()->id() == $id) {
            return redirect()->back()->with('error', 'You cannot delete your own account.');
        }

        DB::table('Users')->where('id', $id)->delete();

        return redirect()->back()->with('success', 'User deleted successfully.');
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = auth()->user();
        
        if (!$user) {
            return redirect()->route('login');
        }

        $userWithRole = DB::table('Users')
            ->join('Roles', 'Users.role_id', '=', 'Roles.id')
            ->select('Users.*', 'Roles.name as role_name')
            ->where('Users.id', $user->id)
            ->first();

        if (!$userWithRole || strtolower($userWithRole->role_name) !== strtolower($role)) {
            if ($userWithRole && strtolower($userWithRole->role_name) === 'admin') {
                return redirect()->route('admin.dashboard');
            }
            return redirect()->route('customer.dashboard');
        }

        return $next($request);
    }
}

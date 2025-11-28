<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();
        $userRole = null;

        if ($user) {
            $userWithRole = \Illuminate\Support\Facades\DB::table('Users')
                ->join('Roles', 'Users.role_id', '=', 'Roles.id')
                ->select('Users.*', 'Roles.name as role_name')
                ->where('Users.id', $user->id)
                ->first();
            
            if ($userWithRole) {
                $userRole = strtolower($userWithRole->role_name);
            }
        }

        return [
            ...parent::share($request),
            'auth' => [
                'user' => $user,
                'role' => $userRole,
            ],
        ];
    }
}

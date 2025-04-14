<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'flash' => [
                'message' => fn() => $request->session()->pull('message'),
                'status' => fn() => $request->session()->pull('status'),
                'error' => fn() => $request->session()->pull('error'),
            ],
            'auth' => [
                'user' => function () {
                    if (Session::has('user_id')) {
                        $user = \App\Models\User::find(Session::get('user_id'));
                        return $user ? [
                            'id' => $user->id,
                            'username' => $user->username,
                            'email' => $user->email,
                            'profile_pic' => $user->profile_pic && file_exists(public_path($user->profile_pic))
                                ? '/' . $user->profile_pic
                                : '/default-user.png',
                        ] : null;
                    }
                    return null;
                },
            ],
        ]);
    }
}

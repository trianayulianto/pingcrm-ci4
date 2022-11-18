<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware implements FilterInterface
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     *
     * @param  \CodeIgniter\HTTP\Request  $request
     * @return string|null
     */
    public function version(Request $request)
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @param  \CodeIgniter\HTTP\Request  $request
     * @return array
     */
    public function share(Request $request)
    {
        return array_merge(parent::share($request), [
            'auth' => function () {
                $user = service('auth')->id()
                    ? (new UserModel())->with('accounts')->find(service('auth')->id())
                    : false;

                return [
                    'user' => $user ? [
                        'id' => $user->id,
                        'first_name' => $user->first_name,
                        'last_name' => $user->last_name,
                        'email' => $user->email,
                        'owner' => $user->owner,
                        'account' => [
                            'id' => $user->account->id,
                            'name' => $user->account->name,
                        ],
                    ] : null,
                ];
            },
            'flash' => function () {
                return [
                    'success' => session()->get('success'),
                    'error' => session()->get('error'),
                ];
            },
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::paginate();

        return view('user.index', compact('users'))
            ->with('i', (request()->input('page', 1) - 1) * $users->perPage());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = new User();
        $user->role = UserRole::Employee();
        $user->is_can_access_admin = 0;
        $user->is_can_access_manager = 0;
        $user->is_can_access_supervisor = 0;
        $user->is_can_access_customer = 0;

        $userRoles = UserRole::asSelectArray();

        return view('user.create', compact('user', 'userRoles'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate(User::$onCreateRules);

        $data = $request->all();
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        return redirect()->route('users.index')
            ->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        if (config('auth.super_admin_user_id') == $id && Auth::user()->id != $id) {
            return redirect()->route('users.index')
                ->with('success', 'Can not show super admin');
        }

        $user = User::find($id);

        return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (config('auth.super_admin_user_id') == $id && Auth::user()->id != $id) {
            return redirect()->route('users.index')
                ->with('success', 'Can not edit super admin');
        }

        $user = User::find($id);

        $userRoles = UserRole::asSelectArray();

        return view('user.edit', compact('user', 'userRoles'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  User $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        if (config('auth.super_admin_user_id') == $user->id && Auth::user()->id != $user->id) {
            return redirect()->route('users.index')
                ->with('success', 'Can not edit super admin');
        }

        request()->validate(User::$onUpdateRules);

        $data = $request->all();
        if (isset($data['password']) && trim($data['password']) != '') {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function destroy($id)
    {
        if (config('auth.super_admin_user_id') == $id && Auth::user()->id != $id) {
            return redirect()->route('users.index')
                ->with('success', 'Can not delete super admin');
        }

        $user = User::find($id)->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully');
    }
}
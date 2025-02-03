<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('users.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {

            $request->validate([
                'user-name' => 'required|min:5|max:255',
                'user-email' => 'required|email|max:255|unique:users,email',
                'user-password' => ['required', Password::defaults()],
                'user-isadmin' => 'required|boolean',
                ],
                [
                    'user-name.required' => 'User name is required',
                    'user-name.min' => 'User name must be at least 5 characters',
                    'user-name.max' => 'User name must be less than 255 characters',
                    'user-email.required' => 'User email is required',
                    'user-email.max' => 'User email must be less than 255 characters',
                    'user-email.email' => 'User email is not valid',
                    'user-email.unique' => 'User email is already registered',
                    'user-password.required' => 'User password is required',
                    'user-password.min' => 'User password must be at least 8 characters',
                    'user-password.max' => 'User password must be less than 255 characters',
                    'user-isadmin.required' => 'Admin is required',
                ]
            );

            $user = new User();
            $user->name = $request['user-name'];
            $user->email = $request['user-email'];
            $user->password = Hash::make($request['user-password']);
            $user->is_admin =$request['user-isadmin'];
            $user->save();

            return redirect()
                ->route('users.index')
                ->with('success', 'User created successfully');

        } catch (\Exception $e) {
            // Catch any exceptions and return an error response
            return response()->json([
                'error' => 'An error occurred while processing the request.',
                'details' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user)
    {
        return view('users.show', ['user' => $user]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user)
    {
        return view('users.edit', ['user' => $user]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {

        //dd($request);

        $request->validate([
            'user-name' => 'required|max:255',
            'user-email' => 'required|email|max:255',
            'user-isadmin' => 'required|boolean',
        ],
        [
            'user-name.required' => 'User name is required',
            'user-email.required' => 'User email is required',
            'user-email.max' => 'User email must be less than 255 characters',
            'user-email.email' => 'User email is not valid',
            'user-isadmin.required' => 'Admin is required',
        ]);

        $input = $request->all();

        $user = User::find($input['user-id']);

        $update = $user;
        $update->name = $input['user-name'];
        $update->email = $input['user-email'];
        $update->is_admin = $input['user-isadmin'];
        $update->save();

        return redirect()
            ->route('users.show', ['user' => $user])
            ->with('success', 'Profile updated successfully');

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        {
            // Make sure the user can't delete themselves
            if(Auth::id() == $user->id){
                return redirect()
                    ->back()
                    ->with('error', 'You cannot delete yourself');
            }

            // Delete the user
            $user->delete();

            return redirect()
                ->back()
                ->with('success', 'User deleted successfully');
        }
    }

}

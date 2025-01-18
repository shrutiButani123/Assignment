<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Role;
use App\Models\File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|max:20',
            'password_confirmation' => 'required|min:8|max:20|same:password',
            'contact' => 'required|string|max:20',
            'city_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'postcode' => 'nullable|string',
            'hobbies' => 'required|array',
            'gender' => 'required|in:male,female,other',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'files' => 'nullable|array',
            'files.*' => 'file|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        DB::beginTransaction();

        try {
            $data['password'] = bcrypt($data['password']);
            $data['hobbies'] = json_encode($data['hobbies']);
            $user = User::create($data);

            $user->roles()->attach($data['roles']);

            if ($request->has('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('uploads/'. $user->id);
                    $user->files()->create(['path' => $path]);
                }
            }

            DB::commit();

            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'status' => 'success',
                'message' => 'User created successfully',
                'data' => [
            'user' => $user,
            'roles' => $user->roles->pluck('name'),
            'files' => $user->files->pluck('path'),
        ],
                'token' => $token
            ], 201);


        }catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('authToken')->accessToken;

            return response()->json([
                'data' => $user,
                'status' => 'success',
                'message' => 'Login successfully.',
                'access_token' => $token,
                'token_type' => 'Bearer',
            ], 200);

        } else {
            return response()->json([
                'error' => 'Invalid email or password. Please try again.'
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'last_name' => 'required|string',
            'contact' => 'required|string|max:20',
            'city_id' => 'nullable|integer',
            'state_id' => 'nullable|integer',
            'country_id' => 'nullable|integer',
            'postcode' => 'nullable|string',
            'hobbies' => 'required|array',
            'gender' => 'required|in:male,female,other',
            'roles' => 'required|array',
            'roles.*' => 'exists:roles,id',
            'files' => 'nullable|array',
            'files.*' => 'file|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = $validator->validated();

        DB::beginTransaction();

        try {
            $user = User::findOrFail($id);

            if (isset($data['hobbies'])) {
                $data['hobbies'] = json_encode($data['hobbies']);
            }

            $user->update($data);

            if ($request->has('roles')) {
                $user->roles()->sync($data['roles']);
            }

            if ($request->has('files')) {
                foreach ($request->file('files') as $file) {
                    $path = $file->store('uploads/'. $user->id);
                    $user->files()->create(['path' => $path]);
                }
            }

            if ($request->has('files')) {
                foreach ($user->files as $file) {
                    Storage::delete($file->path);
                }
            
                $user->files()->delete();
            
                foreach ($request->file('files') as $file) {
                    $path = $file->store('uploads/' . $user->id);
                    $user->files()->create(['path' => $path]);
                }
            }        

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'User updated successfully',
                'data' => [
                    'user' => $user,
                    'roles' => $user->roles->pluck('name'),
                    'files' => $user->files->pluck('path'),
                ]
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    
    public function getUsers(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $users = User::with('roles', 'files')->where('id', '!=', $user->id)->paginate($perPage, ['*'], 'page', $page);

        return response()->json([
            'data' => $users->items(),
            'total' => $users->total(),
            'current_page' => $users->currentPage(),
            'last_page' => $users->lastPage(),
            'per_page' => $users->perPage(),
            'from' => $users->firstItem(),
            'to' => $users->lastItem(),
            'status' => 'success',
            'message' => 'Users retrieved successfully',
        ], 200);
    }
}

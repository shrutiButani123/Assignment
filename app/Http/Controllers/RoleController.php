<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::all();

        return response()->json([
            'status' => 'success',
            'message' => 'Roles retrieved successfully',
            'data' => $roles
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Role::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists()) {
                        $fail('The ' . $attribute . ' must be unique.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role = Role::create([
            'name' => strtolower($request->input('name')),
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Role created successfully',
            'data' => $role,
        ], 201);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Role retrieved successfully',
            'data' => $role,
        ], 201);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'message' => 'Role not found'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (Role::whereRaw('LOWER(name) = ?', [strtolower($value)])->exists()) {
                        $fail('The ' . $attribute . ' must be unique.');
                    }
                },
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $role->update([
            'name' => strtolower($request->input('name')),
        ]);

        return response()->json([
            'message' => 'Role updated successfully.',
            'role' => $role
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return response()->json(['message' => 'Role not found.'], 404);
        }
    
        if ($role->users()->exists()) {
            return response()->json([
                'message' => 'This role cannot delete because it is associated with users.'
            ], 400);
        }
    
        $role->delete();
    
        return response()->json([
            'message' => 'Role deleted successfully.'
        ], 200);
    }
}

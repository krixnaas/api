<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role; 
use DataTables;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Contracts\DataTable;

class RolesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->ajax())
        {
            return $this->getRoles();
        }
        return view('users.roles.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::get(); 
        return view('users.roles.create')->with(['Permissions'=> $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Validate name
        $this->validate($request, [
            'name' => 'required|unique:roles,name', 
            'permission' => 'required'
        ]);
        $role = Role::create(['name' => strtolower(trim($request->name))]);
        $role->syncPermissions($request->permission);
        if($role)
        {
            toast('New Role Added Successfully.','success');
            return view('users.roles.index');
        }
        toast('Error on Saving role','error');
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Role $role)
    {
        if($request->ajax())
        {
            return $this->getRolesPermissions($role);
        }
        return view('users.roles.show')->with(['role' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Role $role)
    {
        return view('users.roles.edit')->with(['role' => $role]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Role $role, Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'permission' => 'required',
        ]);
        $role->update($request->only('name'));
        $role->syncPermissions($request->permission);
        if($role)
        {
            toast('Role Updated Successfully.','success');
            return view('users.roles.index');
        }
        toast('Error on Updating role','error');
        return back()->withInput();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, Role $role)
    {
        if($request->ajax() && $role->delete())
        {
            return response(["message" => "Role Deleted Successfully"], 200);
        }
        return response(["message" => "Data Delete Error! Please Try again"], 201);
    }

    private function getRoles()
    {
        $data = Role::withCount(['users', 'permissions'])->get(); 
        return DataTables::of($data)
                ->addColumn('name', function($row){
                    return ucfirst($row->name);
                })
                ->addColumn('users_count', function($row){
                    return $row->users_count;
                })
                ->addColumn('permissions_count', function($row){
                    return $row->permissions_count;
                })
                ->addColumn('action', function($row){
                    $action = ""; 
                    $action.="<a class='btn btn-xs btn-success' id='btnShow' href='".route('users.roles.show', $row->id)."'><i class='fas fa-eye'></i></a> "; 
                    $action.="<a class='btn btn-xs btn-warning' id='btnEdit' href='".route('users.roles.edit', $row->id)."'><i class='fas fa-edit'></i></a>"; 
                    $action.=" <button class='btn btn-xs btn-outline-danger' id='btnDel' data-id='".$row->id."'><i class='fas fa-trash'></i></button>"; 
                    return $action;
                })
                ->make('true');
    }

    private function getRolesPermissions($role)
    {
        $permissions = $role->permissions; 
        return DataTables::of($permissions)->make('true');
    }
}

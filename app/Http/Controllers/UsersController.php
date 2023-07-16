<?php

namespace App\Http\Controllers;

use DataTables; 
use Carbon\Carbon;
use App\Models\User; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Laravel\Ui\Presets\React;
use Spatie\Permission\Models\Role;

class UsersController extends Controller
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
            return $this->getUsers();
        }
        return view('users.index')->with(["roles" => Role::get()]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required', 
            'email' => 'required|email:rfc,dns|unique:users,email'
        ]);
        if($request->has('roles'))
        {
            $user->create($request->all())->roles()->sync($request->roles);
        }else{            
            $user->create($request->all());
        }
        if($user)
        {
            toast('New User Created Successfully.','success');
            return Redirect::to('users');
        }
        toast('Error Creating New User','error');
        return back()->withInput();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(User $user)
    {
        return view('users.edit', [
            "user" => $user, 
            "userRole" => $user->roles->pluck('name')->toArray(), 
            "roles" => Role::latest()->get()
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, User $user)
    {
        $this->validate($request, [
            'name' => 'required', 
            'email' => 'required|email:rfc,dns|unique:users,email,'.$user->id,
        ]); 
        
        $user->update($request->all());
        $user->roles()->sync($request->input('roles'));

        if($user)
        {
            toast('User Updated Successfully.','success');
            return Redirect::to('users');
        }
        toast('Error in User Update','error');
        return back()->withInput();

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, User $user)
    {
        if($request->ajax() && $user->delete())
        {
            return response(["message" => "User Deleted Successfully"], 200);
        }
        return response(["message" => "Data Delete Error! Please Try again"], 201);
    }

    private function getUsers()
    {
        $data = User::with('roles')->get();
        return DataTables::of($data)
                ->addColumn('name', function($row){
                    return ucfirst($row->name);
                })
                ->addColumn('date', function($row){
                    return Carbon::parse($row->created_at)->format('d M, Y h:i:s A');
                })
                ->addColumn('roles', function($row){
                    $role = ""; 
                    if($row->roles != null)
                    {
                        foreach($row->roles as $next)
                        {
                            $role.='<span class="badge badge-primary">'.ucfirst($next->name).'</span> ';
                        }
                    }
                    return $role;
                })
                ->addColumn('action', function($row){
                    $action = ""; 
                    $action.="<a class='btn btn-xs btn-warning' id='btnEdit' href='".route('users.edit', $row->id)."'><i class='fas fa-edit'></i></a>"; 
                    $action.=" <button class='btn btn-xs btn-outline-danger' id='btnDel' data-id='".$row->id."'><i class='fas fa-trash'></i></button>"; 
                    return $action;
                })
                ->rawColumns(['name', 'date','roles', 'action'])->make('true');
    }
}

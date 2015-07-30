<?php

class PermissionController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Show a listing of employees.

        $employeeId = Session::get('userEmployeeId');

        $employee = new Employee;
        $employeeInfo = $employee->getEmployeeInfoById($employeeId);

        $permissions = Permission::all();
        return View::make('admin.indexpermission')
            ->with('permissions', $permissions)
            ->with('employeeInfo', $employeeInfo);
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function createPermission()
	{
		
        $input = array('modulename' => Input::get('modulename'));

        $validator = Validator::make($input, array(
            'modulename' => 'required'
            )
        );

        // process the login
        if ($validator->fails()) {
            return Redirect::action('PermissionController@create')
                ->withErrors($validator);
        } else {
            $module = Input::get('modulename');
			$permission = new Permission;
            $permission->modulename = strtolower(Input::get('modulename')) ;
            $permission->permissions = Input::get('permissions');

            $c = array();
            $perms_array = $permission->permissions;
            $c = explode(',', $perms_array);
            $perm = '';

            foreach ($c as $key => $value) 
            {
                $val = '"' . $permission->modulename . '.' . $c[$key] . '"';
                $perm .= $val . ', ';
            }

            $perm2 = rtrim($perm, ", ");
            $perm3 = '[' . $perm2 . ']';
            //echo $perm3;
            $permission->permissions = $perm3;
            $permission->save();
            return Redirect::action('PermissionController@index')
                  ->with('success', Lang::get('permissions.create_success'));

        
        }

	}


    // show edit permission page
    public function editpermission($id)
    {

        $permission = Permission::findOrFail($id);
        $perms = array("View", "Create", "Update", "Delete");
        //var_dump($permission);
        return View::make('admin.editpermission')
            ->with('permission', $permission)
            ->with('perms', $perms);        
    }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function handleEditpermission()
    {
        // Handle edit form submission.
        $permission = Permission::findOrFail(Input::get('pid'));
        $permission->modulename = Input::get('modulename');
        $permission->permissions = Input::get('group_permission');
        $perms_array = $permission->permissions;
        $perm = '';

        foreach ($perms_array as $key => $value) 
        {
            $val = '"' . $permission->modulename . '.' . $perms_array[$key] . '"';
            $perm .= $val . ', ';
        }

        $perm2 = rtrim($perm, ", ");
        $perm3 = '[' . $perm2 . ']';
        $permission->permissions = $perm3;
        $permission->save();
        return Redirect::action('PermissionController@index')
            ->with('success', Lang::get('permissions.update_success'));
    }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		// Show the create employee form.

        $employeeId = Session::get('userEmployeeId');

        $employee = new Employee;
        $employeeInfo = $employee->getEmployeeInfoById($employeeId);

        return View::make('admin.createpermission')
              ->with('employeeInfo', $employeeInfo);
	}



	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroyperm($id)
    {

        $permission = Permission::findOrFail($id);
        $permission->delete();
        return Redirect::action('PermissionController@index')
            ->with('success', Lang::get('permissions.delete_success'));

    }


}

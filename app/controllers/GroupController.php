<?php

class GroupController extends \BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Show a listing of groups.

        $employeeId = Session::get('userEmployeeId');

        $employee = new Employee;
        $employeeInfo = $employee->getEmployeeInfoById($employeeId);

        $groups = Group::all();
        return View::make('admin.indexgroup')
            ->with('groups', $groups)
            ->with('employeeInfo', $employeeInfo);


	}


	public function createGroup()
    {

        $employeeId = Session::get('userEmployeeId');

        $employee = new Employee;
        $employeeInfo = $employee->getEmployeeInfoById($employeeId);

        $perms_arr = array('View', 'Create', 'Update', 'Delete');
        $permissions = Permission::all();
        return View::make('admin.creategroup')
        	     ->with('perms_arr', $perms_arr)
        	     ->with('permissions', $permissions)
                 ->with('employeeInfo', $employeeInfo);

    }


    /**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	//public function edit($id)
	//{
		//
	//}

    public function editgroup($id)
    {

        $employeeId = Session::get('userEmployeeId');

        $employee = new Employee;
        $employeeInfo = $employee->getEmployeeInfoById($employeeId);

        // Show details of a user.
        $group = Group::findOrFail($id);
        $perms = array("view"=>1, "create"=>2, "update"=>3, "delete"=>4);
        $permissions = Permission::all();

        return View::make('admin.editgroup')
            ->with('group', $group)
            ->with('perms', $perms)
            ->with('permissions', $permissions)
            ->with('employeeInfo', $employeeInfo);
    }


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function handleCreate()
	{

		$rules = array(
            'name' => 'required|alpha'
        );

     	$input = array('groupname' => Input::get('groupname'));
		$validator = Validator::make($input, array(
        	'groupname' => 'required'
    		)
		);

        if ($validator->fails()) {
            return Redirect::action('GroupController@index')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {

        	$permissions = Input::get('permissions');
            $group = new Group;
            $group->permissions = Input::get('permissions');
            $groupname = Input::get('groupname');
            $permns = $group->permissions;
        
            foreach ($permns as $key => $value) 
            {    
                if(strcmp($value,'0') == 0)
                {
                    unset($permns[$key]);
                }
            }
            
            $arr2 = json_encode($permns, JSON_NUMERIC_CHECK);
            $group->name = $groupname;
            $group->permissions = $arr2;
            $group->save();

            return Redirect::action('GroupController@index')
                ->with('success', Lang::get('groups.create_success'));

        }

	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function handleEditGroup()
    {

        $rules = array(
            'name' => 'required|alpha'
        );

        $input = array('groupname' => Input::get('groupname'));

        $validator = Validator::make($input, array(
            'groupname' => 'required'
            )
        );

        // process the login
        if ($validator->fails()) {
            return Redirect::action('GroupController@index')
                ->withErrors($validator)
                ->withInput(Input::except('password'));
        } else {
            // store

            //$permissions = Input::get('permissions');
            //var_dump($permissions);
            /*$perm = '';
            foreach ($permissions as $key => $value) 
            {
                if($value != 0)
                {
                    $data = '"' . $key . '":' . $value;
                    $perm .= $data . ', ';
                }
            }*/

            
            /*$groupname = Input::get('groupname');
            $perm2 = rtrim($perm, ", ");
            //echo $perm2;
            $group = new Group;
            $group->name = $groupname;
            $group->permissions = $perm2;
            $group->save();
            return Redirect::action('GroupController@index');*/

            $id = Input::get('id');
            $group = Group::findOrFail($id);
            $group->permissions = Input::get('permissions');
            $groupname = Input::get('groupname');
            $permns = $group->permissions;
        
            foreach ($permns as $key => $value) 
            {    
                if(strcmp($value,'0') == 0)
                {
                    unset($permns[$key]);
                }
            }
            
            $arr2 = json_encode($permns, JSON_NUMERIC_CHECK);
            $group->name = $groupname;
            $group->permissions = $arr2;
            $group->save();

            return Redirect::action('GroupController@index')
                ->with('success', Lang::get('groups.update_success'));



        }
    }


	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroygroup($id)
	{
		
        $group = Group::findOrFail($id);
        $group->delete();
        return Redirect::action('GroupController@index')
                ->with('success', Lang::get('groups.delete_success'));
	}

}
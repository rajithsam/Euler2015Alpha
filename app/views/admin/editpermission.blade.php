@extends('employee.layout')

@section('header')
    <h3>
        <i class="icon-ban-circle"></i>
        Permissions
    </h3>
@stop

@section('help')
    <p class="lead">Module Permission</p>
    <p>
        Every keywords enter in the <strong>Permissions field</strong> will be prefixed with the
        <strong>Module name Field</strong>.
    </p>
    <br/>
    <p class="lead">Example</p>
    <p>
        <strong>Module name: </strong> blog
    </p>
    <p>
        <strong>Permissions: </strong> view, create, delete and publish
    </p>
    <p>
        <strong>Result:</strong> blog.view, blog.create, blog.delete and blog.publish
    </p>
@stop

@section('content')
    <div class="row">
        <div class="span12">
            <div class="block">
                <p class="block-heading">Create new permissions for a module</p>
                <div class="block-body">                    
                    {{-- Former::horizontal_open(route('cpanel.permissions.update', array($permission->id)))->method('PUT') --}}
                    <form action="{{ action('PermissionController@handleEditpermission') }}" method="post">
                    {{ Form::label('modulename', 'Module Name') }}
                    <input type="text" name="modulename" required="" value="{{$permission->modulename}}" /><br>
                    {{ Form::label('permissions', 'Permissions') }}

                        <select id="groups" name="group_permission[]" class="select2" multiple="true">
                            <?php
                                $permns  = str_replace(array('[',']'),'',$permission->permissions);
                                $permns2 = str_replace('"','',$permns);
                                //$permns3 = rtrim($permns2, ",");
                                //$permns3 = substr($permns2, 0, -1);
                                //echo $permns3;
                                $a = explode(',',$permns2);
                                $c = array();
                                for($j=0; $j<sizeof($a); $j++)
                                {
                                    $permssn = ucfirst(substr($a[$j], strpos($a[$j], ".") + 1));
                                    array_push($c, $permssn);
                                }

                                for($i=0; $i<sizeof($perms); $i++)
                                {
                                    if(in_array($perms[$i], $c))
                                    {  
                                        //echo "hello world";  ?>
                                        <option value="{{ $perms[$i] }}" selected>{{ $perms[$i] }}</option>
                            <?php   }
                                    else
                                    { ?>
                                        <option value="{{ $perms[$i] }}">{{ $perms[$i] }}</option>
                            <?php   }

                                }
                            ?>
                        </select>

                    <!--input type="text" name="permissions" id="permission-tags" required="" value="{{ $permission->permissions }}"/-->
                    <input type="hidden" name="pid" required="" value="{{$permission->id}}" />
                    {{-- Former::xlarge_text('name', 'Module Name',$permission->name)->required() --}}
            


                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Save changes</button>
                        <a href="{{ action('PermissionController@index') }}" class="btn">Cancel</a>
                    </div>
                    {{-- Former::close() --}}
                </div>
            </div>
        </div>
    </div>
@stop


            <!--table width="50%" cellpadding="10" cellspacing="10"-->
            <table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
                @foreach( $permissions as $permission) 
                <tr><td colspan="2"><legend>{{ $permission['modulename'] }}</legend></td></tr>
            <?php


                        $permns  = str_replace(array('[',']'),'',$permission['permissions']);
                        $permns2 = str_replace('"','',$permns);
                        $a = explode(',',$permns2);

                        for($i=0;$i<sizeof($a);$i++)
                        { 

                            $idx = $a[$i]; 
                            //echo $idx . "<br>";    
            ?>

                            <tr>
                                <td><span>{{ $a[$i] }}</span></td>
                                <td>{{ Form::select("permissions[$idx]", ['0' => 'Inherit', '1' => 'Allow', '-1' => 'Deny'], null, ['class' => 'select2', 'id' => 'superuser1']) }}
                                        {{ Form::hidden('module_perm', $idx) }}
                                </td>
                            </tr>
            <?php       
                     }  ?>
                @endforeach 
            </table>


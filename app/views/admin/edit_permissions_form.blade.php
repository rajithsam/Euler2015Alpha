        <!--table width="50%" cellpadding="10" cellspacing="10"-->
        <table class="table table-striped table-hover table-list display" cellspacing="0" width="100%">
                @foreach( $permissions as $permission) 
                <tr><td colspan="2"><legend>{{ $permission['modulename'] }}</legend></td></tr>
            <?php


                        $permns  = str_replace(array('[',']'),'',$permission['permissions']);
                        $permns2 = str_replace('"','',$permns);
                        $a = explode(',',$permns2);

                        $arr2 = $group->permissions;  
                        $vals = explode(',',$arr2);
                        $json2 = '{' . $arr2 . '}'; //"a":1,"b":2,"c":3,"d":4,"e":5}';
                        $json2 =  $arr2;

                        //var_dump(json_decode($json));
                        $json_arr2 = json_decode($json2, true);
                                        
                        for($i=0;$i<sizeof($a);$i++)
                        { 

                            $idx = $a[$i]; 
                            //echo $idx . "<br>";

                            
            ?> 
                            <tr>
                                <td><span>{{ $a[$i] }}</span></td>
                                <td>{{-- Form::select("permissions[$idx]", ['1' => 'Allow', '-1' => 'Deny'], $val4, ['class' => 'select2', 'id' => 'superuser1']) --}}
                                    <select name="<?php echo "permissions[" . $idx . "]"; ?>">
                                        <option value="1" 
                                        <?php
                                         if(!empty($json_arr2))
                                         {
                                            if(array_key_exists($idx, $json_arr2))
                                            {

                                                //echo $perms['$perm'];
                                                //echo "hello world";
                                                foreach ($json_arr2 as $k => $v) {
                                                    if(strcmp($idx,$k) == 0)
                                                    {
                                                        if($v == 1)
                                                            echo "selected";
                                                    }
                                                     
                                                }
                                            }
                                         }
                                        ?>
                                        >Allow
                                        </option>
                                        <option value="-1" 
                                        <?php
                                         if(!empty($json_arr2))
                                         {
                                            if(array_key_exists($idx, $json_arr2))
                                            {

                                                //echo $perms['$perm'];
                                                //echo "hello world";
                                                foreach ($json_arr2 as $k => $v) {
                                                    if(strcmp($idx,$k) == 0)
                                                    {
                                                        if($v == -1)
                                                            echo "selected";
                                                    }
                                                     
                                                }
                                            }
                                         }
                                        ?>
                                        >Deny
                                        </option> 
                                    </select>                                   
                                    {{ Form::hidden('module_perm', $idx) }}
                                    
                                </td>
                            </tr>
            <?php       
                     }  ?>
                @endforeach 
            </table>

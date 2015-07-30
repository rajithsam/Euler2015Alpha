<?php

class Timesheet extends \Eloquent {
    protected $fillable = [];

    protected $table = 'employee_timesheet';

    public function hasManyOvertime()
    {
        return $this->hasMany('Overtime');
    }


    public function getAllRows($employeeId) {
        
        $timeSheet = DB::table('employee_timesheet')->where('employee_id', $employeeId)->get();
        return $timeSheet;

    } 

    public function getTimesheetById($employeeId, $dayDate) {

        $timeSheet = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();

        return $timeSheet;
    
    }    

    public function getDateYesteday($employeeId, $dayDateArr) {
        $employeeId = Session::get('userEmployeeId');
        //$timeSheet = DB::table('employee_timesheet')->select('daydate', 'schedule_in', 'schedule_out', 'time_in_1', 'time_out_1', 'time_in_2', 'time_out_2', 'time_in_3', 'time_out_3', 'total_hours', 'total_overtime', 'work_hours')->where('employee_id', Auth::user()->employee_id)->where('daydate', trim($dayDate))->get();
        $timeSheet = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();
        return $timeSheet;    
    }    

    public function getTimesheetPerMonth($employeeId, $dayDateArr){
        
        $employeeId = Session::get('userEmployeeId');
        //$timeSheet = array();

        //return dd($dayDateArr);
        foreach($dayDateArr as $dayDate) {

            $timeSheet[] = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();                         

        } 

        return $timeSheet;        
    }

    public function getTimesheetJsObject($employeeId) {

        //$timeSheet = DB::table('employee_timesheet')->select('daydate', 'schedule_in', 'schedule_out', 'time_in_1', 'time_out_1', 'time_in_2', 'time_out_2', 'time_in_3', 'time_out_3', 'total_hours', 'total_overtime', 'work_hours', 'tardiness', 'undertime')->where('employee_id', $employeeId)->get();
        $timeSheet = DB::table('employee_timesheet')->where('employee_id', $employeeId)->get();        
        return json_encode($timeSheet);

    }

    public function getTimesheetJsObjectPerMonth($employeeId, $dayDateArr) {
        $data['employeeId'] = Input::get('employeeid');
        
        if( isset($data['employeeId']) && !empty($data['employeeId']) ) {
        
            $employeeId = $data['employeeId'];
        
        } else {

            $employeeId = Session::get('userEmployeeId');

        }

          //$employeeId = Session::get('userEmployeeId');
        
        //$dayDateArr = str_replace(array('<br>', '<br/>', '<br />'), '', $dayDateArr);        
        
        foreach($dayDateArr as $dayDate) {  

            Session::put('dayDate', $dayDate);

            //$timeSheet[] = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();                                                
            $schedule[] = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dayDate))->get();                                           

            $timeSheet[] = DB::table('employee_timesheet')
                                    ->join('overtime', 'employee_timesheet.id', '=', 'overtime.timesheet_id')
                                    ->where('employee_timesheet.employee_id', '=', $employeeId)
                                    ->where('employee_timesheet.daydate', '=', trim($dayDate))        
                                    ->get();              

       
        } 

        //return dd($timeSheet);   

        //Generate Json object for data tables jquery plugin
        //http://www.datatables.net/examples/ajax/objects.html        

        $ctr = 1;
        $output = '{';
        $output .= '"data": [';
        for($i = 0; $i <= sizeof($timeSheet) - 1; $i++) {

            $output .= '{';            
            //http://datatables.net/examples/server_side/ids.html
            $output .= '"DT_RowId": '. '"'.$timeSheet[$i][0]->timesheet_id.'",';
            $output .= '"id": '. '"'.$timeSheet[$i][0]->timesheet_id.'",';

            //Date
            $output .= '"daydate": '. '"'.date('D, M d', strtotime($timeSheet[$i][0]->daydate)).'",';

            //Schedule
            if ( !empty($timeSheet[$i][0]->schedule_in) && !empty($timeSheet[$i][0]->schedule_out) ) {
                $output .= '"schedule": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->schedule_in)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->schedule_out)).'",';             
            } else {
                $output .= '"schedule": '. '"'.'00:00:00 - 00:00:00'.'",';                             
            }
            
            //in-out 1
            /*if ( !empty($timeSheet[$i][0]->time_in_1) && !empty($timeSheet[$i][0]->time_out_1) ) {
             
                $output .= '"in_out_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_1)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_1)).'",';            

            } elseif ( !empty($timeSheet[$i][0]->time_in_1) && empty($timeSheet[$i][0]->time_out_1) ) {

                $output .= '"in_out_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_1)). ' - ' .'",';

            } elseif ( empty($timeSheet[$i][0]->time_in_1) && !empty($timeSheet[$i][0]->time_out_1) ) {

                $output .= '"in_out_1": '. '"'. ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_1)).'",';                            
                
            } else {

                $output .= '"in_out_1": '. '"'. ' - ' .'",';

            }*/

            if ( !empty($timeSheet[$i][0]->time_in_1) ) {

                $output .= '"in_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_1)).'",';                            
                
            } else {

                $output .= '"in_1": '. '"'. ' --:-- ' .'",';

            }

            if ( !empty($timeSheet[$i][0]->time_out_1) ) {

                $output .= '"out_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_out_1)).'",';                            
                
            } else {

                $output .= '"out_1": '. '"'. ' --:-- ' .'",';

            }




            //in-out 2
            /*if ( !empty($timeSheet[$i][0]->time_in_2) && !empty($timeSheet[$i][0]->time_out_2) ) {
             
                $output .= '"in_out_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_2)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_2)).'",';            

            } elseif ( !empty($timeSheet[$i][0]->time_in_2) && empty($timeSheet[$i][0]->time_out_2) ) {

                $output .= '"in_out_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_2)). ' - ' .'",';
                
            } elseif ( empty($timeSheet[$i][0]->time_in_2) && !empty($timeSheet[$i][0]->time_out_2) ) {

                $output .= '"in_out_2": '. '"'. ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_2)).'",';                            
                
            } else {

                $output .= '"in_out_2": '. '"'. ' - ' .'",';

            }*/


            if ( !empty($timeSheet[$i][0]->time_in_2) ) {

                $output .= '"in_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_2)).'",';                            
                
            } else {

                $output .= '"in_2": '. '"'. ' --:-- ' .'",';

            }

            if ( !empty($timeSheet[$i][0]->time_out_2) ) {

                $output .= '"out_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_out_2)).'",';                            
                
            } else {

                $output .= '"out_2": '. '"'. ' --:-- ' .'",';

            }                      

            //in-out 3
            /*if ( !empty($timeSheet[$i][0]->time_in_3) && !empty($timeSheet[$i][0]->time_out_3) ) {
             
                $output .= '"in_out_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_3)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_3)).'",';            

            } elseif ( !empty($timeSheet[$i][0]->time_in_3) && empty($timeSheet[$i][0]->time_out_3) ) {

                $output .= '"in_out_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_3)). ' - ' .'",';
                
            } elseif ( empty($timeSheet[$i][0]->time_in_3) && !empty($timeSheet[$i][0]->time_out_3) ) {

                $output .= '"in_out_3": '. '"'. ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_3)).'",';                            
                
            } else {

                $output .= '"in_out_3": '. '"'. ' - ' .'",';

            }*/  

            if ( !empty($timeSheet[$i][0]->time_in_3) ) {

                $output .= '"in_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_3)).'",';                            
                
            } else {

                $output .= '"in_3": '. '"'. ' --:-- ' .'",';

            }

            if ( !empty($timeSheet[$i][0]->time_out_3) ) {

                $output .= '"out_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_out_3)).'",';                            
                
            } else {

                $output .= '"out_3": '. '"'. ' --:-- ' .'",';

            }            

            //Night Differential          
            if ( !empty($timeSheet[$i][0]->night_differential_1) && !empty($timeSheet[$i][0]->night_differential_3) ) {                

                $output .= '"night_differential": '. '"'. getTotal($timeSheet[$i][0]->night_differential_1, $timeSheet[$i][0]->night_differential_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->night_differential_2) && !empty($timeSheet[$i][0]->night_differential_3) ) {                

                //$output .= '"night_differential": '. '"'. getTotal($timeSheet[$i][0]->night_differential_1, $timeSheet[$i][0]->night_differential_3).'",';                
                $output .= '"night_differential": '. '"'. getTotal($timeSheet[$i][0]->night_differential_2, $timeSheet[$i][0]->night_differential_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->night_differential_1) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timeSheet[$i][0]->night_differential_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->night_differential_2) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timeSheet[$i][0]->night_differential_2, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->night_differential_3) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timeSheet[$i][0]->night_differential_3, 2, '.', '').'",';                                

            } else {

                $output .= '"night_differential": '. '"'. ' - ' .'",';

            }


            //Total Hours           
            if ( !empty($timeSheet[$i][0]->total_hours_1) && !empty($timeSheet[$i][0]->total_hours_3) ) {                

                $output .= '"total_hours": '. '"'. getTotal($timeSheet[$i][0]->total_hours_1, $timeSheet[$i][0]->total_hours_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->total_hours_2) && !empty($timeSheet[$i][0]->total_hours_3) ) {                

                //$output .= '"total_hours": '. '"'. getTotal($timeSheet[$i][0]->total_hours_1, $timeSheet[$i][0]->total_hours_3).'",';                
                $output .= '"total_hours": '. '"'. getTotal($timeSheet[$i][0]->total_hours_2, $timeSheet[$i][0]->total_hours_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->total_hours_1) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timeSheet[$i][0]->total_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_hours_2) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timeSheet[$i][0]->total_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"total_hours": '. '"'. ' - ' .'",';

            }

            //Work Hours
            if ( !empty($timeSheet[$i][0]->work_hours_1) && !empty($timeSheet[$i][0]->work_hours_3) ) {                

                $output .= '"work_hours": '. '"'. getTotal($timeSheet[$i][0]->work_hours_1, $timeSheet[$i][0]->work_hours_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->work_hours_2) && !empty($timeSheet[$i][0]->work_hours_3) ) {                

                //$output .= '"work_hours": '. '"'. getTotal($timeSheet[$i][0]->work_hours_1, $timeSheet[$i][0]->work_hours_3).'",';                
                $output .= '"work_hours": '. '"'. getTotal($timeSheet[$i][0]->work_hours_2, $timeSheet[$i][0]->work_hours_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->work_hours_1) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timeSheet[$i][0]->work_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->work_hours_2) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timeSheet[$i][0]->work_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"work_hours": '. '"'. ' - ' .'",';

            }

            //Total Overtime
            if ( !empty($timeSheet[$i][0]->total_overtime_1) && !empty($timeSheet[$i][0]->total_overtime_3) ) {                

                $output .= '"total_overtime": '. '"'. getTotal($timeSheet[$i][0]->total_overtime_1, $timeSheet[$i][0]->total_overtime_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_2) && !empty($timeSheet[$i][0]->total_overtime_3) ) {                

                //$output .= '"total_overtime": '. '"'. getTotal($timeSheet[$i][0]->total_overtime_1, $timeSheet[$i][0]->total_overtime_3).'",';                
                $output .= '"total_overtime": '. '"'. getTotal($timeSheet[$i][0]->total_overtime_2, $timeSheet[$i][0]->total_overtime_3).'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_1) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timeSheet[$i][0]->total_overtime_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_2) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timeSheet[$i][0]->total_overtime_2, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_3) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timeSheet[$i][0]->total_overtime_3, 2, '.', '').'",';                

            } else {

                $output .= '"total_overtime": '. '"'. ' - ' .'",';

            }

            //Tardiness
            if ( !empty($timeSheet[$i][0]->tardiness_1) && !empty($timeSheet[$i][0]->tardiness_3) ) {                

                $output .= '"tardiness": '. '"'. getTotal($timeSheet[$i][0]->tardiness_1, $timeSheet[$i][0]->tardiness_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->tardiness_2) && !empty($timeSheet[$i][0]->tardiness_3) ) {                

                //$output .= '"tardiness": '. '"'. getTotal($timeSheet[$i][0]->tardiness_1, $timeSheet[$i][0]->tardiness_3).'",';                
                $output .= '"tardiness": '. '"'. getTotal($timeSheet[$i][0]->tardiness_2, $timeSheet[$i][0]->tardiness_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->tardiness_1) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timeSheet[$i][0]->tardiness_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->tardiness_2) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timeSheet[$i][0]->tardiness_2, 2, '.', '').'",';                

            } else {

                $output .= '"tardiness": '. '"'. ' - ' .'",';

            }

            //Undertime
            if ( !empty($timeSheet[$i][0]->undertime_1) && !empty($timeSheet[$i][0]->undertime_3) ) {                

                $output .= '"undertime": '. '"'. getTotal($timeSheet[$i][0]->undertime_1, $timeSheet[$i][0]->undertime_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->undertime_2) && !empty($timeSheet[$i][0]->undertime_3) ) {                

                //$output .= '"undertime": '. '"'. getTotal($timeSheet[$i][0]->undertime_1, $timeSheet[$i][0]->undertime_3).'",';                
                $output .= '"undertime": '. '"'. getTotal($timeSheet[$i][0]->undertime_2, $timeSheet[$i][0]->undertime_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->undertime_1) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timeSheet[$i][0]->undertime_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->undertime_2) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timeSheet[$i][0]->undertime_2, 2, '.', '').'",';                

            } else {

                $output .= '"undertime": '. '"'. ' - ' .'",';

            }            

            /*otStatus

            -1 applied ot
            0 denied
            1 approved*/

            /*$overtimeStatus1 = (string) $timeSheet[$i][0]->overtime_status_1;
            $overtimeStatus2 = (string) $timeSheet[$i][0]->overtime_status_2;
            $overtimeStatus3 = (string) $timeSheet[$i][0]->overtime_status_3;*/

            /*if( !empty($timeSheet[$i][0]->total_overtime_1) && 
                is_null($timeSheet[$i][0]->overtime_status_1) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.gettype($timeSheet[$i][0]->overtime_status_1).'"'; 

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === -1) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Applied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"'; 

            } elseif( !empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 0) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Denied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } elseif( !empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 1) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Approved OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } else {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';

            } */


            if( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                is_null($timeSheet[$i][0]->overtime_status_1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                is_null($timeSheet[$i][0]->overtime_status_2)) ||

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                is_null($timeSheet[$i][0]->overtime_status_1)) && 

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                is_null($timeSheet[$i][0]->overtime_status_3)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                is_null($timeSheet[$i][0]->overtime_status_2)) && 

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                is_null($timeSheet[$i][0]->overtime_status_3)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"'; 

            } elseif ( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === -1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === -1)) ||            

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === -1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === -1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === -1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === -1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Applied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"'; 

            } elseif ( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 0)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 0)) ||            

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 0)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 0)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 0)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 0)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Denied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } elseif ( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 1)) ||            

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Approved OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } else {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';

            }                       


            if ( $ctr == sizeof($timeSheet) ) {
                $output .= '}';
            } else {
                $output .= '},';
            }

            $ctr++;
        }
        $output .= ']';
        $output .= '}';        

        return (string) $output; //json_encode($timeSheetObj);

    }




    public function getSearchTimesheetJsObjectPerMonth($employeeId, $dayDateArr) {
        //$employeeId = Session::get('userEmployeeId');
        //$dayDateArr = str_replace(array('<br>', '<br/>', '<br />'), '', $dayDateArr);        

        foreach($dayDateArr as $dayDate) {  

            Session::put('dayDate', $dayDate);

            //$timeSheet[] = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();                                                
            $schedule[] = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dayDate))->get();                                           

            $timeSheet[] = DB::table('employee_timesheet')
                                    ->join('overtime', 'employee_timesheet.id', '=', 'overtime.timesheet_id')
                                    ->where('employee_timesheet.employee_id', '=', $employeeId)
                                    ->where('employee_timesheet.daydate', '=', trim($dayDate))        
                                    ->get();              

       
        } 


        //Generate Json object for data tables jquery plugin
        //http://www.datatables.net/examples/ajax/objects.html        

        $ctr = 1;
        $output = '{';
        $output .= '"data": [';
        for($i = 0; $i <= sizeof($timeSheet) - 1; $i++) {

            $output .= '{';            
            //http://datatables.net/examples/server_side/ids.html
            $output .= '"DT_RowId": '. '"'.$timeSheet[$i][0]->timesheet_id.'",';
            $output .= '"id": '. '"'.$timeSheet[$i][0]->timesheet_id.'",';

            //Date
            $output .= '"daydate": '. '"'.date('D, M d', strtotime($timeSheet[$i][0]->daydate)).'",';

            //Schedule
            if ( !empty($timeSheet[$i][0]->schedule_in) && !empty($timeSheet[$i][0]->schedule_out) ) {
                $output .= '"schedule": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->schedule_in)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->schedule_out)).'",';             
            } else {
                $output .= '"schedule": '. '"'.'00:00:00 - 00:00:00'.'",';                             
            }
            
            //in-out 1
            /*if ( !empty($timeSheet[$i][0]->time_in_1) && !empty($timeSheet[$i][0]->time_out_1) ) {
             
                $output .= '"in_out_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_1)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_1)).'",';            

            } elseif ( !empty($timeSheet[$i][0]->time_in_1) && empty($timeSheet[$i][0]->time_out_1) ) {

                $output .= '"in_out_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_1)). ' - ' .'",';

            } elseif ( empty($timeSheet[$i][0]->time_in_1) && !empty($timeSheet[$i][0]->time_out_1) ) {

                $output .= '"in_out_1": '. '"'. ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_1)).'",';                            
                
            } else {

                $output .= '"in_out_1": '. '"'. ' - ' .'",';

            }*/

            if ( !empty($timeSheet[$i][0]->time_in_1) ) {

                $output .= '"in_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_1)).'",';                            
                
            } /*else {

                $output .= '"in_1": '. '"'. ' --:-- ' .'",';

            }*/

            if ( !empty($timeSheet[$i][0]->time_out_1) ) {

                $output .= '"out_1": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_out_1)).'",';                            
                
            } /*else {

                $output .= '"out_1": '. '"'. ' --:-- ' .'",';

            }*/




            //in-out 2
            /*if ( !empty($timeSheet[$i][0]->time_in_2) && !empty($timeSheet[$i][0]->time_out_2) ) {
             
                $output .= '"in_out_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_2)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_2)).'",';            

            } elseif ( !empty($timeSheet[$i][0]->time_in_2) && empty($timeSheet[$i][0]->time_out_2) ) {

                $output .= '"in_out_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_2)). ' - ' .'",';
                
            } elseif ( empty($timeSheet[$i][0]->time_in_2) && !empty($timeSheet[$i][0]->time_out_2) ) {

                $output .= '"in_out_2": '. '"'. ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_2)).'",';                            
                
            } else {

                $output .= '"in_out_2": '. '"'. ' - ' .'",';

            }*/


            if ( !empty($timeSheet[$i][0]->time_in_2) ) {

                $output .= '"in_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_2)).'",';                            
                
            } /*else {

                $output .= '"in_2": '. '"'. ' --:-- ' .'",';

            }*/

            if ( !empty($timeSheet[$i][0]->time_out_2) ) {

                $output .= '"out_2": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_out_2)).'",';                            
                
            } /*else {

                $output .= '"out_2": '. '"'. ' --:-- ' .'",';

            }*/                      

            //in-out 3
            /*if ( !empty($timeSheet[$i][0]->time_in_3) && !empty($timeSheet[$i][0]->time_out_3) ) {
             
                $output .= '"in_out_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_3)). ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_3)).'",';            

            } elseif ( !empty($timeSheet[$i][0]->time_in_3) && empty($timeSheet[$i][0]->time_out_3) ) {

                $output .= '"in_out_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_3)). ' - ' .'",';
                
            } elseif ( empty($timeSheet[$i][0]->time_in_3) && !empty($timeSheet[$i][0]->time_out_3) ) {

                $output .= '"in_out_3": '. '"'. ' - ' .date('H:i', strtotime($timeSheet[$i][0]->time_out_3)).'",';                            
                
            } else {

                $output .= '"in_out_3": '. '"'. ' - ' .'",';

            }*/  

            if ( !empty($timeSheet[$i][0]->time_in_3) ) {

                $output .= '"in_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_in_3)).'",';                            
                
            } /*else {

                $output .= '"in_3": '. '"'. ' --:-- ' .'",';

            }*/

            if ( !empty($timeSheet[$i][0]->time_out_3) ) {

                $output .= '"out_3": '. '"'.date('H:i', strtotime($timeSheet[$i][0]->time_out_3)).'",';                            
                
            } /*else {

                $output .= '"out_3": '. '"'. ' --:-- ' .'",';

            }*/

            //Night Differential          
            if ( !empty($timeSheet[$i][0]->night_differential_1) && !empty($timeSheet[$i][0]->night_differential_3) ) {                

                $output .= '"night_differential": '. '"'. getTotal($timeSheet[$i][0]->night_differential_1, $timeSheet[$i][0]->night_differential_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->night_differential_2) && !empty($timeSheet[$i][0]->night_differential_3) ) {                

                //$output .= '"night_differential": '. '"'. getTotal($timeSheet[$i][0]->night_differential_1, $timeSheet[$i][0]->night_differential_3).'",';                
                $output .= '"night_differential": '. '"'. getTotal($timeSheet[$i][0]->night_differential_2, $timeSheet[$i][0]->night_differential_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->night_differential_1) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timeSheet[$i][0]->night_differential_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->night_differential_2) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timeSheet[$i][0]->night_differential_2, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->night_differential_3) ) {

                $output .= '"night_differential": '. '"'.number_format((double) $timeSheet[$i][0]->night_differential_3, 2, '.', '').'",';                                

            } else {

                $output .= '"night_differential": '. '"'. ' - ' .'",';

            }          


            //Total Hours           
            if ( !empty($timeSheet[$i][0]->total_hours_1) && !empty($timeSheet[$i][0]->total_hours_3) ) {                

                $output .= '"total_hours": '. '"'. getTotal($timeSheet[$i][0]->total_hours_1, $timeSheet[$i][0]->total_hours_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->total_hours_2) && !empty($timeSheet[$i][0]->total_hours_3) ) {                

                //$output .= '"total_hours": '. '"'. getTotal($timeSheet[$i][0]->total_hours_1, $timeSheet[$i][0]->total_hours_3).'",';                
                $output .= '"total_hours": '. '"'. getTotal($timeSheet[$i][0]->total_hours_2, $timeSheet[$i][0]->total_hours_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->total_hours_1) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timeSheet[$i][0]->total_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_hours_2) ) {

                $output .= '"total_hours": '. '"'.number_format((double) $timeSheet[$i][0]->total_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"total_hours": '. '"'. ' - ' .'",';

            }

            //Work Hours
            if ( !empty($timeSheet[$i][0]->work_hours_1) && !empty($timeSheet[$i][0]->work_hours_3) ) {                

                $output .= '"work_hours": '. '"'. getTotal($timeSheet[$i][0]->work_hours_1, $timeSheet[$i][0]->work_hours_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->work_hours_2) && !empty($timeSheet[$i][0]->work_hours_3) ) {                

                //$output .= '"work_hours": '. '"'. getTotal($timeSheet[$i][0]->work_hours_1, $timeSheet[$i][0]->work_hours_3).'",';                
                $output .= '"work_hours": '. '"'. getTotal($timeSheet[$i][0]->work_hours_2, $timeSheet[$i][0]->work_hours_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->work_hours_1) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timeSheet[$i][0]->work_hours_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->work_hours_2) ) {

                $output .= '"work_hours": '. '"'.number_format((double) $timeSheet[$i][0]->work_hours_2, 2, '.', '').'",';                

            } else {

                $output .= '"work_hours": '. '"'. ' - ' .'",';

            }

            //Total Overtime
            if ( !empty($timeSheet[$i][0]->total_overtime_1) && !empty($timeSheet[$i][0]->total_overtime_3) ) {                

                $output .= '"total_overtime": '. '"'. getTotal($timeSheet[$i][0]->total_overtime_1, $timeSheet[$i][0]->total_overtime_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_2) && !empty($timeSheet[$i][0]->total_overtime_3) ) {                

                //$output .= '"total_overtime": '. '"'. getTotal($timeSheet[$i][0]->total_overtime_1, $timeSheet[$i][0]->total_overtime_3).'",';                
                $output .= '"total_overtime": '. '"'. getTotal($timeSheet[$i][0]->total_overtime_2, $timeSheet[$i][0]->total_overtime_3).'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_1) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timeSheet[$i][0]->total_overtime_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_2) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timeSheet[$i][0]->total_overtime_2, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_3) ) {

                $output .= '"total_overtime": '. '"'.number_format((double) $timeSheet[$i][0]->total_overtime_3, 2, '.', '').'",';                

            } else {

                $output .= '"total_overtime": '. '"'. ' - ' .'",';

            }

            //Tardiness
            if ( !empty($timeSheet[$i][0]->tardiness_1) && !empty($timeSheet[$i][0]->tardiness_3) ) {                

                $output .= '"tardiness": '. '"'. getTotal($timeSheet[$i][0]->tardiness_1, $timeSheet[$i][0]->tardiness_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->tardiness_2) && !empty($timeSheet[$i][0]->tardiness_3) ) {                

                //$output .= '"tardiness": '. '"'. getTotal($timeSheet[$i][0]->tardiness_1, $timeSheet[$i][0]->tardiness_3).'",';                
                $output .= '"tardiness": '. '"'. getTotal($timeSheet[$i][0]->tardiness_2, $timeSheet[$i][0]->tardiness_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->tardiness_1) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timeSheet[$i][0]->tardiness_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->tardiness_2) ) {

                $output .= '"tardiness": '. '"'.number_format((double) $timeSheet[$i][0]->tardiness_2, 2, '.', '').'",';                

            } else {

                $output .= '"tardiness": '. '"'. ' - ' .'",';

            }

            //Undertime
            if ( !empty($timeSheet[$i][0]->undertime_1) && !empty($timeSheet[$i][0]->undertime_3) ) {                

                $output .= '"undertime": '. '"'. getTotal($timeSheet[$i][0]->undertime_1, $timeSheet[$i][0]->undertime_3).'",';

            } elseif ( !empty($timeSheet[$i][0]->undertime_2) && !empty($timeSheet[$i][0]->undertime_3) ) {                

                //$output .= '"undertime": '. '"'. getTotal($timeSheet[$i][0]->undertime_1, $timeSheet[$i][0]->undertime_3).'",';                
                $output .= '"undertime": '. '"'. getTotal($timeSheet[$i][0]->undertime_2, $timeSheet[$i][0]->undertime_3).'",';                                

            } elseif ( !empty($timeSheet[$i][0]->undertime_1) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timeSheet[$i][0]->undertime_1, 2, '.', '').'",';                

            } elseif ( !empty($timeSheet[$i][0]->undertime_2) ) {

                $output .= '"undertime": '. '"'.number_format((double) $timeSheet[$i][0]->undertime_2, 2, '.', '').'",';                

            } else {

                $output .= '"undertime": '. '"'. ' - ' .'",';

            }

            /*otStatus
            -1 applied ot
            0 denied
            1 approved*/

            /*$overtimeStatus1 = (string) $timeSheet[$i][0]->overtime_status_1;
            $overtimeStatus2 = (string) $timeSheet[$i][0]->overtime_status_2;
            $overtimeStatus3 = (string) $timeSheet[$i][0]->overtime_status_3;*/

            /*if( !empty($timeSheet[$i][0]->total_overtime_1) && 
                is_null($timeSheet[$i][0]->overtime_status_1) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.gettype($timeSheet[$i][0]->overtime_status_1).'"'; 

            } elseif ( !empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === -1) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Applied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"'; 

            } elseif( !empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 0) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Denied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } elseif( !empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 1) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Approved OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } else {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';

            } */


            if( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                is_null($timeSheet[$i][0]->overtime_status_1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                is_null($timeSheet[$i][0]->overtime_status_2)) ||

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                is_null($timeSheet[$i][0]->overtime_status_1)) && 

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                is_null($timeSheet[$i][0]->overtime_status_3)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                is_null($timeSheet[$i][0]->overtime_status_2)) && 

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                is_null($timeSheet[$i][0]->overtime_status_3)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\">Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"'; 

            } elseif ( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === -1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === -1)) ||            

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === -1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === -1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === -1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === -1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Applied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"'; 

            } elseif ( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 0)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 0)) ||            

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 0)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 0)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 0)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 0)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Denied OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } elseif ( (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 1)) ||            

                (!empty($timeSheet[$i][0]->total_overtime_1) && 
                (!is_null($timeSheet[$i][0]->overtime_status_1) &&
                $timeSheet[$i][0]->overtime_status_1 === 1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 1)) ||

                (!empty($timeSheet[$i][0]->total_overtime_2) && 
                (!is_null($timeSheet[$i][0]->overtime_status_2) &&
                $timeSheet[$i][0]->overtime_status_2 === 1)) &&

                (!empty($timeSheet[$i][0]->total_overtime_3) && 
                (!is_null($timeSheet[$i][0]->overtime_status_3) &&
                $timeSheet[$i][0]->overtime_status_3 === 1)) ) {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Approved OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';                 

            } else {

                $cellContent = '<button class=\"ot-apply-btn btn btn-success\" type=\"button\" disabled>Apply OT</button>';                    
                $output .= '"overtime_status": '. '"'.$cellContent.'"';

            }                       


            if ( $ctr == sizeof($timeSheet) ) {
                $output .= '}';
            } else {
                $output .= '},';
            }

            $ctr++;
        }
        $output .= ']';
        $output .= '}';        

        return (string) $output; //json_encode($timeSheetObj);

    }

    public function getEmployeeByEmployeeIdandDate($employeeId, $dayDate) {
        $employeeId = Session::get('userEmployeeId');
        //return Timesheet::where('employee_id', '=', Auth::user()->employee_id)->where('daydate', '=', date('Y-m-d'))->first();
        //return DB::table('employee_timesheet')->where('employee_id', '=', Auth::user()->employee_id)->where('daydate', '=', date('Y-m-d'))->first();
        return Timesheet::where('employee_id', '=', $employeeId)->where('daydate', '=', date('Y-m-d'))->first();        

    }    


    public function getTimesheetYesterday($employeeId, $yesterDayDate) {
        $employeeId = Session::get('userEmployeeId');
        return Timesheet::where('employee_id', $employeeId)->where('daydate', $yesterDayDate)->get();

    }

    public function getEmployeeNightDiffClocking($employeeId, $yesterDayDate) {
        $employeeId = Session::get('userEmployeeId');
        return Timesheet::where('employee_id', '=', $employeeId)->where('daydate', '=', $yesterDayDate)->first();

    }

}
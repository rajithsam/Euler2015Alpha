<?php

class Summary extends \Eloquent {
	protected $fillable = [];

    protected $table = 'employee_summary';


    public function getSummaryCutoff($employeeId, $dayDateArr) {

        /*$data['employeeId'] = Input::get('employeeid');
        
        if( isset($data['employeeId']) && !empty($data['employeeId']) ) {
        
            $employeeId = $data['employeeId'];
        
        } else {

            $employeeId = Session::get('userEmployeeId');

        }*/

		
	   $employeeId = Session::get('userEmployeeId');
	   $userId = Session::get('userId');

       $currentDate = date('Y-m-d');

      //$dayDateArr = str_replace(array('<br>', '<br/>', '<br />'), '', $dayDateArr);        

      $leaves = Leave::where('employee_id', $employeeId)->get();

      foreach($leaves as $leave) {

        // Start date
        $fromDate = $leave->from_date;
        // End date
        $toDate = $leave->to_date;                      

        $leaveDateArr = array($fromDate, $toDate);

        if ( (1 === (int) $leave->status) ) { //Aprroved

            //Paid Sick Leave
            if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

                $update = array('paid_sick_leave' => number_format(8, 2));

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            //Paid Vacation Leave
            } elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

                $update = array('paid_vacation_leave' => number_format(8, 2));

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            //Marternity Leave
            } elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {                                            

                $update = array('maternity_leave' => number_format(8, 2));

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            //Paternity Leave
            } elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {                                            

                $update = array('paternity_leave' => number_format(8, 2));

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);

            //Leave Without Pay
            } elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {                                          

                $update = array('leave_without_pay' => number_format(8, 2));

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            }            


        } 

        /*if ( (0 === (int) $leave->status) ) { //Denied


            //Paid Sick Leave
            if ( 'sick leave' === strtolower($leave->nature_of_leave) ) {

                $update = array('paid_sick_leave' => '');

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            //Paid Vacation Leave
            } elseif ( 'vacation leave' === strtolower($leave->nature_of_leave) ) {

                $update = array('paid_vacation_leave' => '');

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            //Marternity Leave
            } elseif ( 'maternity leave' === strtolower($leave->nature_of_leave) ) {                                            

                $update = array('maternity_leave' => '');

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            //Paternity Leave
            } elseif ( 'paternity leave' === strtolower($leave->nature_of_leave) ) {                                            

                $update = array('paternity_leave' => '');

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);

            //Leave Without Pay
            } elseif ( 'leave without Pay' === strtolower($leave->nature_of_leave) ) {                                          

                $update = array('leave_without_pay' => '');

                DB::table('employee_summary')
                    ->where('employee_id', $employeeId)
                    ->whereBetween('daydate', $leaveDateArr)
                    ->update($update);                                          

            }             


        }*/

      }

      foreach($dayDateArr as $dayDate) {                                

        //$dateDate = new DateTime($dayDate);
        //$dateDate->modify("-1 month"); //Debug
        //$dateDate->format('Y-m-d');

        $timeSheet[] = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();                                                            
        //$schedule[] = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dateDate->format('Y-m-d')))->get();        

        $summary[] = DB::table('employee_summary')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();                                                                    

      } 

      for($i = 0; $i <= sizeof($timeSheet) - 1; $i++) {

        //SUMMARY: 1st Column
        $latesArr[] = $summary[$i][0]->lates;  
        $underTimeArr[] = $summary[$i][0]->undertime;  

        $absentArr[] = $summary[$i][0]->absent;  
        $paidSickLeaveArr[] = $summary[$i][0]->paid_sick_leave;

        $paidVacationLeaveArr[] = $summary[$i][0]->paid_vacation_leave;  
        $leaveWithoutPayArr[] = $summary[$i][0]->leave_without_pay;

        $maternityLeaveArr[] = $summary[$i][0]->maternity_leave;  
        $paternityLeaveArr[] = $summary[$i][0]->paternity_leave;                        

        //SUMMARY: 2nd Column
        $regularArr[] = $summary[$i][0]->regular;
        $regularOtArr[] = $summary[$i][0]->regular_overtime;
        $restDayArr[] = $summary[$i][0]->rest_day;
        $restDayOtArr[] = $summary[$i][0]->rest_day_overtime;

        //With Night Diff
        $regularOtNdArr[] = $summary[$i][0]->regular_overtime_night_diff;
        $regularNdArr[] = $summary[$i][0]->regular_night_differential;    
        $restDayOtNdArr[] = $summary[$i][0]->rest_day_overtime_night_diff;        
        $restDayNdArr[] = $summary[$i][0]->rest_day_night_differential;

        //SUMMARY: 3rd Column
        $restDaySpecialHolidayArr[] = $summary[$i][0]->rest_day_special_holiday;
        $restDaySpecialHolidayOtArr[] = $summary[$i][0]->rest_day_special_holiday_overtime;
        $restDayLegalHolidayArr[] = $summary[$i][0]->rest_day_legal_holiday;
        $restDayLegalHolidayOtArr[] = $summary[$i][0]->rest_day_legal_holiday_overtime;

        //With Night Diff
        $restDaySpecialHolidayOtNdArr[] = $summary[$i][0]->rest_day_special_holiday_overtime_night_diff;
        $restDaySpecialHolidayNdArr[] = $summary[$i][0]->rest_day_special_holiday_night_diff;
        $restDayLegalHolidayOtNdArr[] = $summary[$i][0]->rest_day_legal_holiday_overtime_night_diff;
        $restDayLegalHolidayNdArr[] = $summary[$i][0]->rest_day_legal_holiday_night_diff;

        //SUMMARY: 4th Column
        $specialHolidayArr[] = $summary[$i][0]->special_holiday;
        $specialHolidayOtArr[] = $summary[$i][0]->special_holiday_overtime;
        $legalHolidayArr[] = $summary[$i][0]->legal_holiday;
        $legalHolidayOtArr[] = $summary[$i][0]->legal_holiday_overtime;

        //With Night Diff
        $specialHolidayOtNdArr[] = $summary[$i][0]->special_holiday_overtime_night_diff;
        $specialHolidayNdArr[] = $summary[$i][0]->special_holiday_night_diff;
        $legalHolidayOtNdArr[] = $summary[$i][0]->legal_holiday_overtime_night_diff;
        $legalHolidayNdArr[] = $summary[$i][0]->legal_holiday_night_diff;

      }

        $output = '{';
        $output .= '"data": [';
        $output .= '{'; 

       //SUMMARY: 1st Column
        $output .= '"tardiness": '. '"'.cutoffTotal($latesArr).'",';                
        $output .= '"undertime": '. '"'.cutoffTotal($underTimeArr).'",';

        $output .= '"absences": '. '"'.cutoffTotal($absentArr).'",';                
        $output .= '"paid_vacation_leave": '. '"'.cutoffTotal($paidVacationLeaveArr).'",';

        $output .= '"paid_sick_leave": '. '"'.cutoffTotal($paidSickLeaveArr).'",';
        $output .= '"leave_without_pay": '. '"'.cutoffTotal($leaveWithoutPayArr).'",';                
        
        $output .= '"maternity_leave": '. '"'.cutoffTotal($maternityLeaveArr).'",';                
        $output .= '"paternity_leave": '. '"'.cutoffTotal($paternityLeaveArr).'",';        

        //SUMMARY: 2nd Column
        $output .= '"regular": '. '"'.cutoffTotal($regularArr).'",';                
        $output .= '"regular_overtime": '. '"'.cutoffTotal($regularOtArr).'",';                
        $output .= '"rest_day": '. '"'.cutoffTotal($restDayArr).'",';                
        $output .= '"rest_day_overtime": '. '"'.cutoffTotal($restDayOtArr).'",';

        //With Night Diff
        $output .= '"regular_overtime_night_diff": '. '"'.cutoffTotal($regularOtNdArr).'",';                
        $output .= '"regular_night_differential": '. '"'.cutoffTotal($regularNdArr).'",';                
        $output .= '"rest_day_overtime_night_diff": '. '"'.cutoffTotal($restDayOtNdArr).'",';        
        $output .= '"rest_day_night_diff": '. '"'.cutoffTotal($restDayNdArr).'",';                

        //SUMMARY: 3rd Column                                
        $output .= '"rest_day_special_holiday": '. '"'.cutoffTotal($restDaySpecialHolidayArr).'",';                
        $output .= '"rest_day_special_holiday_overtime": '. '"'.cutoffTotal($restDaySpecialHolidayOtArr).'",';                
        $output .= '"rest_day_legal_holiday": '. '"'.cutoffTotal($restDayLegalHolidayArr).'",';        
        $output .= '"rest_day_legal_holiday_overtime": '. '"'.cutoffTotal($restDayLegalHolidayOtArr).'",';        

        //With Night Diff
        $output .= '"rest_day_special_holiday_overtime_night_diff": '. '"'.cutoffTotal($restDaySpecialHolidayOtNdArr).'",';                
        $output .= '"rest_day_special_holiday_night_diff": '. '"'.cutoffTotal($restDaySpecialHolidayNdArr).'",';                
        $output .= '"rest_day_legal_holiday_overtime_night_diff": '. '"'.cutoffTotal($restDayLegalHolidayOtNdArr).'",';        
        $output .= '"rest_day_legal_holiday_night_diff": '. '"'.cutoffTotal($restDayLegalHolidayNdArr).'",';                

        //SUMMARY: 4th Column                                
        $output .= '"special_holiday": '. '"'.cutoffTotal($specialHolidayArr).'",';                
        $output .= '"special_holiday_overtime": '. '"'.cutoffTotal($specialHolidayOtArr).'",';                
        $output .= '"legal_holiday": '. '"'.cutoffTotal($legalHolidayArr).'",';        
        $output .= '"legal_holiday_overtime": '. '"'.cutoffTotal($legalHolidayOtArr).'",';

        //With Night Diff
        $output .= '"special_holiday_overtime_night_diff": '. '"'.cutoffTotal($specialHolidayOtNdArr).'",';                
        $output .= '"special_holiday_night_diff": '. '"'.cutoffTotal($specialHolidayNdArr).'",';                
        $output .= '"legal_holiday_overtime_night_diff": '. '"'.cutoffTotal($legalHolidayOtNdArr).'",';        
        $output .= '"legal_holiday_night_diff": '. '"'.cutoffTotal($legalHolidayNdArr).'"';                                     

        $output .= '}'; 
        $output .= ']';
        $output .= '}';        

        return (string) $output; //json_encode($timeSheetObj);      

    }

    public function getSearchSummaryCutoff($employeeId, $dayDateArr) {
        
        //$employeeId = Session::get('userEmployeeId');
        //$userId = Session::get('userId');

      //$dayDateArr = str_replace(array('<br>', '<br/>', '<br />'), '', $dayDateArr);        

      foreach($dayDateArr as $dayDate) {                                

        //$dateDate = new DateTime($dayDate);
        //$dateDate->modify("-1 month"); //Debug
        //$dateDate->format('Y-m-d');

        $timeSheet[] = DB::table('employee_timesheet')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();                                                            
        //$schedule[] = DB::table('employee_schedule')->where('employee_id', $employeeId)->where('schedule_date', trim($dateDate->format('Y-m-d')))->get();        

        $summary[] = DB::table('employee_summary')->where('employee_id', $employeeId)->where('daydate', trim($dayDate))->get();                                                                    

      } 

      for($i = 0; $i <= sizeof($timeSheet) - 1; $i++) {

        //SUMMARY: 1st Column
        $latesArr[] = $summary[$i][0]->lates;  
        $underTimeArr[] = $summary[$i][0]->undertime;  

        $absentArr[] = $summary[$i][0]->absent;  
        $paidSickLeaveArr[] = $summary[$i][0]->paid_sick_leave;

        $paidVacationLeaveArr[] = $summary[$i][0]->paid_vacation_leave;  
        $leaveWithoutPayArr[] = $summary[$i][0]->leave_without_pay;

        $maternityLeaveArr[] = $summary[$i][0]->maternity_leave;  
        $paternityLeaveArr[] = $summary[$i][0]->paternity_leave;                        

        //SUMMARY: 2nd Column
        $regularArr[] = $summary[$i][0]->regular;
        $regularOtArr[] = $summary[$i][0]->regular_overtime;
        $restDayArr[] = $summary[$i][0]->rest_day;
        $restDayOtArr[] = $summary[$i][0]->rest_day_overtime;

        //With Night Diff
        $regularOtNdArr[] = $summary[$i][0]->regular_overtime_night_diff;
        $regularNdArr[] = $summary[$i][0]->regular_night_differential;    
        $restDayOtNdArr[] = $summary[$i][0]->rest_day_overtime_night_diff;        
        $restDayNdArr[] = $summary[$i][0]->rest_day_night_differential;

        //SUMMARY: 3rd Column
        $restDaySpecialHolidayArr[] = $summary[$i][0]->rest_day_special_holiday;
        $restDaySpecialHolidayOtArr[] = $summary[$i][0]->rest_day_special_holiday_overtime;
        $restDayLegalHolidayArr[] = $summary[$i][0]->rest_day_legal_holiday;
        $restDayLegalHolidayOtArr[] = $summary[$i][0]->rest_day_legal_holiday_overtime;

        //With Night Diff
        $restDaySpecialHolidayOtNdArr[] = $summary[$i][0]->rest_day_special_holiday_overtime_night_diff;
        $restDaySpecialHolidayNdArr[] = $summary[$i][0]->rest_day_special_holiday_night_diff;
        $restDayLegalHolidayOtNdArr[] = $summary[$i][0]->rest_day_legal_holiday_overtime_night_diff;
        $restDayLegalHolidayNdArr[] = $summary[$i][0]->rest_day_legal_holiday_night_diff;

        //SUMMARY: 4th Column
        $specialHolidayArr[] = $summary[$i][0]->special_holiday;
        $specialHolidayOtArr[] = $summary[$i][0]->special_holiday_overtime;
        $legalHolidayArr[] = $summary[$i][0]->legal_holiday;
        $legalHolidayOtArr[] = $summary[$i][0]->legal_holiday_overtime;

        //With Night Diff
        $specialHolidayOtNdArr[] = $summary[$i][0]->special_holiday_overtime_night_diff;
        $specialHolidayNdArr[] = $summary[$i][0]->special_holiday_night_diff;
        $legalHolidayOtNdArr[] = $summary[$i][0]->legal_holiday_overtime_night_diff;
        $legalHolidayNdArr[] = $summary[$i][0]->legal_holiday_night_diff;

      }

        $output = '{';
        $output .= '"data": [';
        $output .= '{'; 

       //SUMMARY: 1st Column
        $output .= '"tardiness": '. '"'.cutoffTotal($latesArr).'",';                
        $output .= '"undertime": '. '"'.cutoffTotal($underTimeArr).'",';

        $output .= '"absences": '. '"'.cutoffTotal($absentArr).'",';                
        $output .= '"paid_vacation_leave": '. '"'.cutoffTotal($paidVacationLeaveArr).'",';

        $output .= '"paid_sick_leave": '. '"'.cutoffTotal($paidSickLeaveArr).'",';
        $output .= '"leave_without_pay": '. '"'.cutoffTotal($leaveWithoutPayArr).'",';                
        
        $output .= '"maternity_leave": '. '"'.cutoffTotal($maternityLeaveArr).'",';                
        $output .= '"paternity_leave": '. '"'.cutoffTotal($paternityLeaveArr).'",';        

        //SUMMARY: 2nd Column
        $output .= '"regular": '. '"'.cutoffTotal($regularArr).'",';                
        $output .= '"regular_overtime": '. '"'.cutoffTotal($regularOtArr).'",';                
        $output .= '"rest_day": '. '"'.cutoffTotal($restDayArr).'",';                
        $output .= '"rest_day_overtime": '. '"'.cutoffTotal($restDayOtArr).'",';

        //With Night Diff
        $output .= '"regular_overtime_night_diff": '. '"'.cutoffTotal($regularOtNdArr).'",';                
        $output .= '"regular_night_differential": '. '"'.cutoffTotal($regularNdArr).'",';                
        $output .= '"rest_day_overtime_night_diff": '. '"'.cutoffTotal($restDayOtNdArr).'",';        
        $output .= '"rest_day_night_diff": '. '"'.cutoffTotal($restDayNdArr).'",';                

        //SUMMARY: 3rd Column                                
        $output .= '"rest_day_special_holiday": '. '"'.cutoffTotal($restDaySpecialHolidayArr).'",';                
        $output .= '"rest_day_special_holiday_overtime": '. '"'.cutoffTotal($restDaySpecialHolidayOtArr).'",';                
        $output .= '"rest_day_legal_holiday": '. '"'.cutoffTotal($restDayLegalHolidayArr).'",';        
        $output .= '"rest_day_legal_holiday_overtime": '. '"'.cutoffTotal($restDayLegalHolidayOtArr).'",';        

        //With Night Diff
        $output .= '"rest_day_special_holiday_overtime_night_diff": '. '"'.cutoffTotal($restDaySpecialHolidayOtNdArr).'",';                
        $output .= '"rest_day_special_holiday_night_diff": '. '"'.cutoffTotal($restDaySpecialHolidayNdArr).'",';                
        $output .= '"rest_day_legal_holiday_overtime_night_diff": '. '"'.cutoffTotal($restDayLegalHolidayOtNdArr).'",';        
        $output .= '"rest_day_legal_holiday_night_diff": '. '"'.cutoffTotal($restDayLegalHolidayNdArr).'",';                

        //SUMMARY: 4th Column                                
        $output .= '"special_holiday": '. '"'.cutoffTotal($specialHolidayArr).'",';                
        $output .= '"special_holiday_overtime": '. '"'.cutoffTotal($specialHolidayOtArr).'",';                
        $output .= '"legal_holiday": '. '"'.cutoffTotal($legalHolidayArr).'",';        
        $output .= '"legal_holiday_overtime": '. '"'.cutoffTotal($legalHolidayOtArr).'",';

        //With Night Diff
        $output .= '"special_holiday_overtime_night_diff": '. '"'.cutoffTotal($specialHolidayOtNdArr).'",';                
        $output .= '"special_holiday_night_diff": '. '"'.cutoffTotal($specialHolidayNdArr).'",';                
        $output .= '"legal_holiday_overtime_night_diff": '. '"'.cutoffTotal($legalHolidayOtNdArr).'",';        
        $output .= '"legal_holiday_night_diff": '. '"'.cutoffTotal($legalHolidayNdArr).'"';                                     

        $output .= '}'; 
        $output .= ']';
        $output .= '}';         

        return (string) $output; //json_encode($timeSheetObj);      

    }    

    public function getEmployeeSummaryByEmployeeIdandDate($employeeId, $dayDate) {

       //return DB::table('employee_summary')->where('employee_id', '=', $employeeId)->where('daydate', '=', date('Y-m-d'))->first();
       return Summary::where('employee_id', '=', $employeeId)->where('daydate', '=', date('Y-m-d'))->first();       

    }      


    public function getEmployeeSummaryNightDiffClocking($employeeId, $yesterDayDate) {

        return Summary::where('employee_id', '=', $employeeId)->where('daydate', '=', $yesterDayDate)->first();  

    }    
}
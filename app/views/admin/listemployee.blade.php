@extends('layouts.admin.default')

@section('content')

<div class="row">

<div class="col-md-12">


	    <h3 style="font-size:14px; font-weight:bold;">Employee <a href="{{ url('/admin/user/new') }}" class="btn btn-primary">Add New</a></h3>

			<table class="table table-striped table-hover display list-table users" cellspacing="0" width="100%">
				<thead>
					<tr>
						<th>#</th>		           				           		
		           		<th>Employee Number</th>		           				           		
		           		<th>Full Name</th>                        
		           		<th>Nick Name</th>		           	
		           		<th>Group</th>
						<th>Date</th>		           		                        							
						<th>Actions</th>
					</tr>
				</thead>
				<tbody>
                <?php foreach( $getUserEmployee as $userEmployee ): ?>
	
					<tr>
						<td><?php echo $userEmployee->id; ?></td>	
		           		<td><?php echo $userEmployee->employee_number; ?></td>		           				           		
		           		<td><?php echo $userEmployee->firstname.' '.$userEmployee->middle_name.', '.$userEmployee->lastname; ?></td>                        
		           		<td><?php echo $userEmployee->nick_name; ?></td>		           	
						<td><?php echo $userEmployee->name; ?></td>	
						<td><?php echo $userEmployee->created_at; ?></td>	

						<td><a href="{{ URL::to('/admin/user/' . $userEmployee->employee_id . '/edit/') }}">Edit</a> | <a href="{{ URL::to('/admin/user/' . $userEmployee->employee_id . '/delete/') }}">Delete</a></td>						
					</tr>
						
				<?php endforeach; ?>
                </tbody>
				<tfoot>
					<tr>
						<th>#</th>		           				           		
		           		<th>Employee Number</th>		           				           		
		           		<th>Full Name</th>                        
		           		<th>Nick Name</th>		           	
		           		<th>Group</th>
						<th>Date</th>	
						<th>Actions</th>							           		                        	
					</tr>
				</tfoot>
			</table>

	<!--nav class="pull-right">
      <ul class="pagination">
        <li class="disabled"><a href="#" aria-label="Previous"><span aria-hidden="true">«</span></a></li>
        <li class="active"><a href="#">1 <span class="sr-only">(current)</span></a></li>
        <li><a href="#">2</a></li>
        <li><a href="#">3</a></li>
        <li><a href="#">4</a></li>
        <li><a href="#">5</a></li>
        <li><a href="#" aria-label="Next"><span aria-hidden="true">»</span></a></li>
     </ul>
   	</nav-->   	

    </div>
    
</div>

@stop
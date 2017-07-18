 @extends ('email_template')

 @section ('content')

 	@foreach ($attendees as $attendee)
 		<!-- ORDER BLOCK -->
		 <table class="row"><tbody><tr>
            <th class="small-12 large-8 columns first"><table><tr><th>

            @if ($attendee->updated->payload_mode == 'order.placed')
              <h2>New Order</h2>
            @elseif ($attendee->updated->payload_mode == 'attendee.updated') 
              <h2>Order Updated</h2>
            @endif
           
              
            </th></tr></table></th>
          </tr></tbody></table>
		  
          <table class="row"><tbody><tr>
            <th class="small-12 large-8 columns first"><table><tr><th>
              <p><strong>Order ID: </strong>{{ $attendee->updated->order_id }}</p>
			  <p><strong>Attendee ID: </strong>{{ $attendee->updated->user_id }}</p>
			  <p><strong>Order Date: </strong>{{ $attendee->updated->created_at }}</p>
            </th></tr></table></th>
          </tr></tbody></table>
		  
          <table class="row CustomInnerBg"><tbody><tr>

   		
            <th class="small-12 large-6 columns first"><table><tr><th>
@if (isset($attendee->original->payload_mode))            
              <br /><h4>Updated</h4><br/>
@endif              
              <p><strong>Name: </strong>{{ $attendee->updated->full_name }}</p>
			  <p><strong>Email: </strong>{{ $attendee->updated->email }}</p>
			  <p><strong>Title: </strong>{{ $attendee->updated->job_title }}</p>
              <p><strong>Company: </strong>{{ $attendee->updated->company }}</p>
              <p><strong>Ticket: </strong>{{ $attendee->updated->ticket_type }}</p>
			  <p><strong>Discount: </strong>{{ $attendee->updated->discount_code }}</p>
              <p><strong>Status: </strong>{{ $attendee->updated->attendee_status }}</p>
            </th></tr></table></th>

      
@if ($attendee->updated->payload_mode == 'attendee.updated' && isset($attendee->original->payload_mode))
            <th class="small-12 large-6 columns last"><table><tr><th>
             <br/> <h4>Previous</h4><br/>
              <p><strong>Name: </strong>{{ $attendee->original->full_name }}</p>
			  <p><strong>Email: </strong>{{ $attendee->original->email }}</p>
			  <p><strong>Title: </strong>{{ $attendee->original->job_title }}</p>
              <p><strong>Company: </strong>{{ $attendee->original->company }}</p>
              <p><strong>Ticket: </strong>{{ $attendee->original->ticket_type }}</p>
			  <p><strong>Discount: </strong>{{ $attendee->original->discount_code }}</p>
              <p><strong>Status: </strong>{{ $attendee->original->attendee_status }}</p>
            </th></tr></table></th>
@endif			
			
          </tr></tbody></table>
          <table class="row"><tbody><tr>
            <th class="small-12 large-8 columns first"><table><tr><th>
              <br/><p><strong>Salesforce URL: <a href="https://eu4.salesforce.com/{{ $attendee->salesforce }}">https://eu4.salesforce.com/{{ $attendee->salesforce }}</a></strong></p>
            </th></tr></table></th>
          </tr></tbody></table>			  
		  
		<hr>
		<!-- END ORDER BLOCK -->
	@endforeach 
@endsection	

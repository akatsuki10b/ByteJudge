<tr>
 		<td valign='top'>
  			<label><strong>Add Students :</strong></label>
 		</td>
 		<td>
  			<label><strong>Search : </strong></label> 
  			<input  type='text' style='width: 200px;' id='searchstr' name='exp'> 
  			<input type='button' value='Select' onclick='checksearch();'>
			<script language='javascript' type='text/javascript'>
			function checksearch()
			{
		
				var checkboxes = new Array();
				checkboxes = document.getElementsByName('studentstoadd[]');
				var searchstr=document.getElementById('searchstr').value;
				searchstr=searchstr.toUpperCase();
				
	       		        for (var i = 0; i < checkboxes.length; i++) 
		  		{
			    		if((checkboxes[i].value).indexOf(searchstr)!=-1)
						checkboxes[i].checked=true;
	     		        }
					   
						 
			}
			</script>
 		</td>
 	</tr>
 	<tr>
 		<td>
  			
 		</td>
 		<td>
  			<p style='color:#8f8f8f;'> <i> Select manually from the list below OR enter part/whole roll number in the above Search field </i></p>
 		</td>
 	</tr>
 	<tr>
 		<td>
 		
 		</td>
 		<td>
 			<div style='max-height:300px; overflow:auto;'>
 			<table id='minitable' style='width:100%; margin-left:0%; max-height:80px; overflow:hidden;'>
 				<tr>
 					<th>
						<input type='checkbox' id='main_checkbox' onchange='checkAll(this)' name='main_checkbox'>
						<script language='javascript' type='text/javascript'>
						function checkAll(main_check) 
						{
							var checkboxes = new Array();
						 	checkboxes = document.getElementsByName('studentstoadd[]');
							
						    for (var i = 0; i < checkboxes.length; i++) 
						    {
						    
								checkboxes[i].checked=main_check.checked;	
						    }
						   
						 }
						 </script>
				 	</th>
				 	<th>
						<label>Roll No.</label>
				 	</th>
					<th>
				  		<label>Name</label>
				 	</th>
 				</tr>
				<?php
					//database connection expected
					$query="select rollno,fullname from students_main order by rollno";
					$res=mysqli_query($db,$query);
					if($res)
					while($r=mysqli_fetch_array($res))
					{
						$rollno=$r['rollno'];
						$fullname=$r['fullname'];
						echo "
						<tr>
						<td>
							<input type='checkbox' name='studentstoadd[]' value='$rollno' ";
						
						if($edit==true)
						
						if(in_array($rollno,$studentsingroup)!=false)
							echo " checked='true' ";
						echo " >
					 	</td>
					 	<td>
							<a href='#'>$rollno</a>
					 	</td>
						<td>
					  		<label>$fullname</label>
					 	</td>
						</tr>
						";
					}
				?>
				
			</table>
			</div>
 		</td>
 		
	</tr>

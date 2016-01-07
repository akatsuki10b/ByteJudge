<tr>
 		<td valign='top'>
  			<label><strong>Add Problems :</strong></label>
 		</td>
 		<td>
  			<label><strong>Search : </strong></label> 
  			<input  type='text' style='width: 200px;' id='searchstr_problem' > 
  			<input type='button' value='Select' onclick='checksearch_problems();'>
			<script language='javascript' type='text/javascript'>
			function checksearch_problems()
			{
		
				var checkboxes = new Array();
				checkboxes = document.getElementsByName('problemstoadd[]');
				var searchstr=document.getElementById('searchstr_problem').value;
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
  			<p style='color:#8f8f8f;'> <i> Select manually from the list below OR enter part/whole Problem Code in the above Search field </i></p>
 		</td>
 	</tr>
 	<tr>
 		<td>
 		
 		</td>
 		<td>
 			<div style='max-height:300px; overflow:auto;'>
 			<table id='listtable'>
 				<tr>
 					<th>
						<input type='checkbox' id='problems_checkbox' onchange='checkAllProblems(this)' name='problems_checkbox'>
						<script language='javascript' type='text/javascript'>
						function checkAllProblems(main_check) 
						{
							var checkboxes = new Array();
						 	checkboxes = document.getElementsByName('problemstoadd[]');
							
						    for (var i = 0; i < checkboxes.length; i++) 
						    {
						    
								checkboxes[i].checked=main_check.checked;	
						    }
						   
						 }
						 </script>
				 	</th>
				 	<th>
						<label>Problem Code</label>
				 	</th>
					<th>
				  		<label>Problem Name</label>
				 	</th>
 				</tr>
				<?php
					//database connection expected
					$query="select problem_code,problem_title from problems order by problem_code";
					$res=mysqli_query($db,$query);
					if($res)
					while($r=mysqli_fetch_array($res))
					{
						$problem_code=$r['problem_code'];
						$problem_title=$r['problem_title'];
						echo "
						<tr>
						<td>
							<input type='checkbox' name='problemstoadd[]' value='$problem_code' ";
						
						if($edit==true)
						
						if(in_array($problem_code,$problemsintest)!=false)
							echo " checked='true' ";
						echo " >
					 	</td>
					 	<td>
							<a href='./viewproblem.php?pcode=$problem_code'>$problem_code</a>
					 	</td>
						<td>
					  		<label>$problem_title</label>
					 	</td>
						</tr>
						";
					}
				?>
				
			</table>
			</div>
 		</td>
 		
	</tr>

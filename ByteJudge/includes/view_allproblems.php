<?php

?>
<h3>Manage Problems</h3>
	<tr>
 		
 		<td>
  			<label><strong>Search : </strong></label> 
  			<input  type='text' style='width: 200px;' id='searchstr' name='exp'> 
  			<input type='button' value='Select' onclick='checksearch();'>
			<script language='javascript' type='text/javascript'>
			function checksearch()
			{
		
				var checkboxes = new Array();
				checkboxes = document.getElementsByName('problemstodelete[]');
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
  			<p style='color:#8f8f8f;'> <i> Select manually from the list below OR enter part/whole problem code in the above Search field </i></p>
 		</td>
 	</tr>
 	<tr>
<form  method='post'  action='./scripts/deleteproblems.php'  name='manage_accounts'>
<div style='max-height:400px; overflow:auto;'>
 			<table id='listtable'>
 				<tr>
 					<th>
						<input type='checkbox' id='main_checkbox' onchange='checkAll(this)' name='main_checkbox'>
						<script language='javascript' type='text/javascript'>
						function checkAll(main_check) 
						{
							var checkboxes = new Array();
						 	checkboxes = document.getElementsByName('problemstodelete[]');
							
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
					<th>
						<label>Added by</label>
					</th>
				 	<th>
				 		<label>Edit</label>
				 	</th>
				 	
 				</tr>
				<?php
			
					include("./includes/mysql_login_faculty.php");
					$query="select problem_code,problem_title,addedby from problems order by problem_code";
					$res=mysqli_query($db,$query);
					
					$i=0;
					if($res)
					while($r=mysqli_fetch_array($res))
					{
						$problem_code=$r['problem_code'];
						$problem_title=$r['problem_title'];
						$addedby=$r['addedby'];
						echo "
						<tr";
						if($i%2==1)
							echo " class='alt'";
						echo ">
						<td>
							<input type='checkbox' name='problemstodelete[]' value='$problem_code'>
					 	</td>
					 	<td>
							<a href='./viewproblem.php?pcode=$problem_code'>$problem_code</a>
					 	</td>
						<td>
					  		<label>$problem_title</label>
					 	</td>
						<td>
							<a href='./faculty_profile.php?facultyid=$addedby'>$addedby</a>
							
						</td>
						<td>
							<a href='./edit_problem.php?edit=$problem_code'><img src='./assets/edit.png'  onMouseOver=\"this.src='./assets/edit_hover.png'\" onMouseOut=\"this.src='./assets/edit.png'\"/></a>
				 		</td>
						</tr>
						";
						$i++;
					}
				
				?>
				
			</table>
			</div>
		<input type="submit" name="delete" value="Delete Checked" class="rbutton" onclick="return confirm('Are you sure?');">
		
</form>
<div>
	<br>
	
</div>

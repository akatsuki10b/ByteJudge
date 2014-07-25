
<h3>Manage Tests</h3>
	<tr>
 		
 		<td>
  			<label><strong>Search : </strong></label> 
  			<input  type='text' style='width: 200px;' id='searchstr' name='exp'> 
  			<input type='button' value='Select' onclick='checksearch();'>
			<script language='javascript' type='text/javascript'>
			function checksearch()
			{
		
				var checkboxes = new Array();
				checkboxes = document.getElementsByName('teststodelete[]');
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
  			<p style='color:#8f8f8f;'> <i> Select manually from the list below OR enter part/whole Test ID in the above Search field </i></p>
 		</td>
 	</tr>
 	<tr>
<form  method='post'  action='./scripts/deletetests.php'  name='manage_tests'>
<div style='max-height:400px; overflow:auto;'>
 			<table id='listtable'>
 				<tr>
 					<th>
						<input type='checkbox' id='main_checkbox' onchange='checkAll(this)' name='main_checkbox'>
						<script language='javascript' type='text/javascript'>
						function checkAll(main_check) 
						{
							var checkboxes = new Array();
						 	checkboxes = document.getElementsByName('teststodelete[]');
							
						    for (var i = 0; i < checkboxes.length; i++) 
						    {
						    
								checkboxes[i].checked=main_check.checked;	
						   	}
						   
						 }
						 </script>
				 	</th>
				 	<th>
						<label>Test ID</label>
				 	</th>
					<th>
				  		<label>Test Name</label>
				 	</th>
				 	<th>
						<label>Visible On</label>
				 	</th>
					<th>
				  		<label>Visible Till</label>
				 	</th>
				 	<th>
				  		<label>Edit</label>
				 	</th>
				 	
				 	
 				</tr>
				<?php
			
					include("./includes/mysql_login_view.php");
					$query="select testid,testname,visiblefrom,visibletill from test_main order by testid";
					$res=mysqli_query($db,$query);
					$i=0;
					if($res)
					while($r=mysqli_fetch_array($res))
					{
						$testid=$r['testid'];
						$testname=$r['testname'];
						$visiblefrom=$r['visiblefrom'];
						$visibletill=$r['visibletill'];
						echo "
						<tr";
						if($i%2==1)
							echo " class='alt'";
						echo ">
						<td>
							<input type='checkbox' name='teststodelete[]' value='$testid'>
					 	</td>
					 	<td>
							<a href='./view_test.php?testid=$testid'>$testid</a>
					 	</td>
						<td>
					  		<label>$testname</label>
					 	</td>
						<td>
				  		<label>$visiblefrom</label>
					 	</td>
					 	<td>
					  		<label>$visibletill</label>
					 	</td>
						<td>
							<a href='./new_test.php?edit=$testid'><img src='./assets/edit.png'  onMouseOver=\"this.src='./assets/edit_hover.png'\" onMouseOut=\"this.src='./assets/edit.png'\"/></a>
				 		</td>
						</tr>
						";
						
					
				 	
						$i++;
					}
				
				?>
			</table>

			</div>
			<br />
		<input type='submit' value='Delete Checked' class="rbutton" onclick="return confirm('Are you sure?');">

</form>
<div>
	<br>
	
</div>

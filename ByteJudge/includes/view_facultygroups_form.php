<?php

?>
<h3>Manage Faculty groups</h3>
	<tr>
 		
 		<td>
  			<label><strong>Search : </strong></label> 
  			<input  type='text' style='width: 200px;' id='searchstr' name='exp'> 
  			<input type='button' value='Select' onclick='checksearch();'>
			<script language='javascript' type='text/javascript'>
			function checksearch()
			{
		
				var checkboxes = new Array();
				checkboxes = document.getElementsByName('facultygroupstodelete[]');
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
<form  method='post'  action='./scripts/deletefacultygroups.php'  name='manage_accounts'>
<div style='max-height:400px; overflow:auto;'>
 			<table id='listtable'>
 				<tr>
 					<th>
						<input type='checkbox' id='main_checkbox' onchange='checkAll(this)' name='main_checkbox'>
						<script language='javascript' type='text/javascript'>
						function checkAll(main_check) 
						{
							var checkboxes = new Array();
						 	checkboxes = document.getElementsByName('facultygroupstodelete[]');
							
						    for (var i = 0; i < checkboxes.length; i++) 
						    {
						    
								checkboxes[i].checked=main_check.checked;	
						   	}
						   
						 }
						 </script>
				 	</th>
				 	<th>
						<label>Faculty Group Id</label>
				 	</th>
					<th>
				  		<label>Name</label>
				 	</th>
				 	<th>
				  		<label>Edit</label>
				 	</th>
				 	
 				</tr>
				<?php
			
					include("./includes/mysql_login_view.php");
					$query="select groupid,groupname from groups_faculty order by groupid";
					$res=mysqli_query($db,$query);
					$i=0;
					if($res)
					while($r=mysqli_fetch_array($res))
					{
						$groupid=$r['groupid'];
						$groupname=$r['groupname'];
						echo "
						<tr";
						if($i%2==1)
							echo " class='alt'";
						echo ">
						<td>
							<input type='checkbox' name='facultygroupstodelete[]' value='$groupid'>
					 	</td>
					 	<td>
							<a href='./facultygroup_profile.php?groupid=$groupid'>$groupid</a>
					 	</td>
						<td>
					  		<label>$groupname</label>
					 	</td>
						<td>
							<a href='./new_faculty_group.php?edit=$groupid'><img src='./assets/edit.png'  onMouseOver=\"this.src='./assets/edit_hover.png'\" onMouseOut=\"this.src='./assets/edit.png'\"/></a>
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

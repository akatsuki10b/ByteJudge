<tr>
 		<td valign='top'>
  			<label><strong>Add Student Groups :</strong></label>
 		</td>
 		<td>
  			<label><strong>Search : </strong></label> 
  			<input  type='text' style='width: 200px;' id='searchstr_studentgroups' > 
  			<input type='button' value='Select' onclick='checksearch_studentgroups();'>
			<script language='javascript' type='text/javascript'>
			function checksearch_studentgroups()
			{
		
				var checkboxes = new Array();
				checkboxes = document.getElementsByName('studentgroupstoadd[]');
				var searchstr=document.getElementById('searchstr_studentgroups').value;
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
  			<p style='color:#8f8f8f;'> <i> Select manually from the list below OR enter part/whole GroupID in the above Search field </i></p>
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
						<input type='checkbox' id='main_checkbox' onchange='checkAll(this)' name='main_checkbox'>
						<script language='javascript' type='text/javascript'>
						function checkAll(main_check) 
						{
							var checkboxes = new Array();
						 	checkboxes = document.getElementsByName('studentgroupstoadd[]');
							
						    for (var i = 0; i < checkboxes.length; i++) 
						    {
						    
								checkboxes[i].checked=main_check.checked;	
						    }
						   
						 }
						 </script>
				 	</th>
				 	<th>
						<label>Group ID</label>
				 	</th>
					<th>
				  		<label>Group Name</label>
				 	</th>
 				</tr>
				<?php
					//database connection expected
					$query="select groupid,groupname from groups_students order by groupid";
					$res=mysqli_query($db,$query);
					if($res)
					while($r=mysqli_fetch_array($res))
					{
						$groupid=$r['groupid'];
						$groupname=$r['groupname'];
						echo "
						<tr>
						<td>
							<input type='checkbox' name='studentgroupstoadd[]' value='$groupid' ";
						
						if($edit==true)
						
						if(in_array($groupid,$studentgroupsintest)!=false)
							echo " checked='true' ";
						echo " >
					 	</td>
					 	<td>
							<a href='./studentgroup_profile.php?groupid=$groupid'>$groupid</a>
					 	</td>
						<td>
					  		<label>$groupname</label>
					 	</td>
						</tr>
						";
					}
				?>
				
			</table>
			</div>
 		</td>
 		
	</tr>

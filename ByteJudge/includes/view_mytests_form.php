
<h3>My Tests</h3>
<div style='max-height:500px; overflow:auto;'>
 			<table id='listtable'>
 				<tr>
 					
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
				 	
 				</tr>
				<?php
					$username=$_SESSION['username'];
					include("./includes/mysql_login_view.php");
					
					$query="select distinct test_main.testid,testname,visiblefrom,visibletill from test_main natural join test_visibleto where groupid IN (select groupid from students_belongtogroups where rollno='$username') order by testid";
					//echo $query;
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
							<a href='./test.php?testid=$testid'>$testid</a>
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
						
						</tr>
						";
						
					
				 	
						$i++;
					}
				
				?>
			</table>

			</div>


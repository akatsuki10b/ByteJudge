
<h3>Leaderboard</h3>
	
 	
 	<tr>

		<div style='height:500px; overflow:auto;'>
 			<table id='listtable'>
 				<tr>
 					<th>
						<label>Rank</label>
					</th>
				 	<th>
						<label>Student Roll No.</label>
				 	</th>
					<th>
				  		<label>Name</label>
				 	</th>
				 	<th>
				  		<label>Solved Questions</label>
				 	</th>
					<th>
						<label>Total Time Taken ( in seconds )</label>
					</th>
				 	
 				</tr>
				<?php
			
				
					
					$query="select visiblefrom from test_main where testid='$testid'";	
					$res=mysqli_query($db,$query);
					if(!($res))
					{	
						$errorstr="Error in Query";
						goto end;
					}
				//	echo mysqli_error($db);
					$r=mysqli_fetch_array($res);
					$test_visiblefrom=$r['visiblefrom'];
					$thistestsubmissions="select * from (select * from test_submissions where testid='$testid') as A natural join (select * from submissions where verdict='AC') as B";
$firstsubmissions="select submissionid,UNIX_TIMESTAMP(MIN(submissiontime))-UNIX_TIMESTAMP('$test_visiblefrom') as timespent,username,problem_code from ($thistestsubmissions) as C group by username,problem_code";
$leaders="select username,count(*) as totalsolved, sum(timespent) as totaltime from ($firstsubmissions) as D group by username";
					$query="select username,totalsolved,totaltime,fullname from ($leaders) as E join students_main on E.username=students_main.rollno order by totalsolved DESC,totaltime";
					//echo $query;
					$res=mysqli_query($db,$query);
					if(!($res))
					{	
						$errorstr="Error in Query";
						goto end;
					}
					//echo mysqli_error($db);
					$i=1;
					if($res)
					while($r=mysqli_fetch_array($res))
					{
						$rollno=$r['username'];
						$fullname=$r['fullname'];
						$totalsolved=$r['totalsolved'];
						$totaltime=$r['totaltime'];
						echo "
						<tr";
						if($i%2==0)
							echo " class='alt'";
						echo ">
						
						<td>
							$i
						</td>
					 	<td>
							<a href='./student_profile.php?rollno=$rollno'>$rollno</a>
					 	</td>
						<td>
					  		<label>$fullname</label>
					 	</td>
						<td>
					  		<label>$totalsolved</label>
					 	</td>
						<td>
					  		<label>$totaltime</label>
					 	</td>
																		
								
						</tr>
						";
						$i++;
					}
				end:
				if($errorstr!='')
				{
					echo $errorstr;
				}
				
				?>
				
			</table>
			</div>
	

<div>
	<br>
	
</div>

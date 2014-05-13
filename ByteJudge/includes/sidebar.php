<div id='sidebar'>
<?php
if(isset($_SESSION['type']))
{
	
		if($_SESSION['type']=='admin' || $_SESSION['type']=="faculty")
		{
			echo "<h3>Create</h3>";
			if($_SESSION['type']=="admin")
			{	
				echo " 
					<li><a href='./student_registration.php'>Student</a></li>
					<li><a href='./faculty_registration.php'>Faculty</a></li>
					<li><a href='./new_student_group.php'>Student Group</a></li>
					<li><a href='./new_faculty_group.php'>Faculty Group</a></li>
				";
				
			}
			if($_SESSION['type']=="faculty")
			{
				echo "	
					<li><a href='./new_student_group.php'>Student Group</a></li>
					<li><a href='./new_problem.php'>Problem</a></li>
					<li><a href='./new_test.php'>Test</a></li>
				";
			}
		}
		echo "
		<h3>View</h3>
		";
		if($_SESSION['type']=='admin')
		{
			echo "
				<li><a href='./view_students.php'>Students</a></li>
				<li><a href='./view_studentgroups.php'>Student Groups</a></li>
				<li><a href='./view_faculty.php'>Faculties</a></li>
				<li><a href='./view_facultygroups.php'>Faculty Groups</a></li>
			";
	
		}
		if($_SESSION['type']=="faculty")
		{
			echo "
				<li><a href='./view_studentgroups.php'>Student Groups</a></li>
				<li><a href='./view_allproblems.php'>Problems</a></li>
				<li><a href='./view_alltests.php'>Tests</a></li>
			";
		}
		if($_SESSION['type']=="student")
		{
			echo "
				<li><a href='./problems_practice.php'>Practice Problems</a></li>
				<li><a href='./view_mytests.php'>My Tests</a></li>
			";
		}
	
}
?>
</div> <!-- end #side_nav -->

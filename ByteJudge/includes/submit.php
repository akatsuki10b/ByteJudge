<div id='submit'>
<script language='JavaScript' type='text/javascript'>
function HandleBrowseClick()
{
    var fileinput = document.getElementById('browse');
    fileinput.click();
}
function Handlechange()
{
var fileinput = document.getElementById('browse');
var textinput = document.getElementById('filename');
textinput.value = fileinput.value;
}
function validate()
{
	var solfile=document.getElementById('solutionfile');
	if(solfile.value=='')
	{
		alert('No file selected');
		return false;
	}
	var language=document.getElementById('language');
	if(language.value=='')
	{
		alert('No Language selected');
		return false;
	}
	document.getElementById('hidden_running_gif').style.display='block'; 
	document.getElementById('submit_button').style.display='none'; 
	return true;
}
</script>

<?php 
	if($username!='')
	{
	
		include_once('./includes/listoflanguages.php');
		echo "
			<form method='post'  enctype='multipart/form-data' action='./scripts/evaluate.php' onsubmit='return validate()'>
			";
			if(isset($istest) && $istest==true)
				echo "<input type='hidden' name='testid' value='$testid'>";
			echo "
			<input type='hidden' name='probcode' value='$probcode'>
		        <input type='hidden' name='username' value='$username'>
		        <table>
				<tr>
				<td>
		        <input type='file' id='solutionfile' name='solution' ;/>
		        </td>
		        <td>
	       	        <select name='language' id='language'>
			";
		
		
		foreach($languagesallowed as $lang)
		{
			echo "<option value='$lang'>$lang</option>";
		}
		echo "</select>
		</td>
			<td>
			<input type='submit' id='submit_button' value='Submit' />
			</td>
			<td>
			<img src='./assets/PENDING.png' id='hidden_running_gif' style='inline-display:block; display:none;'>
			</td>
			</tr>
		</table>
			</form>

		";
	}
	else
	{
		echo "Please log in to submit a solution";
	}
?>
</div> <!-- end #submit -->

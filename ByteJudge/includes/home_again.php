<table style='margin-left:50%; font-size:16px' >
	<tr>
		<td>
    	<li class="lh" id="t1" onclick="change('tab1')">
    	Student
    	</li>
    	</td>
    	<td>
    	<li class="lh" id="t2" onclick="change('tab2')" >
    	Faculty
    	</li>
    	</td>
    </tr>
</table>

	<div id="display_content" onload="change('tab1')"></div>
	
	<div id="tab1" class="content_tab">
		<img src='./assets/student_walkthrough.jpg'/>
	</div>
	<div id="tab2" class="content_tab">
		<img src='./assets/faculty_walkthrough.jpg'/>
	</div>
	
<script language='javascript' type='text/javascript'>
var tab_name;
function change(tab_name)
{
	document.getElementById("display_content").innerHTML=document.getElementById(tab_name).innerHTML;
	var livetab, i;
	switch(tab_name)
	{
		case 'tab1':
		    livetab='t1';
		break;
		case 'tab2':
		    livetab='t2';
		break;
	}
	for(i=1;i<=2;i++)
	{
		document.getElementById("t"+i).style.borderBottom='#fafafa solid 3px';
	}
	document.getElementById(livetab).style.borderBottom='#396070 solid 3px';
}
</script>

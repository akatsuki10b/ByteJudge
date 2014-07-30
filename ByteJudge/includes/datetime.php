<?php
function dateandtime($name_datetime,$def_year,$def_month,$def_day,$def_hour,$def_minute,$def_second)
{
	echo "<select name='${name_datetime}year'>";
	echo "<option value='YYYY'>YYYY</option>";
	for($i=2010;$i<=2020;$i++)
	{
		echo "<option value='$i' "; if($def_year==$i) echo " selected='true' "; echo ">$i</option>";
	}
	echo "</select>";
	
	echo "<select name='${name_datetime}month'>";
	echo "<option value='MM'>MM</option>";
	for($i=1;$i<=12;$i++)
	{
		echo "<option value='$i' "; if($def_month==$i) echo " selected='true' "; echo ">$i</option>";
	}
	echo "</select>";

	echo "<select name='${name_datetime}day'>";
	echo "<option value='DD'>DD</option>";
	for($i=1;$i<=31;$i++)
	{
		echo "<option value='$i' "; if($def_day==$i) echo " selected='true' "; echo ">$i</option>";
	}
	echo "</select>";

	echo "<select name='${name_datetime}hour'>";
	echo "<option value='HH'>HH</option>";
	for($i=0;$i<=23;$i++)
	{
		echo "<option value='$i' "; if($def_hour==$i) echo " selected='true' "; echo ">$i</option>";
	}
	echo "</select>";

	echo "<select name='${name_datetime}minute'>";
	echo "<option value='MM'>MM</option>";
	for($i=0;$i<=59;$i++)
	{
		echo "<option value='$i' "; if($def_minute==$i) echo " selected='true' "; echo ">$i</option>";
	}
	echo "</select>";
	
	echo "<select name='${name_datetime}second'>";
	echo "<option value='SS'>SS</option>";
	for($i=0;$i<=59;$i++)
	{
		echo "<option value='$i' "; if($def_second==$i) echo " selected='true' "; echo ">$i</option>";
	}
	echo "</select>";
}
?>

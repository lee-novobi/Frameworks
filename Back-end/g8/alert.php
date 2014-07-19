<?php
if($_SERVER['REQUEST_METHOD'] == 'POST'){
	pd($_FILES);
}
?>
<html>
	<form method="post" enctype="multipart/form-data">
		<table cellpadding="0" cellspacing="5">
			<tr>
				<th>Affected Deals</th>
				<td><input type="text" class="w1" name="data[affected_deals]"></td>
			</tr>
			<tr>
				<th>To Mail</th>
				<td><input type="text" class="w1" name="data[to_email_list]"></td>
			</tr>
			<tr>
				<th>CC Mail</th>
				<td><input type="text" class="w1" name="data[cc_email_list]"></td>
			</tr>
			<tr>
				<th>Description</th>
				<td><textarea type="text" class="w1" name="data[description]"></textarea></td>
			</tr>
			<tr>
				<th>Title</th>
				<td><textarea type="text" class="w1" name="data[title]"></textarea></td>
			</tr>
			<tr>
				<th>File 1</th>
				<td><input type="file" name="data[file[]]"></textarea></td>
			</tr>
			<tr>
				<th>File 2</th>
				<td><input type="file" name="data[file[]]"></textarea></td>
			</tr>
			<tr>
				<th>File 3</th>
				<td><input type="file" name="data[file[]]"></textarea></td>
			</tr>
			<tr>
				<th>File 4</th>
				<td><input type="file" name="data[file[]]"></textarea></td>
			</tr>
			<tr>
				<th>File 5</th>
				<td><input type="file" name="data[file[]]"></textarea></td>
			</tr>
		</table>
	</form>
</html>
<?php
function d()
{
  die("123456");
}
// ---------------------------------------------------------------------------------------------- //
function p($v)
{
  echo('<pre>');
  print_r($v);
  echo('</pre>');
}
// ---------------------------------------------------------------------------------------------- //
function pd($v)
{
  echo('<pre>');
  print_r($v);
  echo('</pre>');
  d();
}
?>
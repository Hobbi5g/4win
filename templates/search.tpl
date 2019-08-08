{*
<form action="" method="post" style="width:400px">
	<input type="hidden" name="searchsubmit" value="yes">
	<table border="0" cellspacing="0" cellpadding="0">
		<tr>
			<td style="padding: 5px 0 5px 10px;">Search our site for games:</td>
			<td><input type="text" name="srequest" style="border-style:solid; border: 3px solid #e0e0e0; margin: 5px 0 5px 5px;font-size:17px;" value="{$srequest}"></td>
			<td><input type="submit" name="search" value="Search" class="inp" style="-webkit-border-radius: 2px;font-size:15px;margin: 5px 10px 5px 5px;padding:4px 16px;"></td>
		</tr>
	</table>
</form>
	*}

<div class="search">
	<form method="post" id="searchform" action="">
		<input type="hidden" name="searchsubmit" value="yes" />
		<fieldset>
			<input name="srequest" type="text" onfocus="if(this.value=='Search') this.value='';" onblur="if(this.value=='') this.value='Search';" value="Search">
			<button type="submit" name="search"></button>
		</fieldset>
	</form>
</div>
<div class="submit">


<form action="./" method="post" class="loginform">
	<input type="hidden" name="try" value="1">

	<table><tr><td align="center" valign="center">

	<table cellspacing="3" cellpadding="10">
	<tr><td align="left" class="frm5" colspan="2">
	<b><font color=red>PLEASE LOGIN:{*<?if(!$try_password){?>PLEASE LOGIN:<?}else{?>TRY AGAIN!<?}?>*}</font></b>

	<tr><td align="right" class="frm4">

		<table border="0" style="font-size: 11px;">
		<tr>
			<td>Login:
			<td align="right"><input type="text" name="l">
		<tr>
			<td align="right">Password:
			<td align="right"><input type="password" name="p">
		<tr>
			<td><td><a href="">Register</a>
		<tr>
			<td><td><a href="">Forgot password?</a>
		<tr>
			<td><td><input name="submit" type="submit" class="button" style="width:100%" value="Login">
		</table>

	<tr><td class="frm5" colspan="2">
	<img src="../images/admin/logo01.gif" width="32" height="32" border=0 alt="" align="right" valign="center">
	Games4Win Submit<br>v.1.0
	</table>

	</td></tr></table>
</form>








<!--
	<h1>{$blocktitle}</h1>
	<form action="" name="Add" method="post" onSubmit="{literal}if (document.Add.text.value=='' || document.Add.text.value=='http://') { alert('Please write PAD URL.'); return false; } else { return true; }{/literal}">
	PAD URL:
	<input type="text" name="text" class="inp2" maxlength="1024" size="46" title="PAD URL" value="http://" />
	<input name="commentadded" type="submit" class="sendcomm" value=" Add " />
	</form>
-->
</div>

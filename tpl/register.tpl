<div class="content">
{register-form}
<table class="login_body">
	<tr>
		<td align=center>
			<br>
			<br>
			<table class="register_form">
				<tr>
					<td class="register_form"><form action="/?do=register" method="post">{lang-login}:</td>
					<td class="register_form"><input name="user" type="text" maxlength="30" size="20"><b><font color=red>!</font></b></td>
				</tr>
				<tr>
					<td class="register_form">{lang-fio}:</td>
					<td class="register_form"><input name="fio" type="text" maxlength="150" size="21"></td>
				</tr>
				<tr>
					<td class="register_form">{lang-passwd}:</td>
					<td class="register_form"><input name="pass" type="password" size="20"><b><font color=red>!</font></b></td>
				</tr>
				<tr>
					<td class="register_form">{lang-re-passwd}:</td>
					<td class="register_form"><input name="pass2" type="password" size="20"><b><font color=red>!</font></b></td>
				</tr>
				<tr>
					<td class="register_form">{lang-email}:</td>
					<td class="register_form"><input name="email" type="text" maxlength="255" size="20"><b><font color=red>!</font></b></td>
				</tr>
				{register-errors}
				<tr><td class="register_form" colspan=2 align=center><input type="submit" value="{lang-submit}"></td></tr>
			</table>
			<br>
			<br>
		</td>
	</tr>
</table>
</div>
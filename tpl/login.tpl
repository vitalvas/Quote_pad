<div class="content">
{login-form}
<table class="login_body">
	<tr>
		<td align=center>
			<br>
			<br>
			<table class="login_form">
				<tr>
					<td class="login_form">
						<form action="/?do=login" method="post">
						{lang-login}: <br><input name="login" type="text">
					</td>
				</tr>
				<tr>
					<td class="login_form">
						{lang-passwd}: <br><input name="pass" type="password">
					</td>
				</tr>
				{login-errors}
				<tr>
					<td class="login_form">
						<input type="submit" value="{lang-submit}">
					</td>
				</tr>
			</table>
			<br>
			<br>
		</td>
	</tr>
</table>
</div>
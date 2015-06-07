<?php
	header('Content-Type: text/html; charset=utf-8');
?>
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/CSS" />
		<title>MyFiles - Pagina di Installazione</title>
	</head>
	<body>

<?php
	if (isset($_POST['username'])) {
		$username = $_POST['username'];
		$fpassword = $_POST['password'];
		$spassword = $_POST['checkpassword'];
		
		if ($fpassword == $spassword) {
			$md5user = md5($username);
			$md5pass = md5($fpassword);
			file_put_contents("./config.php", "<?php\n\$rightuser = \"{$md5user}\";\n\$rightpass = \"{$md5pass}\";\n?>");
?>
		<a href="modify.php?afterinstallation">Installazione completata. Inizia a creare cose</a>
<?php
			exit(1);
		} else {
?>
		<p class="txtstd text gray center">Errore. Le due password non coincidono. Reinseriscile</p>
<?php
		}
	}
?>

		<p class="section txtbig gray center">Installazione. Inserire password e nome utente</p>
		<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
			<table><tbody>
				<tr><td>Username:</td><td><input type="text" name="username" size="100"/></td></tr>
				<tr><td>Password:</td><td><input type="password" name="password" size="100"/></td></tr>
				<tr><td>Conferma Password:</td><td><input type="password" name="checkpassword" size="100"/></td></tr>
				<tr><td colspan="2"><button>Setta Username e Password</button></td></tr>
			</tbody></table>
		</form>
	</body>
</html>

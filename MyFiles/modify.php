<?php
	session_start();
	$file = "files.xml";

	$rightuser = "dario"; $rightpass = "dario";
	//Script che deve permettere di autenticarsi e modificare/creare/eliminare i progetti ed i files
	$xml = simplexml_load_file($file);
	$lastprojectid = $xml->root->lastprojectid;
	$lastfileid = $xml->root->lastfileid;

	//Se si sta cercando di autenticarsi
	if (!isset($_SESSION['auth']) && isset($_POST['username']) && isset($_POST['password'])) {
		if ($_POST['username'] == $rightuser && $_POST['password'] == $rightpass) {
			//Autenticazione andata a buon fine, ricordiamocelo
			$_SESSION['auth'] = "si";
		} else {
			//Autenticazione Fallita
			unset($_POST['username']); unset($_POST['password']);
		?>
		<p class="txtstd text gray center">Autenticazione Fallita. Riprovare</p><br/><br/>
		<?php
		}
	}

	//Non ci si è ancora autenticati
	if (!isset($_SESSION['auth']) && !isset($_POST['username'])) {
		//Non siamo ancora autenticati. Chiediamo nome e password
	?>
		<div class="pagecontainer">
		<form action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<table><tbody>
				<tr><td>Username:</td><td><input type="text" name="username" size="40"/></td></tr>
				<tr><td>Password:</td><td><input type="password" name="password" size="40"/></td></tr>
			</tbody></table>
		</form>
		</div>
	<?php
		exit(1);
	}

	//Ci si è autenticati, mostriamo l'elenco delle cose tra cui scegliere

	
	if (isset($_GET['project'])) {
		//Se si è selezionato un progetto, allora mostra l'elenco dei file / AGGIUNGI NUOVO FILE

	}

	


	//Elenco dei progetti / MODIFICA TITOLO PAGINA / CREA NUOVO PROGETTO
	?>
	<br/><br/><br/><p class="txtbig section center">Elenco dei progetti presenti</p>
	<ul>
	<?php
		foreach ($xml->xpath("/root/project") as $project) {
	?>		<a href="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $project->projectid ?>"><li><?= $project->projectname ?></li></a>
	<?php } ?>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?modtitle=yes">MODIFICA TITOLO E DESCRIZIONE PAGINA</a></li>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $lastprojectid+1 ?>&createnewproject=<?= $lastprojectid+1 ?>">CREA NUOVO PROGETTO</a></li>
	<?php
?>

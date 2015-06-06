<?php
	session_start();
	$xmlfile = "files.xml";
?>
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/CSS" />
		<title>MyFiles - Modifica Pagina</title>
	</head>
	<body>
<?php
	$rightuser = "dario"; $rightpass = "dario";
	//Script che deve permettere di autenticarsi e modificare/creare/eliminare i progetti ed i files
	$xml = simplexml_load_file($xmlfile);
	$lastprojectid = $xml->root->lastprojectid;
	$lastfileid = $xml->root->lastfileid;

	//Se ci si vuole disconnetere (LOGOUT)
	if (isset($_GET['logout'])) {
		unset($_SESSION['auth']);
		session_destroy();
?>
	<p class="txtstd text gray center">Disconnessione avvenuta con successo</p><br/><br/>
<?php
	}

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
				<tr><td colspan="2"><button>Accedi</button></td></tr>
			</tbody></table>
		</form>
		</div>
	<?php
		exit(1);
	}
	
	//Operazioni da effettuare prima che l'utente veda la schermata (ad esempio creare un nuovo progetto, creare un nuovo file)
	//Creiamo un nuovo progetto
	if (isset($_GET['createnewproject'])) {
		$thisid = $lastprojectid + 1;
		$newproject = $xml->root->addChild("project");
		$newproject->addChild("projectid", $lastprojectid+1);
		$newproject->addChild("projectname", "Senza Nome {$thisid}");
		$newproject->addChild("projectdescription", "Nessuna Descrizione");
		$newproject->addChild("content");
		$xml->root->lastprojectid = $lastprojectid+1;
		$xml->asXML($xmlfile);
	}

	//Modifica effettiva del titolo della pagina
	if (isset($_POST['newpagetitle']) && isset($_POST['newpagedescription'])) {
		$xml->pagetitle = $_POST['newpagetitle'];
		$xml->pagedescription = $_POST['newpagedescription'];
		$xml->asXML($xmlfile);
?>
	<p class="txtstd text gray center">Titolo della pagina modificato con successo</p>
<?php
	}



	//Modifica del titolo della pagina
	if (isset($_GET['modtitle'])) {
?>
	<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<table><tbody>
			<tr><td>Titolo Pagina:</td><td><input type="text" name="newpagetitle" value="<?= $xml->pagetitle ?>" size="100"/></td>
			<tr><td>Descrizione:</td><td><textarea rows="20" name="newpagedescription" cols="100"><?= $xml->pagedescription ?></textarea></td></tr>
			<tr><td colspan="2"><button>Modifica</button></td></tr>
		</tbody></table>
	</form>
<?php
	}


	//Ci si è autenticati, mostriamo l'elenco delle cose tra cui scegliere
	//Elenco dei files nel progetto / AGGIUNGI NUOVO FILE
	if (isset($_GET['project'])) {
		foreach ($xml->xpath("/root/project") as $project)
			if ($project->projectid == $_GET['project'])
			{ $thisproject = $project; break; }
?>
	<br/><br/><br/><p class="txtbig section center">Elenco dei files presenti nel progetto <?= $thisproject->projectname ?></p>
	<ul>
<?php
		//Se si è selezionato un progetto, allora mostra l'elenco dei file / AGGIUNGI NUOVO FILE	
		foreach ($thisproject->xpath("content/file") as $file) {
?>		<a href="<?= $_SERVER['PHP_SELF'] ?>?file=<?= $file->fileid ?>?project=<?= $thisproject->projectid ?>"><li><?= $file->filedescription ?></li></a>
	<?php } ?>
		<li><a href="<?= $_SERVER['PHP_SELF'] ?>?file=<?= $lastfileid+1 ?>&project=<?= $thisproject->projectid ?>&createnewfileinproject=<?= $thisproject->projectid ?>">AGGIUNGI NUOVO FILE</a></li>
	</ul>
<?php
	}


	//Elenco dei progetti / MODIFICA TITOLO PAGINA / CREA NUOVO PROGETTO / LOGOUT
	?>
	<br/><br/><br/><p class="txtbig section center">Elenco dei progetti presenti</p>
	<ul>
	<?php
		foreach ($xml->xpath("/root/project") as $project) {
	?>		<a href="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $project->projectid ?>"><li><?= $project->projectname ?></li></a>
	<?php } ?>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?modtitle">MODIFICA TITOLO E DESCRIZIONE PAGINA</a></li>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $lastprojectid+1 ?>&createnewproject">CREA NUOVO PROGETTO</a></li>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?logout">LOGOUT</a></li>
			</ul>
	<?php
?>
	</body>
</html>

<?php
	session_start();
	$xmlfile = "files.xml";
	$xml = simplexml_load_file($xmlfile);
	$lastprojectid = $xml->lastprojectid;
	$lastfileid = $xml->lastfileid;
	$rightuser = "dario"; $rightpass = "dario";
	
	function searchProjectById ($projid) {
		global $xml;
		foreach ($xml->xpath("/root/project") as $project)
			if ($project->projectid == $projid)
				{return $project; break; }
		return NULL;
	}

	function searchFileById ($fileid) {
		global $xml;
		foreach ($xml->xpath("/root/project") as $project)
			foreach ($project->xpath("content/file") as $file)
				if ($file->fileid == $fileid)
					return $file;
		return NULL;
	}
?>
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/CSS" />
		<title>MyFiles - Modifica Pagina</title>
	</head>
	<body>
<?php
	//Script che deve permettere di autenticarsi e modificare/creare/eliminare i progetti ed i files
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
		$thisprojid = $lastprojectid + 1;
		$newproject = $xml->addChild("project");
		$newproject->addChild("projectid", $thisprojid);
		$newproject->addChild("projectname", "Senza Nome {$thisprojid}");
		$newproject->addChild("projectdescription", "Nessuna Descrizione");
		$newproject->addChild("content");
		$xml->lastprojectid = $thisprojid;
		$xml->asXML($xmlfile);
?><p class="txtstd gray text center">Progetto aggiunto</p><?php
	}

	//Creiamo un nuovo file
	if (isset($_GET['createnewfileinproject'])) {
		$thisproject = searchProjectById($_GET['createnewfileinproject']);
		$thisfileid = $lastfileid + 1;
		$newfile = $thisproject->content->addChild("file");
		$newfile->addChild("fileid", $thisfileid);
		$newfile->addChild("link", "");
		$newfile->addChild("filedescription", "File senza nome {$thisfileid}");
		$newfile->addChild("filenotes", "");
		$xml->lastfileid = $thisfileid;
		$xml->asXML($xmlfile);
?><p class="txtstd gray text center">File aggiunto</p><?php
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

	//Modifica effettiva del nome del progetto
	if (isset($_POST['modprojectid'])) {
		$thisproject = searchProjectById($_POST['modprojectid']);
		$thisproject->projectname = $_POST['newprojecttitle'];
		$thisproject->projectdescription = $_POST['newprojectdescription'];
		$xml->asXML($xmlfile);
?><p class="txtstd text gray center">Progetto modificato con successo</p><?php
	}

	//Modifica effettiva del file
	if (isset($_POST['modfileid'])) {
		$thisfile = searchFileById($_POST['modfileid']);
		$thisfile->link = $_POST['newfilelink'];
		$thisfile->filedescription = utf8_encode($_POST['newfiledescription']);
		$thisfile->filenotes = $_POST['newfilenotes'];
		$xml->asXML($xmlfile);
?><p class="txtstd text gray center">File modificato con successo</p><?php	
	}



	//Modifica del titolo della pagina
	if (isset($_GET['modpagetitle'])) {
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

	//Modifica del titolo del progetto
	if (isset($_GET['modprojecttitle'])) {
		$thisproject = searchProjectById($_GET['modprojecttitle']);
?>
	<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<p class="txtbig section center">Modifica titolo e descrizione del progetto <?= $thisproject->projectname ?></p>
		<table><tbody>
			<tr><td>Titolo:</td><td><input type="text" name="newprojecttitle" value="<?= $thisproject->projectname ?>" size="100"/></td></tr>
			<tr><td>Descrizione:</td><td><textarea rows="20" cols="100" name="newprojectdescription"><?= $thisproject->projectdescription ?></textarea></td></tr>
			<tr><td colspan="2"><input type="hidden" name="modprojectid" value="<?= $thisproject->projectid ?>"/><button>Modifica</button></td></tr>
		</tbody></table>
	</form>
<?php	
	}

	//Modifica del file
	if (isset($_GET['file'])) {
		echo "fileid: {$_GET['file']}";
		$thisfile = searchFileById($_GET['file']);
?>
	<form action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<p class="txtbig section center">Modifica del file <?= $thisfile->filedescription ?></p>
		<table><tbody>
			<tr><td>Link al file:</td><td><input type="text" name="newfilelink" value="<?= $thisfile->link ?>" size="100"/></td></tr>
			<tr><td>Titolo file:</td><td><input type="text" name="newfiledescription" value="<?= $thisfile->filedescription ?>" size="100"/></td></tr>
			<tr><td>Note al file:</td><td><textarea rows="20" cols="100" name="newfilenotes"><?= $thisfile->filenotes ?></textarea></td></tr>
			<tr><td colspan="2"><input type="hidden" name="modfileid" value="<?= $thisfile->fileid ?>"/><button>Modifica File</button></td></tr>
		</tbody></table>
	</form>
<?php
	}


	//Ci si è autenticati, mostriamo l'elenco delle cose tra cui scegliere
	//Elenco dei files nel progetto / AGGIUNGI NUOVO FILE / MODIFICA TITOLO PROGETTO
	if (isset($_GET['project'])) {
		$thisproject = searchProjectById($_GET['project']);
?>
	<br/><br/><br/><p class="txtbig section center">Elenco dei files presenti nel progetto <?= $thisproject->projectname ?></p>
	<ul>
<?php
		//Se si è selezionato un progetto, allora mostra l'elenco dei file / AGGIUNGI NUOVO FILE / MODIFICA TITOLO PROGETTO	
		foreach ($thisproject->xpath("content/file") as $file) {
?>		<a href="<?= $_SERVER['PHP_SELF'] ?>?file=<?= $file->fileid ?>&project=<?= $thisproject->projectid ?>"><li><?= $file->filedescription ?></li></a>
	<?php } ?>
		<li><a href="<?= $_SERVER['PHP_SELF'] ?>?file=<?= $lastfileid+1 ?>&project=<?= $thisproject->projectid ?>&createnewfileinproject=<?= $thisproject->projectid ?>">AGGIUNGI NUOVO FILE</a></li>
		<li><a href="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $thisproject->projectid ?>&modprojecttitle=<?= $thisproject->projectid ?>">MODIFICA TITOLO / DESCRIZIONE PROGETTO</a></li>
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
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?modpagetitle">MODIFICA TITOLO E DESCRIZIONE PAGINA</a></li>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $lastprojectid+1 ?>&createnewproject">CREA NUOVO PROGETTO</a></li>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?logout">LOGOUT</a></li>
			</ul>
	<?php
?>
	</body>
</html>

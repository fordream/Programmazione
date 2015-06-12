<?php
	session_start();
	header('Content-Type: text/html; charset=utf-8');
	$xmlfile = "files.xml";
	$xml = simplexml_load_file($xmlfile);
	$lastprojectid = $xml->lastprojectid;
	$lastfileid = $xml->lastfileid;

	if (!file_exists($xmlfile)) {
		file_put_contents($xmlfile, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n<?xml-stylesheet type=\"text/xsl\" href=\"files.xsl\"?>\n<!-- Elenco dei files e relativa descrizione -->\n<root>\n<lastprojectid>0</lastprojectid>\n<lastfileid>0</lastfileid>\n<options><backgroundcolor>E5E4E2</backgroundcolor><sectioncolor>15317E</sectioncolor><textcolor>368BC1</textcolor><linkcolor>0000A0</linkcolor><hovercolor>1F45FC</hovercolor><globalmag>100</globalmag><txtstdmag>1.1</txtstdmag><sectionsmag>1.3</sectionsmag><pagetitlemag>2.2</pagetitlemag><dxmargin>20</dxmargin><sxmargin>20</sxmargin></options>\n<homepage>/</homepage>\n<pagetitle></pagetitle>\n<pagedescription></pagedescription>\n</root>");
	}
	if (!file_exists("config.php")) {
?>
	<html>
		<head>
			<link href="style.php" rel="stylesheet" type="text/CSS" />
			<title>MyFiles - Non installato</title>
		</head>
		<body>
			<a href="install.php">Devi ancora installare MyFiles. Clicca Qui per procedere</a>
		</body>
	</html>
<?php
		exit(1);
	}
	if (file_exists("install.php")) {
		unlink("install.php");
	}

	include 'config.php';
	
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

	function getProjectOfFile ($fileid) {
		global $xml;
		foreach ($xml->xpath("/root/project") as $project)
			foreach ($project->xpath("content/file") as $file)
				if ($file->fileid == $fileid)
					return $project;
		return NULL;
	}

	function deleteNonEmptyDir($dir) 
	{
	   if (is_dir($dir)) 
	   {
		$objects = scandir($dir);

		foreach ($objects as $object) 
		{
		    if ($object != "." && $object != "..") 
		    {
		        if (is_dir($dir . DIRECTORY_SEPARATOR . $object))
		        {
		            deleteNonEmptyDir($dir . DIRECTORY_SEPARATOR . $object); 
		        }
		        else
		        {
		            unlink($dir . DIRECTORY_SEPARATOR . $object);
		        }
		    }
		}

		reset($objects);
		rmdir($dir);
	    }
	}
?>
<html>
	<head>
		<link href="style.php" rel="stylesheet" type="text/CSS" />
		<title>MyFiles - Modifica Pagina</title>
	</head>
	<body>
		<script type="text/javascript" src="colorpicker/jscolor.js"></script>
		<script type="text/javascript" src="jquery.js"></script>
		<script type="text/javascript">
			$(document).ready(function() {
				$('input[type="range"]').each(function ( index ) {
					$(this).after($.parseHTML("<div style=\"text-align: center; \" name=\""+this.name+"_display\"></div>"));
					showValue(this.name, this.getAttribute('unit'));
					
					$(this).on('change', function () {
						showValue(this.name, this.getAttribute('unit'))
					});
				});
			});
	
			function showValue(name, units)
			{
				var range = document.getElementsByName(name)[0];
				var wr = document.getElementsByName(name+"_display")[0];
				wr.innerHTML = range.value+units;
			}
		</script>
<?php
	if (isset($_GET['afterinstallation'])) {
		if (file_exists("install.php"))
			unlink("install.php");
?>
	<p class="txtstd text gray center">Grazie per aver installato MyFiles. Buon Lavoro</p>
<?php
	}

	//Script che deve permettere di autenticarsi e modificare/creare/eliminare i progetti ed i files
	//Se ci si vuole disconnetere (LOGOUT)
	if (isset($_GET['logout'])) {
		unset($_SESSION['auth']);
		session_destroy();
?>
	<p class="txtstd text gray center">Disconnessione avvenuta con successo</p><hr/><br/><br/>
<?php
	}

	//Se si sta cercando di autenticarsi
	if (!isset($_SESSION['auth']) && isset($_POST['username']) && isset($_POST['password'])) {
		if (md5($_POST['username']) == $rightuser && md5($_POST['password']) == $rightpass) {
			//Autenticazione andata a buon fine, ricordiamocelo
			$_SESSION['auth'] = "si";
		} else {
			//Autenticazione Fallita
			unset($_POST['username']); unset($_POST['password']);
		?>
		<p class="txtstd text gray center">Autenticazione Fallita. Riprovare</p><hr/><br/><br/>
		<?php
		}
	}

	//Non ci si è ancora autenticati
	if (!isset($_SESSION['auth']) && !isset($_POST['username'])) {
		//Non siamo ancora autenticati. Chiediamo nome e password
	?>
		<div class="pagecontainer">
		<form accept-charset="utf-8" action="<?=$_SERVER['PHP_SELF']?>" method="POST">
			<table><tbody>
				<tr><td>Username:</td><td><input type="text" name="username" size="40"/></td></tr>
				<tr><td>Password:</td><td><input type="password" name="password" size="40"/></td></tr>
				<tr><td colspan="2"><button>Accedi</button></td></tr>
			</tbody></table>
		</form>
		</div>
	
	<div id="footer">
		<span class="footcont">
			<a href="files.xml">Pagina Appunti</a>
		</span>
	</div>
	</body>
	</html>
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
		$directory = ".".DIRECTORY_SEPARATOR.$thisprojid;
		mkdir($directory);
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
		$directory = ".".DIRECTORY_SEPARATOR.$thisproject->projectid.DIRECTORY_SEPARATOR.$thisfileid;
		mkdir($directory);
?><p class="txtstd gray text center">File aggiunto</p><?php
	}

	//Upload del file
	if (isset($_GET['upload'])) {
		
		$thisfile = searchFileById($_GET['thefileid']);
		$thisproject = getProjectOfFile($_GET['thefileid']);
		$lol = basename($_FILES['uploadfile']['name']);
		$targetdir = ".".DIRECTORY_SEPARATOR.$thisproject->projectid.DIRECTORY_SEPARATOR.$_GET['thefileid'];
		$targetfile = $targetdir.DIRECTORY_SEPARATOR.basename($_FILES['uploadfile']["name"]);
		if (move_uploaded_file($_FILES['uploadfile']["tmp_name"], $targetfile)) {
?><p class="txtstd text gray center">File caricato con successo</p><?php
			$thisfile->link = "http://" . $_SERVER['SERVER_NAME'] . dirname(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)) . DIRECTORY_SEPARATOR . $thisproject[0]->projectid . DIRECTORY_SEPARATOR . $thisfile[0]->fileid . DIRECTORY_SEPARATOR . basename($_FILES['uploadfile']['name']);
			$xml->asXML($xmlfile);
		} else {
?><p class="txtstd text gray center">File NON caricato. Errore</p><?php
		}
	}

	//Modifica effettiva del titolo della pagina
	if (isset($_POST['newpagetitle']) && isset($_POST['newpagedescription'])) {
		$xml->pagetitle = $_POST['newpagetitle'];
		$xml->pagedescription = $_POST['newpagedescription'];
		$xml->homepage = $_POST['newpagehomeredirect'];
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
		$thisfile->filedescription = $_POST['newfiledescription'];
		$thisfile->filenotes = $_POST['newfilenotes'];
		$xml->asXML($xmlfile);
?><p class="txtstd text gray center">File modificato con successo</p><?php	
	}

	//Modifica effettiva dei colori
	if (isset($_POST['backgroundcolor'])) {
		$options = $xml->options;
		$options->backgroundcolor = $_POST['backgroundcolor'];
		$options->sectioncolor = $_POST['sectioncolor'];
		$options->textcolor = $_POST['textcolor'];
		$options->linkcolor = $_POST['linkcolor'];
		$options->hovercolor = $_POST['hovercolor'];
		$xml->asXML($xmlfile);
?><p class="txtstd text gray center">Colori modificati con successo</p><?php
	}

	//Modifica effettiva delle grandezze dei caratteri
	if (isset($_POST['globalmag'])) {
		$options = $xml->options;
		$options->globalmag = $_POST['globalmag'];
		$options->sectionsmag = $_POST['sectionsmag'];
		$options->txtstdmag = $_POST['txtstdmag'];
		$options->pagetitlemag = $_POST['pagetitlemag'];
		$xml->asXML($xmlfile);
?><p class="txtstd text gray center">Grandezze caratteri modificate con successo</p><?php
	}

	//Modifica effettiva dei bordi pagina
	if (isset($_POST['dxmargin'])) {
		$options = $xml->options;
		$options->dxmargin = $_POST['dxmargin'];
		$options->sxmargin = $_POST['sxmargin'];
		$xml->asXML($xmlfile);
?><p class="txtstd text gray center">Ampiezza margini modificata con successo</p><?php
	}

	//Eliminazione effettiva del file
	if (isset($_POST['delfileid'])) {
		$thisproject = getProjectOfFile($_POST['delfileid']);
		$thisfile = searchFileById($_POST['delfileid']);
		unset($thisfile[0]);
		$xml->asXML($xmlfile);
		$directory = ".".DIRECTORY_SEPARATOR.$thisproject->projectid.DIRECTORY_SEPARATOR.$_POST['delfileid'];
		deleteNonEmptyDir($directory);
?><p class="txtstd text gray center">File definitivamente eliminato</p><?php
	}

	//Eliminazione effettiva del progetto
	if (isset($_POST['delprojectid'])) {
		$thisproject = searchProjectById($_POST['delprojectid']);
		unset($thisproject[0]);
		$xml->asXML($xmlfile);
		$directory = ".".DIRECTORY_SEPARATOR.$_POST['delprojectid'];
		deleteNonEmptyDir($directory);
?><p class="txtstd text gray center">Progetto definitivamente eliminato</p><?php
	}

	//Conferma di eliminazione del file
	if (isset($_GET['delfile'])) {
		$thisfile = searchFileById($_GET['delfile']);
?>
		<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $_GET['project'] ?>" method="POST">
			<table><tbody>
				<tr><td>Titolo File:</td><td><?= $thisfile->filedescription ?></td></tr>
				<tr><td colspan="2"><input type="hidden" value="<?= $thisfile->fileid ?>" name="delfileid"><button>Elimina definitivamente il file</button></td></tr>
			</tbody></table>
		</form><hr/>
<?php
	}

	//Conferma di eliminazione del progetto
	if (isset($_GET['delproject'])) {
		$thisproject = searchProjectById($_GET['delproject']);
?>
		<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
			<table><tbody>
				<tr><td>Titolo Progetto:</td><td><?= $thisproject->projectname ?></td></tr>
				<tr><td colspan="2"><input type="hidden" value="<?= $thisproject->projectid ?>" name="delprojectid"><button>Elimina definitivamente il progetto (e tutti i suoi file)</button></td></tr>
			</tbody></table>
		</form><hr/>
<?php
	}


	//Modifica del titolo della pagina
	if (isset($_GET['modpagetitle'])) {
?>
	<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<table><tbody>
			<tr><td>Titolo Pagina:</td><td><input type="text" name="newpagetitle" value="<?= $xml->pagetitle ?>" size="100"/></td></tr>
			<tr><td>Descrizione:</td><td><textarea rows="20" name="newpagedescription" cols="100"><?= $xml->pagedescription ?></textarea></td></tr>
			<tr><td>Home Page redirect:</td><td><input type="text" name="newpagehomeredirect" cols="100" value="<?= $xml->homepage ?>"/></td></tr>
			<tr><td colspan="2"><button>Modifica</button></td></tr>
		</tbody></table>
	</form><hr/>
<?php
	}

	//Modifica del titolo del progetto
	if (isset($_GET['modprojecttitle'])) {
		$thisproject = searchProjectById($_GET['modprojecttitle']);
?>
	<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
		<p class="txtbig section center">Modifica titolo e descrizione del progetto <?= $thisproject->projectname ?></p>
		<table><tbody>
			<tr><td>Titolo:</td><td><input type="text" name="newprojecttitle" value="<?= $thisproject->projectname ?>" size="100"/></td></tr>
			<tr><td>Descrizione:</td><td><textarea rows="20" cols="100" name="newprojectdescription"><?= $thisproject->projectdescription ?></textarea></td></tr>
			<tr><td colspan="2"><input type="hidden" name="modprojectid" value="<?= $thisproject->projectid ?>"/><button>Modifica</button></td></tr>
		</tbody></table>
	</form>
	<ul><li><a href="<?= $_SERVER['PHP_SELF'] ?>?delproject=<?= $thisproject->projectid ?>">ELIMINA IL PROGETTO (DEFINITIVO)</a></li></ul><hr/>
<?php	
	}

	//Modifica del file
	if (isset($_GET['file'])) {
		$thisfile = searchFileById($_GET['file']);
?>
	<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $_GET['project'] ?>" method="POST">
		<p class="txtbig section center">Modifica del file <?= $thisfile->filedescription ?></p>
		<table><tbody>
			<tr><td>Link al file:</td><td><input type="text" name="newfilelink" value="<?= $thisfile->link ?>" size="80"/></td></tr>
			<tr><td>Titolo file:</td><td><input type="text" name="newfiledescription" value="<?= $thisfile->filedescription ?>" size="80"/></td></tr>
			<tr><td>Note al file:</td><td><textarea rows="20" cols="100" name="newfilenotes"><?= $thisfile->filenotes ?></textarea></td></tr>
			<tr><td colspan="2"><input type="hidden" name="modfileid" value="<?= $thisfile->fileid ?>"/><button>Modifica File</button></td></tr>
		</tbody></table>
	</form>
	<br/><br/>
	<p class="txtbig section center">Upload rapido del file</p>
	<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>?project=<?= $_GET['project'] ?>&upload&thefileid=<?= $thisfile->fileid ?>&file=<?= $_GET['file'] ?>" method="POST" enctype="multipart/form-data">
		<table><tbody>
			<tr><td>File da uploadare:</td><td><input type="file" name="uploadfile"/></td></tr>
			<tr><td colspan="2"><button>Salva File</button></td></tr>
		</tbody></table>
	</form>
	<ul><li><a href="<?= $_SERVER['PHP_SELF'] ?>?delfile=<?= $thisfile->fileid ?>&project=<?= $_GET['project'] ?>">ELIMINA IL FILE (DEFINITIVO)</a></li></ul><hr/>
<?php
	}

	//Modifica delle impostazioni
	if (isset($_GET['modoptions'])) {
		$options = $xml->options;
?>
		<p class="txtbig section center">Modifica impostazioni</p>
		<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>" method="POST">
			<table><tbody>
				<tr><td>Colore di sfondo pagina:</td><td><input class="color" name="backgroundcolor" value="<?= $options->backgroundcolor ?>"/></td></tr>
				<tr><td>Colore dei titoli:</td><td><input class="color" name="sectioncolor" value="<?= $options->sectioncolor ?>"/></td></tr>
				<tr><td>Colore del testo:</td><td><input class="color" name="textcolor" value="<?= $options->textcolor ?>"/></td></tr>
				<tr><td>Colore dei link:</td><td><input class="color" name="linkcolor" value="<?= $options->linkcolor ?>"/></td></tr>
				<tr><td>Colore dei link evidenziati:</td><td><input class="color" name="hovercolor" value="<?= $options->hovercolor ?>"/></td></tr>
				<tr><td>Grandezza carattere della pagina (globale):</td><td><input type="range" unit="%" name="globalmag" min="30" max="200" step="5" value="<?= $options->globalmag ?>"/></td></tr>
				<tr><td>Grandezza titolo pagina:</td><td><input type="range" unit="em" name="pagetitlemag" min="1.50" max="4" step="0.05" value="<?= $options->pagetitlemag ?>"/></td></tr>
				<tr><td>Grandezza titoletti:</td><td><input type="range" unit="em" name="sectionsmag" min="1.00" max="3" step="0.05" value="<?= $options->sectionsmag ?>"/></td></tr>
				<tr><td>Grandezza testo standard:</td><td><input type="range" unit="em" name="txtstdmag" min="0.75" max="2" step="0.05" value="<?= $options->txtstdmag ?>"/></td></tr>
				<tr><td>Margine pagina destro:</td><td><input type="range" unit="%" name="dxmargin" min="3" max="30" step="0.5" value="<?= $options->dxmargin ?>"/></td></tr>
				<tr><td>Margine pagina sinistro:</td><td><input type="range" unit="%" name="sxmargin" min="3" max="30" step="0.5" value="<?= $options->sxmargin ?>"/></td></tr>
				<tr><td colspan="2"><button>Modifica Impostazioni</button></td></tr>
			</tbody></table>
		</form><br/><br/>
		<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>?modoptions" method="POST">
			<input type="hidden" name="backgroundcolor" value="E5E4E2"/><input type="hidden" name="sectioncolor" value="15317E"/><input type="hidden" name="textcolor" value="368BC1"/><input type="hidden" name="linkcolor" value="0000A0"/><input type="hidden" name="hovercolor" value="1F45FC"/>
			<button>Ripristina colori Predefiniti</button>
		</form>
		<form accept-charset="utf-8" action="<?= $_SERVER['PHP_SELF'] ?>?modoptions" method="POST">
			<input type="hidden" name="globalmag" value="90"/><input type="hidden" name="pagetitlemag" value="2.4"/><input type="hidden" name="sectionsmag" value="1.6"/><input type="hidden" name="txtstdmag" value="1.3"/>
			<button>Ripristina grandezze caratteri Predefinite</button>
		</form>
		<hr/>
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
	</ul><hr/>
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
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?modoptions">MODIFICA IMPOSTAZIONI DI LAYOUT</a></li>
			<li><a href="<?= $_SERVER['PHP_SELF'] ?>?logout">LOGOUT</a></li>
			</ul><hr/>
	<?php
?>
	<br/><br/><br/><br/><br/>
	<div id="footer">
		<span class="footcont">
			<a href="files.xml">Pagina Appunti</a>
		</span>
	</div>
	</body>
</html>

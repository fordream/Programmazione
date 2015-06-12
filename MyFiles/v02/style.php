<?php
	header("Content-type: text/CSS; charset=utf-8");
	$xmlfile = "files.xml";
	$xml = simplexml_load_file($xmlfile);
	$options = $xml->options;
?>

body { background-color: #<?= $options->backgroundcolor ?>; text-align: center; font-size: <?= $options->globalmag ?>%; }
form {display: inline-block; }
ul, p {text-align: left; }
#pagecontainer { margin-left: <?= $options->sxmargin ?>%; margin-right: <?= $options->dxmargin ?>%; margin-top: 50px; }

.text {font-family: Times New Roman, serif; font-weight: 500; }
.section {font-family: Verdana, sans-serif; font-weight: 700; color: #<?= $options->sectioncolor ?>; }

.black {color: black; }
.gray {color: #<?= $options->textcolor ?>; }

a {text-decoration: none; color: #<?= $options->linkcolor ?>; font-weight: 600; }
a:hover {text-decoration: none; color: #<?= $options->hovercolor ?>; font-weight: 650; }
a:visited {text-decoration: none; color: #<?= $options->linkcolor ?>; font-weight: 600; }
a:visited:hover {text-decoration: none; color: #<?= $options->hovercolor ?>; font-weight: 650; }

#footer {position: fixed; right: auto; left: auto; bottom: 10px; margin: 5px 10px; }
.footcont {position: relative; margin: 0px 10px; padding: 5px 10px; border-radius: 5px; }

table {border: 0px; }
table tbody tr td {padding: 3px 12px; margin: 1px 10px; }

.txtstd {font-size: <?= $options->txtstdmag ?>em; }
.txtbig {font-size: <?= $options->sectionsmag ?>em; }
.txthuge {font-size: <?= $options->pagetitlemag ?>em; }

.center {text-align: center; }
.justify {text-align: justify; }
.right {text-align: right; }
.left {text-align: left; }

.topbottomseparate {margin-top: 40px; margin-bottom: 20px; }

#myfilesinfo {position: fixed; right: 5px; bottom: 10px; margin: 5px 10px; }
#myfilesdownload, #myfilesbutton {position: relative; margin: 0px 10px; padding: 5px 10px; }


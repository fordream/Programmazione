<?php
	//CSS dove possiamo modificare lo stile, visto che ne imposterò tre di default
	//Gli stili si danno nell'ordine:
	/*
		0) Colore di Sfondo
		1) Colore della section
		2) Colore del testo grigio
		3) Colore dei link
		4) Colore dei link hover
	*/
	$default_style = array("#FFF5EE", "#000000", "#493D26", "#483C32", "#C58917");
	$blue_style = array("#E5E4E2", "#15317E", "#368BC1", "#0000A0", "#1F45FC");

	$style = $blue_style;
?>


body { background-color: <?= $style[0] ?>; }
#pagecontainer { margin-left: 20%; margin-right: 20%; margin-top: 50px; }

.text {font-family: Times New Roman, serif; font-weight: 500; }
.section {font-family: Verdana, sans-serif; font-weight: 700; color: <?= $style[1] ?>; }

.black {color: black; }
.gray {color: <?= $style[2] ?>; }

a {text-decoration: none; color: <?= $style[3] ?>; font-weight: 600; }
a:hover {text-decoration: none; color: <?= $style[4] ?>; font-weight: 650; }
a:visited {text-decoration: none; color: <?= $style[3] ?>; font-weight: 600; }
a:visited:hover {text-decoration: none; color: <?= $style[4] ?>; font-weight: 650; }

#footer {position: fixed; right: auto; left: auto; bottom: 10px; margin: 5px 10px; }
#backhome {position: relative; margin: 0px 10px; padding: 5px 10px; border-radius: 5px; }

table {border: 0px; }
table tbody tr td {padding: 3px 7px; margin: 1px 10px; }

.txtsmall {font-size: small; }
.txtstd {font-size: 110%; }
.txtbig {font-size: large; }
.txthuge {font-size: x-large; }
.txtgiant {font-size: xx-large; }

.center {text-align: center; }
.justify {text-align: justify; }
.right {text-align: right; }
.left {text-align: left; }

.topbottomseparate {margin-top: 40px; margin-bottom: 20px; }


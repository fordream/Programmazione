<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/CSS" />
		<script type="text/javascript" src="jquery-2.1.4.js"></script>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<title>Lista di Persone</title>
		<script type="text/javascript">
			$(document).ready(function () {
				alert("ready");
				$("#srcnome").val("Loltipo");
				$("#srcnome").on("input", function () {
						alert("Guardiamoci");
						});
			<!--			reg = new RegExp ($("#srcnome").val(), "g");
						$("#persone tbody tr").each( function() {
							var tnome = $(this).find("td.nome").html();
							if (reg.test(tnome) == true) {
								$(this).style = "display: block;";
							} else {
								$(this).style = "display: none;";
							}
						});
					});-->
				$("#srcmatricola").change(function () {});
				$("#srcmail").change(function () {});
			});
		</script>
	</head>
	<body>

		<div id="pagecontainer">


			<table border="0" id="#persone">
			<thead>
				<tr><th class="center">Nome</th><th class="center">Matricola</th><th class="center">Mail</th><th>Modifica</th></tr>
				<tr><th class="center"><input type="text" id="#srcnome"/></th><th class="center"><input type="text" id="#srcmatricola"/></th><th class="center"><input type="text" id="#srcmail"/></th><th></th></tr>
			</thead>
			<tbody>	
				<xsl:for-each select="root/person">
						<tr>
							<td class="nome txtstd gray justify text"><xsl:value-of select="nome"/></td>
							<td class="matricola txtstd gray center text"><xsl:value-of select="matricola"/></td>
							<td class="mail txtstd gray justify text"><xsl:value-of select="mail"/></td>
							<td class="txtstd gray justify text"></td>
						</tr>
				</xsl:for-each>
			</tbody>
			</table>
				
		</div>
		<br/><br/><br/><br/><br/><br/>

	</body>
</html>
</xsl:template>

</xsl:stylesheet> 

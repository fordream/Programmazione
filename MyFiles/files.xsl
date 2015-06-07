<?xml version="1.0" encoding="UTF-8"?>

<xsl:stylesheet version="1.0"
xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

<xsl:template match="/">
<html>
	<head>
		<link href="style.css" rel="stylesheet" type="text/CSS" />
		<title><xsl:value-of select="root/pagetitle"/></title>
	</head>
	<body>

		<div id="pagecontainer">

			<h2 class="section txthuge center"><xsl:value-of select="root/pagetitle"/></h2>
			<p class="txtstd text gray justify"><xsl:value-of select="root/pagedescription"/></p>

			<xsl:for-each select="root/project">
				
				<div class="topbottomseparate">
				<h3 class="section txtbig left"><xsl:value-of select="projectname"/></h3>
				<p class="txtstd text gray justify"><xsl:value-of select="projectdescription"/></p>
				<table border="0">
				<tbody>
					<xsl:for-each select="content/file">
					<tr>
						<td class="text txtstd left">
							<xsl:element name="a">
							    <xsl:attribute name="href">
								<xsl:value-of select="link"/>
							    </xsl:attribute>
							    <xsl:value-of select="filedescription"/>
							</xsl:element>
						</td>
						<td class="txtstd gray justify text"><xsl:value-of select="filenotes"/></td>
					</tr>
					</xsl:for-each>
				</tbody>
				</table>
				<hr/>
				</div>
			</xsl:for-each>
		</div>
		<br/><br/><br/><br/><br/><br/>
		<div id="footer">
			<span class="footcont">
				<a href="index.php" class="txtstd section center">Torna alla Home Page</a>
			</span>
			<span class="footcont">
				<a href="modify.php" class="txtstd section center">Modifica pagina</a>
			</span>
		</div>
	</body>
</html>
</xsl:template>

</xsl:stylesheet> 

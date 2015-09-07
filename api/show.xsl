<?xml version="1.0" encoding="UTF-8"?>
<html xsl:version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
  <body style="font-family:Arial;font-size:12pt;background-color:#EDEDED;">
    <h1 style="margin:20px 10px;font-size:26pt;font-weight:bold;"><xsl:value-of select="qalist/@name"/></h1>
    <xsl:for-each select="qalist/entry">
      <div style="margin:10px 10px;">
      <div style="background-color:#1972A9;color:white;font-size:11pt;padding:10px 15px;margin:0;">
	<xsl:value-of select="@id"/>&#160;&#160;&#160;<span style="font-weight:bold"><xsl:value-of select="question"/></span>
      </div>
      <div style="margin:0;padding:10px 15px;font-size:10pt;background-color:#fff;white-space:pre-wrap;">
	  <xsl:value-of select="answer"/>
      </div>
      </div>
    </xsl:for-each>
  </body>
</html>

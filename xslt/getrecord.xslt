<?xml version="1.0" encoding="UTF-8"?>

<!-- Extract the metadata record from an OAI-PMH GetRecord response -->
<xsl:stylesheet 
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:oai="http://www.openarchives.org/OAI/2.0/"
    strip-namespace-prefixes="oai"
  >

  <xsl:output method="xml" indent="yes" encoding="UTF-8" />
  <xsl:strip-space elements="*"/>

  <!-- extract first metadata element -->
  <xsl:template match="/oai:OAI-PMH">
    <xsl:apply-templates select="oai:GetRecord/oai:record/oai:metadata/*[1]"/>
  </xsl:template>

  <!-- avoid namespace prefix -->
  <xsl:template match="*">
    <xsl:element name="{local-name(.)}" namespace="{namespace-uri(.)}">
      <xsl:copy-of select="@*"/>
      <xsl:apply-templates/>
    </xsl:element>
  </xsl:template>

</xsl:stylesheet>  

<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
  xmlns:e="https://schema.easydb.de/EASYDB/1.0/objects/">
  <xsl:output method="text"/>
  <xsl:template match="e:objects">
    <xsl:value-of select="e:objekttyp/e:_uuid"/>
  </xsl:template>
  <xsl:template match="*"/>
</xsl:stylesheet>

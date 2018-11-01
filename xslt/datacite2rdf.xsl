<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:d="http://datacite.org/schema/kernel-4"
    exclude-result-prefixes="d">
    
  <xsl:output method="xml" indent="yes" encoding="UTF-8" />

  <xsl:template match="d:resource">
    <rdf:RDF about="{d:identifier[@identifierType='URI'][1]}">
      <!-- TODO -->
    </rdf:RDF>
  </xsl:template>

</xsl:stylesheet>

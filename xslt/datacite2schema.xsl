<?xml version="1.0" encoding="UTF-8"?>
<!-- 

Minimal draft of DataCite 4.1 to Schema.org mapping.
     
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:datacite="http://datacite.org/schema/kernel-4"
    xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
    xmlns:schema="http://schema.org/"
    xmlns:owl="http://www.w3.org/2002/07/owl#"
    xmlns="http://www.w3.org/1999/xhtml"
    exclude-result-prefixes="datacite">
    
    <xsl:output method="html" indent="yes" encoding="UTF-8" />

    <!-- We assume all identifiers are URIs (DataCite 4.1 only allows DOI) -->
    <xsl:template match="datacite:identifier">
      <xsl:attribute name="about">
        <xsl:value-of select="."/>
      </xsl:attribute>
    </xsl:template>

    <xsl:template match="datacite:resource">
      <div typeof="schema:Dataset" vocab="http://schema.org/">
        <xsl:apply-templates select="datacite:identifier"/>
        <xsl:apply-templates select="*[local-name() != 'identifier']"/>
      </div>
    </xsl:template>
 
    <xsl:template match="datacite:creators">
      <xsl:for-each select="datacite:creator">
        <div rel="creator" typeof="Person">
          <!-- Assume all nameIdentifiers are URIs and the first is primary -->
          <xsl:if test="datacite:nameIdentifier">
            <xsl:attribute name="resource">
              <xsl:value-of select="datacite:nameIdentifier[1]"/>
            </xsl:attribute>
          </xsl:if>
          <xsl:for-each select="datacite:nameIdentifier[position()&gt;1]">
            <a property="owl:sameAs" href="{.}">
              <xsl:value-of select="."/>
            </a>
          </xsl:for-each>
          <xsl:apply-templates/>
        </div>
      </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:creatorName|datacite:contributorName">
      <span property="name">
        <xsl:value-of select="."/>
      </span>
    </xsl:template>

    <xsl:template match="datacite:givenName|datacite:familyName">
      <span property="{local-name(.)}">
        <xsl:value-of select="."/>
      </span>
    </xsl:template>

    <xsl:template match="datacite:affiliation">
      <div rel="affiliation">
        <!-- TODO: identifier -->
        <span property="name">
          <xsl:value-of select="."/>
        </span>
      </div>
    </xsl:template>

    <xsl:template match="@xml:lang">
      <xsl:copy-of select="."/>
    </xsl:template>

    <xsl:template match="datacite:titles">
      <xsl:for-each select="datacite:title">
        <div property="name">
          <xsl:apply-templates select="@xml:lang"/>
          <xsl:apply-templates/>
        </div>
      </xsl:for-each>
    </xsl:template>

    <xsl:template match="datacite:publisher">
      <div rel="publisher">
        <!-- TODO: identifier -->
        <span property="name">
          <xsl:value-of select="."/>
        </span>
      </div>
    </xsl:template>
    
    <xsl:template match="datacite:publicationYear">
      <div property="datePublished">
        <xsl:value-of select="."/>
      </div>
    </xsl:template>
    
    <xsl:template match="datacite:subjects">
      <xsl:for-each select="datacite:subject[@valueURI]">
        <a rel="about" href="{@valueURI}">
          <span property="name">
            <xsl:apply-templates select="@xml:lang"/>
            <xsl:apply-templates/>
          </span>
        </a>
      </xsl:for-each>
      <xsl:for-each select="datacite:subject[not(@valueURI)]">
        <div property="keywords">
          <xsl:apply-templates select="@xml:lang"/>
          <xsl:apply-templates/>
        </div>
      </xsl:for-each>
    </xsl:template>
    
    <!--
    <xsl:template match="datacite:contributors">
        <xsl:for-each select="datacite:contributor">
            <xsl:element name="dc:contributor">
                <xsl:value-of select="./datacite:contributorName"/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:fundingReferences">
        <xsl:for-each select="distinct-values( descendant::datacite:funderName/text() )">
            <xsl:element name="dc:contributor">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:dates">
        
        <xsl:for-each select="datacite:date">
            <xsl:element name="dc:date">
                <xsl:if test="@dateType">
                    <xsl:value-of select="@dateType"/><xsl:text>: </xsl:text>                        
                </xsl:if>
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    -->

    <xsl:template match="datacite:language">
      <div rel="inLanguage">
        <xsl:value-of select="."/>
      </div>
    </xsl:template>
    
    <!--xsl:template match="datacite:resourceType">
        <xsl:for-each select=".">
            <xsl:if test="normalize-space(@resourceTypeGeneral)">
                <xsl:element name="dc:type">
                    <xsl:value-of select="@resourceTypeGeneral"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="normalize-space(.)">
                <xsl:element name="dc:type">
                    <xsl:value-of select="."/>
                </xsl:element>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:alternateIdentifiers">
        <xsl:for-each select="datacite:alternateIdentifier">
            <xsl:element name="dc:identifier">
                <xsl:choose>
                    <xsl:when test="string-length(@alternateIdentifierType) &gt; 0">
                        <xsl:value-of select="translate(@alternateIdentifierType,  $uppercase, $smallcase)"/>
                        <xsl:text>:</xsl:text>
                    </xsl:when>
                </xsl:choose>
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:relatedIdentifiers">
        <xsl:for-each select="datacite:relatedIdentifier">
            <xsl:element name="dc:relation">
                <xsl:choose>
                    <xsl:when test="string-length(@relatedIdentifierType) &gt; 0">
                        <xsl:value-of select="translate(@relatedIdentifierType,  $uppercase, $smallcase)"/>
                        <xsl:text>:</xsl:text>
                    </xsl:when>
                </xsl:choose>
                <xsl:value-of select="."/>            
            </xsl:element>
        </xsl:for-each>            
    </xsl:template>
    
    <xsl:template match="datacite:sizes">
        <xsl:for-each select="datacite:size">
            <xsl:element name="dc:format">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:formats">
        <xsl:for-each select="datacite:format">
            <xsl:element name="dc:format">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:rightsList">
        <xsl:for-each select="datacite:rights">
            <xsl:element name="dc:rights">
                <xsl:value-of select="."/>
            </xsl:element>
            <xsl:if test="@rightsURI">
                <xsl:element name="dc:rights">
                    <xsl:value-of select="@rightsURI"/>
                </xsl:element>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:geoLocations">
        <xsl:for-each select="datacite:geoLocation">
            <xsl:for-each select="child::node()">
                <xsl:element name="dc:coverage">
                    <xsl:call-template name="node_to_string">
                        <xsl:with-param name="delimiter" select="' '" />
                    </xsl:call-template>
                </xsl:element>
            </xsl:for-each>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:descriptions">
        <xsl:for-each select="datacite:description">
            <xsl:if test="normalize-space(@descriptionType)">
                <xsl:element name="dc:description">
                    <xsl:value-of select="@descriptionType"/>
                </xsl:element>
            </xsl:if>
            <xsl:if test="normalize-space(.)">
                <xsl:element name="dc:description">
                    <xsl:if test="@xml:lang">
                        <xsl:attribute name="xml:lang"><xsl:value-of select="@xml:lang" /></xsl:attribute>
                    </xsl:if>
                    <xsl:value-of select="."/>
                </xsl:element>
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
   
    <xsl:template name="node_to_string">
        <xsl:param name="delimiter" select="' '"/>                
        <xsl:for-each select="descendant::text()">
            <xsl:value-of select="string( . )"/>
            <xsl:if test="position() != last()">
                <xsl:value-of select="$delimiter" />
            </xsl:if>
        </xsl:for-each>
    </xsl:template>
-->
    
  <xsl:template match="*"/>

</xsl:stylesheet>

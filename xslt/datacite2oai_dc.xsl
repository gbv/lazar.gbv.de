<?xml version="1.0" encoding="UTF-8"?>
<!-- 

Source: https://github.com/datacite/oaip/blob/master/src/main/webapp/xsl/kernel4_to_oaidc.xsl

Released under the Apache 2 License.

Ported from XSLT 2.0 to XSLT 1.0.
     
-->
<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xmlns:datacite="http://datacite.org/schema/kernel-4"
    xmlns:dc="http://purl.org/dc/elements/1.1/"
    xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/"    
    exclude-result-prefixes="datacite">
    
    <xsl:output method="xml" indent="yes" encoding="UTF-8" />
    
    <xsl:variable name="smallcase" select="'abcdefghijklmnopqrstuvwxyz'" />
    <xsl:variable name="uppercase" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'" />

    <xsl:template match="datacite:identifier">
        <dc:identifier>
            <xsl:value-of select="."/>
        </dc:identifier>
    </xsl:template>
    
    <xsl:template match="datacite:creators">
        <xsl:for-each select="datacite:creator">
            <xsl:element name="dc:creator">
                <xsl:value-of select="./datacite:creatorName"/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:titles">
        <xsl:for-each select="datacite:title">
            <xsl:element name="dc:title">
                <xsl:if test="@xml:lang">
                    <xsl:attribute name="xml:lang"><xsl:value-of select="@xml:lang"/></xsl:attribute>
                </xsl:if>
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:publisher">
        <xsl:for-each select=".">
            <xsl:element name="dc:publisher">
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
    <xsl:template match="datacite:publicationYear">
        <xsl:element name="dc:date">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="datacite:subjects">
        <xsl:for-each select="datacite:subject">
            <xsl:element name="dc:subject">
                <xsl:if test="@xml:lang">
                    <xsl:attribute name="xml:lang"><xsl:value-of select="@xml:lang"/></xsl:attribute>
                </xsl:if>
                <xsl:value-of select="."/>
            </xsl:element>
        </xsl:for-each>
    </xsl:template>
    
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
    
    <xsl:template match="datacite:language">
        <xsl:element name="dc:language">
            <xsl:value-of select="."/>
        </xsl:element>
    </xsl:template>
    
    <xsl:template match="datacite:resourceType">
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
    
    <xsl:template match="datacite:resource">
      <oai_dc:dc xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai_dc/ http://www.openarchives.org/OAI/2.0/oai_dc.xsd">

            <xsl:apply-templates select="datacite:titles"/>
            <xsl:apply-templates select="datacite:creators"/>
            <xsl:apply-templates select="datacite:publisher"/>
            <xsl:apply-templates select="datacite:publicationYear"/>
            <xsl:apply-templates select="datacite:dates"/>            
            <xsl:apply-templates select="datacite:identifier"/>
            <xsl:apply-templates select="datacite:alternateIdentifiers"/>
            <xsl:apply-templates select="datacite:relatedIdentifiers"/>
            <xsl:apply-templates select="datacite:subjects"/>
            <xsl:apply-templates select="datacite:descriptions"/>
            <xsl:apply-templates select="datacite:contributors"/>
            <xsl:apply-templates select="datacite:fundingReferences"/>
            <xsl:apply-templates select="datacite:language"/>
            <xsl:apply-templates select="datacite:resourceType"/>
            <xsl:apply-templates select="datacite:sizes"/>
            <xsl:apply-templates select="datacite:formats"/>
            <xsl:apply-templates select="datacite:rightsList"/>
            <xsl:apply-templates select="datacite:geoLocations"/>
        </oai_dc:dc>
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
    
</xsl:stylesheet>

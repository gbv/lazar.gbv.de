<?xml version="1.0" encoding="utf-8"?>
<!--

  This XSLT script converts a OAI-PMH 2.0 responses into XHTML.

  It is based on script by Christopher Gutteridge, University of
  Southampton, licensed under the GNU General Public License:
  <https://www.gnu.org/licenses/gpl-3.0.en.html>

  Copyright of the original script by University of Southampton.
  Copyright if this script by Jakob Voß.
-->

<!--

  Not Done
    The 'about' section of 'record'
    The 'compession' part of 'identify'
    The optional attributes of 'resumptionToken'
    The optional 'setDescription' container of 'set'

  All the links just link to oai_dc versions of records.

-->

<xsl:stylesheet
    version="1.0"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
    xmlns:oai="http://www.openarchives.org/OAI/2.0/"
>

<!-- custom configuration -->
<xsl:param name="brand" select="/processing-instruction('brand')"/>
<xsl:param name="brandUrl" select="/processing-instruction('brandUrl')"/>

<!-- link to this script -->
<xsl:param name="xslt" select="substring-before(substring-after(/processing-instruction('xml-stylesheet'),'href=&quot;'),'&quot;')"
/>

<!-- link to optional CSS file (Bootstrap CSS 4 recommended) -->
<xsl:param name="css" select="/processing-instruction('css')" />

<!-- current request and verb -->
<xsl:param name="request" select="/oai:OAI-PMH/oai:request" />
<xsl:param name="verb" select="$request/@verb" />

<!--
<xsl:param name='identifier'
    select="substring-before(concat(substring-after(/oai:OAI-PMH/oai:request,'identifier='),'&amp;'),'&amp;')"
/-->

<xsl:output method="html"/>

<!-- process space separated list of CSS files -->
<xsl:template name="css">
  <xsl:param name="url" select="normalize-space($css)"/>
  <xsl:if test="string-length($url)">
    <xsl:if test="not(contains($url, ' '))">
      <link rel="stylesheet" href="{$url}"/>
    </xsl:if>
    <xsl:if test="contains($url, ' ')">
      <link rel="stylesheet" href="{substring-before($url,' ')}"/>
      <xsl:call-template name="css">
        <xsl:with-param name="url" select="substring-after($url, ' ')"/>
      </xsl:call-template>
    </xsl:if>
  </xsl:if>
</xsl:template>


<xsl:template match="/">
  <html lang="en">
    <head>
      <meta charset="utf-8"/>
      <title>OAI 2.0 Request Results</title>
      <xsl:call-template name="css"/>
    </head>
    <body>
      <xsl:call-template name="navbar"/>
      <div class="container-fluid">
        <xsl:apply-templates select="oai:OAI-PMH" />
        <xsl:call-template name="footer"/>
      </div>
    </body>
  </html>
</xsl:template>

<xsl:template name="footer">
  <hr/>
  <p>
    This view of an
    <a href="https://www.openarchives.org/pmh/">OAI-PMH 2.0</a>
    response has been created by <a href="{$xslt}">an XSLT script</a>
    based on a script by Christopher Gutteridge, licensed
    under the <a href="http://www.gnu.org">GPL</a>.
  </p>
</xsl:template>

<xsl:template name="navbar">
  <nav class="navbar navbar-expand navbar-dark bg-dark fixed-top">
    <xsl:if test="$brand">
      <a class="navbar-brand" href="{$brandUrl}">
        <xsl:value-of select="$brand"/>
      </a>
    </xsl:if>
    <ul class="navbar-nav">
      <li>
        <xsl:attribute name="class">nav-item
          <xsl:if test="$verb ='Identify'">active</xsl:if>
        </xsl:attribute>
        <a class="nav-link" href="?verb=Identify">Identify</a>
      </li>
      <li>
        <xsl:attribute name="class">nav-item
          <xsl:if test="$verb ='ListRecords'">active</xsl:if>
        </xsl:attribute>
        <a class="nav-link" href="?verb=ListRecords&amp;metadataPrefix=oai_dc">ListRecords</a>
      </li>
      <li>
        <xsl:attribute name="class">nav-item
          <xsl:if test="$verb ='ListSets'">active</xsl:if>
        </xsl:attribute>
        <a class="nav-link" href="?verb=ListSets">ListSets</a>
      </li>
      <li>
        <xsl:attribute name="class">nav-item
          <xsl:if test="$verb ='ListMetadataFormats'">active</xsl:if>
        </xsl:attribute>
        <a class="nav-link" href="?verb=ListMetadataFormats">ListMetadataFormats</a>
      </li>
      <li>
        <xsl:attribute name="class">nav-item
          <xsl:if test="$verb ='ListIdentifiers'">active</xsl:if>
        </xsl:attribute>
        <a class="nav-link" href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc">ListIdentifiers</a>
      </li>
    </ul>
  </nav>
</xsl:template>

<xsl:template match="oai:request" mode="request">
  <xsl:variable name="url">
    <xsl:value-of select="."/>
    <xsl:for-each select="@*">
      <xsl:choose>
        <xsl:when test="position() = 1">?</xsl:when>
        <xsl:otherwise>&amp;</xsl:otherwise>
      </xsl:choose>
      <xsl:value-of select="local-name()"/>
      <xsl:text>=</xsl:text>
      <xsl:value-of select="."/>
    </xsl:for-each>
  </xsl:variable>
  <a href="{$url}"><xsl:value-of select="$url"/></a>
</xsl:template>

<xsl:template match="oai:OAI-PMH">
  <h2>Request</h2>
  <p><xsl:apply-templates select="$request" mode="request"/></p>
  <h2>
    Response
    <small><code><xsl:value-of select="oai:responseDate"/></code></small>
  </h2>
  <xsl:apply-templates/>
  <xsl:apply-templates select="*/oai:resumptionToken" />
</xsl:template>

<xsl:template match="oai:error">
  <div class="alert alert-danger" role="alert">
    <b><code><xsl:value-of select="@code"/></code></b>
    <br/>
    <xsl:value-of select="." />
  </div>
</xsl:template>

<!-- IDENTIFY -->

<xsl:template match="/oai:OAI-PMH/oai:Identify">
  <table class="table table-bordered">
    <tr><td class="key">Repository Name</td>
    <td class="value"><xsl:value-of select="oai:repositoryName"/></td></tr>
    <tr><td class="key">Base URL</td>
    <td class="value"><xsl:value-of select="oai:baseURL"/></td></tr>
    <tr><td class="key">Protocol Version</td>
    <td class="value"><xsl:value-of select="oai:protocolVersion"/></td></tr>
    <tr><td class="key">Earliest Datestamp</td>
    <td class="value"><xsl:value-of select="oai:earliestDatestamp"/></td></tr>
    <tr><td class="key">Deleted Record Policy</td>
    <td class="value"><xsl:value-of select="oai:deletedRecord"/></td></tr>
    <tr><td class="key">Granularity</td>
    <td class="value"><xsl:value-of select="oai:granularity"/></td></tr>
    <xsl:apply-templates select="oai:adminEmail"/>
  </table>
  <!-- TODO: oai:description -->
</xsl:template>

<xsl:template match="oai:adminEmail">
  <tr><td class="key">Admin Email</td>
  <td class="value"><xsl:value-of select="."/></td></tr>
</xsl:template>

<!--
   Identify / OAI-Identifier
-->

<xsl:template match="id:oai-identifier" xmlns:id="http://www.openarchives.org/OAI/2.0/oai-identifier">
  <h2>OAI-Identifier</h2>
  <table class="table table-bordered">
    dent<tr><td class="key">Scheme</td>
    <td class="value"><xsl:value-of select="id:scheme"/></td></tr>
    <tr><td class="key">Repository Identifier</td>
    <td class="value"><xsl:value-of select="id:repositoryIdentifier"/></td></tr>
    <tr><td class="key">Delimiter</td>
    <td class="value"><xsl:value-of select="id:delimiter"/></td></tr>
    <tr>OAI-Identifier<td class="key">Sample OAI Identifier</td>
    <td class="value"><xsl:value-of select="id:sampleIdentifier"/></td></tr>
  </table>
</xsl:template>

<!-- GetRecord -->

<xsl:template match="oai:GetRecord">
  <xsl:apply-templates select="oai:record" />
</xsl:template>

<!-- ListRecords -->

<xsl:template match="oai:ListRecords">
  <xsl:apply-templates select="oai:record" />
</xsl:template>

<!-- ListIdentifiers -->

<xsl:template match="oai:ListIdentifiers">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>identifier</th>
        <th>datestamp</th>
        <th>GetRecord</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <xsl:apply-templates select="oai:header" mode="list"/>
    </tbody>
  </table>
</xsl:template>

<xsl:template match="oai:header" mode="list">
  <tr>
    <td>
      <code><xsl:value-of select="oai:identifier"/></code>
    </td>
    <td>
      <code><xsl:value-of select="oai:datestamp"/></code>
      <xsl:if test="@status='deleted'">
        This record has been deleted.
      </xsl:if>
    </td>
    <td>
      <a href="?verb=GetRecord&amp;metadataPrefix=oai_dc&amp;identifier={oai:identifier}">oai_dc</a>
    </td>
    <td>
      <a href="?verb=ListMetadataFormats&amp;identifier={oai:identifier}">formats</a>
    </td>
  </tr>
</xsl:template>

<!-- ListSets -->

<xsl:template match="oai:ListSets">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>setSpec</th>
        <th>setName</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <xsl:apply-templates select="oai:set"/>
    </tbody>
  </table>
</xsl:template>

<xsl:template match="oai:set">
  <tr>
    <td><code><xsl:value-of select="oai:setSpec"/></code></td>
    <td><xsl:value-of select="oai:setName"/></td>
    <td><xsl:apply-templates select="oai:setSpec"/></td>
  </tr>
</xsl:template>

<xsl:template match="oai:setSpec">
  <!-- TODO: use selected or default metadataPrefix -->
  <a href="?verb=ListIdentifiers&amp;metadataPrefix=oai_dc&amp;set={.}">Identifiers</a>
  /
  <a href="?verb=ListRecords&amp;metadataPrefix=oai_dc&amp;set={.}">Records</a>
</xsl:template>

<!-- ListMetadataFormats -->

<xsl:template match="oai:ListMetadataFormats">
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>metadataPrefix</th>
        <th>namespace &amp; schema</th>
        <th>list</th>
      </tr>
    </thead>
    <tbody>
      <xsl:apply-templates select="oai:metadataFormat" />
    </tbody>
  </table>
</xsl:template>

<xsl:template match="oai:metadataFormat">
  <xsl:variable name="prefix" select="oai:metadataPrefix"/>
  <tr>
    <td>
      <code><xsl:value-of select="$prefix"/></code>
    </td>
    <td>
      <xsl:value-of select="oai:metadataNamespace"/>
      <xsl:if test="count(oai:metadataNamespace|oai:schema) &gt; 1">
        <br/>
      </xsl:if>
      <a href="{oai:schema}"><xsl:value-of select="oai:schema"/></a>
    </td>
    <td>
      <a href="?verb=ListIdentifiers&amp;metadataPrefix={$prefix}">
        Identifiers
      </a>
      <br/>
      <a href="?verb=ListRecords&amp;metadataPrefix={$prefix}">
        Records
      </a>
    </td>
  </tr>
</xsl:template>

<xsl:template match="oai:metadataPrefix">
  <xsl:text> </xsl:text>
  <a href="?verb=GetRecord&amp;metadataPrefix={.}&amp;identifier={$identifier}"><xsl:value-of select='.' /></a>
</xsl:template>

<!-- record object -->

<xsl:template match="oai:record">
   <xsl:apply-templates select="oai:header" />
   <xsl:apply-templates select="oai:metadata" />
   <xsl:apply-templates select="oai:about" />
</xsl:template>

<xsl:template match="oai:header">
  <dl>
    <dt>identifier</dt>
    <dd><code><xsl:value-of select="oai:identifier"/></code></dd>
    <dt>datestamp</dt>
    <dd><code><xsl:value-of select="oai:datestamp"/></code></dd>
    <xsl:if test="oai:setSpec">
      <dt>setSpec</dt>
      <dd>
        <table class="table table-sm">
          <xsl:for-each select="oai:setSpec">
            <tr>
              <td><xsl:value-of select="."/></td>
              <td><xsl:apply-templates select="."/></td>
            </tr>
          </xsl:for-each>
        </table>
      </dd>
    </xsl:if>
  </dl>
  <div>
    <xsl:if test="@status='deleted'">
      This record has been deleted.
    </xsl:if>
  </div>
</xsl:template>


<xsl:template match="oai:metadata">
  <div class="metadata">
    <xsl:apply-templates select="*" />
  </div>
</xsl:template>

<!-- oai resumptionToken -->

<xsl:template match="oai:resumptionToken">
  <p>
    There are more results.
    resumptionToken:
    <a href="?verb={$verb}&amp;resumptionToken={.}">
     <xsl:value-of select="."/>
    </a>
  </p>
</xsl:template>

<xsl:template match="oai:metadata/*">
  <pre><code>
    <xsl:apply-templates select="." mode='xmlMarkup' />
  </code></pre>
</xsl:template>

<!-- XML Pretty Maker -->

<!-- TODO: remove this? -->

<xsl:template match="node()" mode='xmlMarkup'>
  <div class="xmlBlock">
    &lt;<span class="xmlTagName"><xsl:value-of select='name(.)' /></span><xsl:apply-templates select="@*" mode='xmlMarkup'/>&gt;<xsl:apply-templates select="node()" mode='xmlMarkup' />&lt;/<span class="xmlTagName"><xsl:value-of select='name(.)' /></span>&gt;
  </div>
</xsl:template>

<xsl:template match="text()" mode='xmlMarkup'><span class="xmlText"><xsl:value-of select='.' /></span></xsl:template>

<xsl:template match="@*" mode='xmlMarkup'>
  <xsl:text> </xsl:text><span class="xmlAttrName"><xsl:value-of select='name()' /></span>="<span class="xmlAttrValue"><xsl:value-of select='.' /></span>"
</xsl:template>

<!--xsl:template name="xmlstyle">
.xmlSource {
	font-size: 70%;
	border: solid #c0c0a0 1px;
	background-color: #ffffe0;
	padding: 2em 2em 2em 0em;
}
.xmlBlock {
	padding-left: 2em;
}
.xmlTagName {
	color: #800000;
	font-weight: bold;
}
.xmlAttrName {
	font-weight: bold;
}
.xmlAttrValue {
	color: #0000c0;
}
</xsl:template-->

<xsl:template match="*"/>

</xsl:stylesheet>


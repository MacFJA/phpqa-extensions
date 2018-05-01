<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0" xmlns:fn="http://www.w3.org/2005/xpath-functions">

    <xsl:output method="html"  encoding="UTF-8"/>
    <xsl:key name="file-category" match="//files/file" use="@extension" />
    <xsl:key name="file-path" match="//files/file" use="@path" />
    <xsl:param name="root-directory"/>
    
    <xsl:template match="/">
        <html>
            <head>
                <title>PhpCodeAnalyzer report</title>
                <link rel="stylesheet"><xsl:attribute name="href"><xsl:value-of select="$bootstrap.min.css" /></xsl:attribute></link>
                <style>
                    .file {
                        background: #f9f9f9
                    }
                    .fixed-navbar {
                        list-style-type: none;
                        position: fixed;
                        top: 0;
                        right: 1em;
                    }
                </style>
                <script>
                var onDocumentReady = [
                    function () {
                        $('[data-file]').each(function () {
                            var pathWithoutRoot = $(this).text().replace('<xsl:value-of select="$root-directory"></xsl:value-of>', '');
                            $(this).text(pathWithoutRoot);
                        });
                    }
                ];
                </script>
            </head>
            <body>

            <div class="container-fluid">
            
                <h1>PhpCodeAnalyzer report</h1>

                <nav>
                    <ul class="nav nav-pills" role="tablist">
                        <li role="presentation" class="active">
                            <a href="#overview" aria-controls="overview" role="tab" data-toggle="tab">Overview</a>
                        </li>
                        <li role="presentation">
                            <a href="#files" aria-controls="files" role="tab" data-toggle="tab">Details by files</a>
                        </li>
                        <li role="presentation">
                            <a href="#extensions" aria-controls="extensions" role="tab" data-toggle="tab">Details by extensions</a>
                        </li>
                    </ul>
                </nav>

                <div class="tab-content">
                    
                    <div role="tabpanel" class="tab-pane active" id="overview">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Extensions</th>
                                    <th>Files</th>
                                    <th>Occurrence</th>
                                </tr>
                            </thead>
                            <!-- http://stackoverflow.com/a/9589085/4587679 -->
                            <xsl:for-each select="//files/file[generate-id() = generate-id(key('file-category', ./@extension)[1])]">
                                <xsl:variable name="group" select="@extension"/>
                                <!--<xsl:key name="file-name" match="//file[@extension = $group]" use="@path" />-->
                                <tr>
                                    <td><strong data-file=""><xsl:value-of select="$group"/></strong></td>
                                    <td><xsl:value-of select="count(//file[@extension = $group and not(following::file[@extension = $group]/@path = @path)])"/></td>
                                    <td><xsl:value-of select="count(//file[@extension = $group])"/></td>
                                </tr>
                            </xsl:for-each>
                            <tfoot>
                                <tr>
                                    <th><span class="label label-info"><xsl:value-of select="count(//files/file[generate-id() = generate-id(key('file-category', @extension)[1])])"/></span></th>
                                    <th><span class="label label-warning"><xsl:value-of select="count(//file[not(following::file/@path = @path)])" /></span></th>
                                    <th>
                                        <span class="label label-warning"><xsl:value-of select="count(//files/file)" /></span>
                                    </th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="files">
                        <div class="fixed-navbar">
                            <div class="input-group" style="width: 20em">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                                <input data-search="errors" type="text" class="form-control" placeholder="undefined..." />
                            </div>
                        </div>
                        <script>
                        onDocumentReady.push(function () {
                            var groups = $('[data-filterable] tbody tr[data-permanent]');
                            var rows = $('[data-filterable] tbody tr:not([data-permanent])');

                            $("[data-search]").keyup(function () {
                                var term = $(this).val().toLowerCase();

                                rows.hide();
                                groups.show();
                                matchElements(rows).show();
                                matchEmptyGroups().hide();

                                function matchElements(elements) {
                                    return elements.filter(function () {
                                        var rowContent = $(this).text().toLowerCase();
                                        return rowContent.indexOf(term) !== -1
                                    });
                                }

                                function matchEmptyGroups() {
                                    return groups.filter(function () {
                                        var group = $(this).data('permanent');
                                        return rows
                                            .filter(function () {
                                                return $(this).data('group') == group <![CDATA[&&]]> $(this).is(':visible');
                                            })
                                            .length == 0;
                                    });
                                }
                            });
                        });
                        </script>

                        <table class="table" data-filterable="errors">
                            <thead>
                                <tr>
                                    <th>Message</th>
                                    <th>Line</th>
                                </tr>
                            </thead>
                            <xsl:for-each select="//files/file[generate-id() = generate-id(key('file-path', ./@path)[1])]">
                                <xsl:variable name="group" select="@path"/>
                                <tr>
                                    <xsl:attribute name="data-permanent">
                                        <xsl:value-of select="$group" />
                                    </xsl:attribute>
                                    <td colspan="3" class="file"><strong data-file=""><xsl:value-of select="$group" /></strong></td>
                                </tr>
                                <xsl:for-each select="//files/file[@path = $group]">
                                    <tr>
                                        <xsl:attribute name="data-group">
                                            <xsl:value-of select="./text()" />
                                        </xsl:attribute>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="@type = 'class'"><xsl:text>The class </xsl:text></xsl:when>
                                                <xsl:when test="@type = 'function'"><xsl:text>The function </xsl:text></xsl:when>
                                                <xsl:when test="@type = 'constant'"><xsl:text>The constant </xsl:text></xsl:when>
                                            </xsl:choose>
                                            <strong><code><xsl:value-of select="./text()" /></code></strong>
                                            <xsl:text>of the extension </xsl:text>
                                            <strong><xsl:value-of select="@extension" /></strong>
                                            <xsl:text> is used.</xsl:text>
                                        </td>
                                        <td><xsl:value-of select="@line" /></td>
                                    </tr>
                                </xsl:for-each>
                            </xsl:for-each>
                        </table>
                    </div>

                    <div role="tabpanel" class="tab-pane" id="extensions">
                        <div class="fixed-navbar">
                            <div class="input-group" style="width: 20em">
                                <span class="input-group-addon"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></span>
                                <input data-search="errors" type="text" class="form-control" placeholder="undefined..." />
                            </div>
                        </div>

                        <table class="table" data-filterable="errors">
                            <thead>
                                <tr>
                                    <th>Message</th>
                                    <th>Line</th>
                                </tr>
                            </thead>
                            <xsl:for-each select="//files/file[generate-id() = generate-id(key('file-category', ./@extension)[1])]">
                                <xsl:variable name="group" select="@extension"/>
                                <tr>
                                    <xsl:attribute name="data-permanent">
                                        <xsl:value-of select="$group" />
                                    </xsl:attribute>
                                    <td colspan="3" class="file"><strong data-file=""><xsl:value-of select="$group" /></strong></td>
                                </tr>
                                <xsl:for-each select="//files/file[@extension = $group]">
                                    <tr>
                                        <xsl:attribute name="data-group">
                                            <xsl:value-of select="./text()" />
                                        </xsl:attribute>
                                        <td>
                                            <xsl:choose>
                                                <xsl:when test="@type = 'class'"><xsl:text>The class </xsl:text></xsl:when>
                                                <xsl:when test="@type = 'function'"><xsl:text>The function </xsl:text></xsl:when>
                                                <xsl:when test="@type = 'constant'"><xsl:text>The constant </xsl:text></xsl:when>
                                            </xsl:choose>
                                            <strong><code><xsl:value-of select="./text()" /></code></strong>
                                            <xsl:text> is used in the file </xsl:text>
                                            <code><xsl:value-of select="@path" /></code>
                                            <xsl:text>.</xsl:text>
                                        </td>
                                        <td><xsl:value-of select="@line" /></td>
                                    </tr>
                                </xsl:for-each>
                            </xsl:for-each>
                        </table>
                    </div>
                </div>
            </div>


            <script><xsl:attribute name="src"><xsl:value-of select="$jquery.min.js" /></xsl:attribute></script>
            <script><xsl:attribute name="src"><xsl:value-of select="$bootstrap.min.js" /></xsl:attribute></script>
            <script>
                $(document).ready(onDocumentReady);
            </script>
            </body>
        </html>
    </xsl:template>
</xsl:stylesheet>
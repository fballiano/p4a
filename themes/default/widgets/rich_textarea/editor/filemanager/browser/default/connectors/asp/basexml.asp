<!--
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2004 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 * 		http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 * 		http://www.fckeditor.net/
 * 
 * File Name: basexml.asp
 * 	This file include the functions that create the base XML output.
 * 
 * Version:  2.0 RC1
 * Modified: 2004-11-29 02:20:19
 * 
 * File Authors:
 * 		Frederico Caldeira Knabben (fredck@fckeditor.net)
-->
<%
Sub CreateXmlHeader( command, resourceType, currentFolder )
	' Create the XML document header.
	Response.Write "<?xml version=""1.0"" encoding=""utf-8"" ?>"

	' Create the main "Connector" node.
	Response.Write "<Connector command=""" & command & """ resourceType=""" & resourceType & """>"
	
	' Add the current folder node.
	Response.Write "<CurrentFolder path=""" & currentFolder & """ url=""" & GetUrlFromPath( resourceType, currentFolder) & """ />"
End Sub

Sub CreateXmlFooter()
	Response.Write "</Connector>"
End Sub
%>
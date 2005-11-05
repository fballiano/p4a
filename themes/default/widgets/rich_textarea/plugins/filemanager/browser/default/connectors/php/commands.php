<?php 
/*
 * FCKeditor - The text editor for internet
 * Copyright (C) 2003-2005 Frederico Caldeira Knabben
 * 
 * Licensed under the terms of the GNU Lesser General Public License:
 *      http://www.opensource.org/licenses/lgpl-license.php
 * 
 * For further information visit:
 *      http://www.fckeditor.net/
 * 
 * File Name: commands.php
 *  This is the File Manager Connector for PHP.
 * 
 * File Authors:
 *      Frederico Caldeira Knabben (fredck@fckeditor.net)
 */

function GetFolders( $resourceType, $currentFolder )
{
    // Map the virtual path to the local server path.
    $sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

    // Open the "Folders" node.
    echo "<Folders>" ;

    $oCurrentFolder = opendir( $sServerDir ) ;

    while ( $sFile = readdir( $oCurrentFolder ) )
    {
        if ( $sFile != '.' && $sFile != '..' && is_dir( $sServerDir . $sFile ) )
            echo '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
    }

    closedir( $oCurrentFolder ) ;

    // Close the "Folders" node.
    echo "</Folders>" ;
}

function GetFoldersAndFiles( $resourceType, $currentFolder )
{
    // Map the virtual path to the local server path.
    $sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

    // Initialize the output buffers for "Folders" and "Files".
    $sFolders   = '<Folders>' ;
    $sFiles     = '<Files>' ;

    $oCurrentFolder = opendir( $sServerDir ) ;

    while ( $sFile = readdir( $oCurrentFolder ) )
    {
        if ( $sFile != '.' && $sFile != '..' )
        {
            if ( is_dir( $sServerDir . $sFile ) )
                $sFolders .= '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
            else
            {
                $iFileSize = filesize( $sServerDir . $sFile ) ;
                if ( $iFileSize > 0 )
                {
                    $iFileSize = round( $iFileSize / 1024 ) ;
                    if ( $iFileSize < 1 ) $iFileSize = 1 ;
                }

                $sFiles .= '<File name="' . ConvertToXmlAttribute( $sFile ) . '" size="' . $iFileSize . '" />' ;
            }
        }
    }

    echo $sFolders ;
    // Close the "Folders" node.
    echo '</Folders>' ;

    echo $sFiles ;
    // Close the "Files" node.
    echo '</Files>' ;
}

function CreateFolder( $resourceType, $currentFolder )
{
    $sErrorNumber   = '0' ;
    $sErrorMsg      = '' ;

    if ( isset( $_GET['NewFolderName'] ) )
    {
        $sNewFolderName = $_GET['NewFolderName'] ;

        // Map the virtual path to the local server path of the current folder.
        $sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

        if ( is_writable( $sServerDir ) )
        {
            $sServerDir .= $sNewFolderName ;

            $sErrorMsg = CreateServerFolder( $sServerDir ) ;

            switch ( $sErrorMsg )
            {
                case '' :
                    $sErrorNumber = '0' ;
                    break ;
                case 'Invalid argument' :
                case 'No such file or directory' :
                    $sErrorNumber = '102' ;     // Path too long.
                    break ;
                default :
                    $sErrorNumber = '110' ;
                    break ;
            }
        }
        else
            $sErrorNumber = '103' ;
    }
    else
        $sErrorNumber = '102' ;

    // Create the "Error" node.
    echo '<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( $sErrorMsg ) . '" />' ;
}

function FileUpload( $resourceType, $currentFolder )
{
    $sErrorNumber = '0' ;
    $sFileName = '' ;

    if ( isset( $_FILES['NewFile'] ) && !is_null( $_FILES['NewFile']['tmp_name'] ) )
    {
        $oFile = $_FILES['NewFile'] ;

        // Map the virtual path to the local server path.
        $sServerDir = ServerMapFolder( $resourceType, $currentFolder ) ;

        // Get the uploaded file name.
        $sFileName = $oFile['name'] ;
        $sOriginalFileName = $sFileName ;
        $sExtension = substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
        $sExtension = strtolower( $sExtension ) ;

        global $Config ;

        $arAllowed  = $Config['AllowedExtensions'][$resourceType] ;
        $arDenied   = $Config['DeniedExtensions'][$resourceType] ;

        if ( ( count($arAllowed) == 0 || in_array( $sExtension, $arAllowed ) ) && ( count($arDenied) == 0 || !in_array( $sExtension, $arDenied ) ) )
        {
            $iCounter = 0 ;

            while ( true )
            {
                $sFilePath = $sServerDir . $sFileName ;

                if ( is_file( $sFilePath ) )
                {
                    $iCounter++ ;
                    $sFileName = RemoveExtension( $sOriginalFileName ) . '(' . $iCounter . ').' . $sExtension ;
                    $sErrorNumber = '201' ;
                }
                else
                {
                    move_uploaded_file( $oFile['tmp_name'], $sFilePath ) ;

                    if ( is_file( $sFilePath ) )
                    {
                        $oldumask = umask(0) ;
                        chmod( $sFilePath, 0777 ) ;
                        umask( $oldumask ) ;
                    }

                    break ;
                }
            }
            
            //Image Resizing
            if (strpos(mime_content_type($sFilePath), "image") !== false) { //The file is an image?
                if (function_exists("gd_info") ) { //libGD is installed ?
                
                    if (array_key_exists("EnableImageResize",$Config) 
                        and $Config['EnableImageResize'] == true) { //Image Resize is enabled?
                        
                        $createfunction = "";
                        if (strpos(mime_content_type($sFilePath), "png")) {
                            $createfunction = "imagecreatefrompng";
                            $savefunction = "imagepng";
                        } elseif (strpos(mime_content_type($sFileUrl), "jpeg")) {
                            $createfunction = "imagecreatefromjpeg";
                            $savefunction = "imagejpeg";
                        }
                        
                        list($width, $height) = getimagesize($sFilePath);
                        
                        foreach ($Config['ImageSize'] as $size=>$value) {
                            list($new_width,$new_height) = explode("x",$value);
                            if (($width > $new_width) or($height > $new_height)) {
                                if (($width/$height)>($new_width/$new_height)) {
                                    $new_height = $new_width / ($width /$height);
                                } else {
                                    $new_width = $new_height * ($width /$height);
                                }                
                            } else {
                                $new_width = $width;
                                $new_height = $height;
                            }
                            
                            if ($createfunction) {
                                $im_new = @imagecreatetruecolor($new_width, $new_height);
                                $im = $createfunction($sFilePath);
                                imagecopyresampled($im_new, $im, 0, 0, 0, 0, $new_width, $new_height, $width, $height);
                                
                                $sNewFileName = RemoveExtension($sFileName) . "_{$size}.{$sExtension}";
                                $sNewFilePath = $sServerDir . $sNewFileName ;
                                $savefunction($im_new, $sNewFilePath);
                                
                                if (is_file($sNewFilePath)) {
                                    $oldumask = umask(0);
                                    chmod($sNewFilePath, 0777);
                                    umask($oldumask) ;
                                }
                            }                
                        } 
                                
                    }
                }
            }
            //Image Resizing
        }
        else
            $sErrorNumber = '202' ;
    }
    else
        $sErrorNumber = '202' ;

    echo '<script type="text/javascript">' ;
    echo 'window.parent.frames["frmUpload"].OnUploadCompleted(' . $sErrorNumber . ',"' . str_replace( '"', '\\"', $sFileName ) . '") ;' ;
    echo '</script>' ;

    exit ;
}

function debug($text) {
    $f = fopen("/tmp/debug","a");
    fwrite($f, "$text\n");
    fclose($f);
}
?>
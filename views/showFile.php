<?php

// Expect: $filepath, $fieldname

$uploads    = wp_upload_dir();
$fullPath   = $uploads['basedir'] . $filepath;
$fullUrl    = $uploads['baseurl'] . $filepath;

$fileData   = pathinfo( $fullPath );
$fileName   = $fileData['basename'];

if( !file_exists( $fullPath ) )
    return;

$html = null;
if( is_array( getimagesize( "$fullUrl" ) ) )
    $html.= "<img src='$fullUrl' alt='$fileName' title='$fileName' />";
else
    $html.= "<a href='$fullUrl'>$fileName</a>";
    
$html .= "<p><a href='#' onclick='umRemoveFile(this)' name='$fieldname'>Remove</a><p>";
//$html .= "<br /> <div class='button' onclick='umRemoveFile(this)' name='$fieldname'>Remove</div>";


$html.= "<input type='hidden' name='$fieldname' value='$filepath' />";
            
?>
<?php
global $userMeta;
// Expected: file_pointer, $percent, $is_loop, $import_count

$html = "

<div id=\"progressbar\" style=\"height:20px\"></div>

<script>
jQuery(document).ready(function(){
	jQuery( \"#progressbar\" ).progressbar({
		value: $percent
	});	   
})
</script>

";

if( $percent == 100 ){
    $html .= __( 'Import completed.', $userMeta->name );
    $html .= '<script>jQuery(".ui-dialog-buttonset .ui-button-text").html("'. __( 'Colse', $userMeta->name ) .'")</script>';
}else{
    $html .= "<img src=\"" . $userMeta->assetsUrl . "/images/pf_loading_fb.gif\" />"; 
}

$html .= "<p>" . sprintf( __( 'Read: %1$s, Created: %2$s, Updated: %3$s, Skipped: %4$s', $userMeta->name ), $import_count['rows'], $import_count['create'], $import_count['update'], $import_count['skip'] ) . "</p>";

$do_loop = $is_loop ? "do_loop=\"do_loop\"" : null;
$html = "<div id=\"import_response\" $do_loop file_pointer=\"$file_pointer\" >$html</div>";
?>
<?php
//global $savant;
if(isset($_GET['cid'])){
 /* start output buffering: */
	ob_start();
	$hrefMod ='/mods/pdf_converter/';
	$sql = "SELECT content_id,title FROM %scontent WHERE content_id=%d";
	$content_row = queryDB($sql, array(TABLE_PREFIX, $_GET['cid']),true);
	echo("
 <a target='_blank' href='".$hrefMod."pdf_converter.php?cid=$content_row[content_id]'><img src='".$hrefMod."Fast_text.png' alt='"._AT('pdf_converter')." "._AT('plain_text').PHP_EOL.$content_row['title']."' title='"._AT('pdf_converter')." "._AT('plain_text').PHP_EOL.$content_row['title']."' /></a>
 <a target='_blank' href='".$hrefMod."pdf_converter.php?cid=$content_row[content_id]&amp;html=1'><img src='".$hrefMod."html_file.png' alt='"._AT('pdf_converter')." HTML2PDF".PHP_EOL.$content_row['title']."' title='"._AT('pdf_converter')." HTML2PDF".PHP_EOL.$content_row['title']."' /></a>
 ");
	//echo ($content_row['title']);
 $savant->assign('dropdown_contents', ob_get_contents());
 ob_end_clean();

 $savant->assign('title', _AT('pdf_converter')); // the box title
 $savant->display('include/box.tmpl.php');
}

?>
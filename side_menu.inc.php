<?php
/* start output buffering: */
ob_start();

//global $savant;
if(isset($_GET['cid'])){
	$sql 	= "SELECT * FROM ".TABLE_PREFIX."content WHERE content_id=$_GET[cid]";
	$result = queryDBresult($sql);
	$content_row = $result->fetch_assoc();
	echo("<a target='_blank' href='../mods/pdf_converter/pdf_converter.php?cid=$content_row[content_id]'>$content_row[title]</a>");
	//echo ($content_row['title']);
}

$savant->assign('dropdown_contents', ob_get_contents());
ob_end_clean();

$savant->assign('title', _AT('pdf_converter')); // the box title
$savant->display('include/box.tmpl.php');
?>
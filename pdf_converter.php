<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
if(isset($_GET['cid'])){
	$sql = "SELECT title,text FROM %scontent WHERE content_id=%d";
	$content_row = queryDB($sql, array(TABLE_PREFIX, $_GET['cid']),true);
	if($content_row!=0){
		$titulo = '<h1 style="text-align:center;">'.$content_row['title'].'</h1>';
		$contenido = $content_row['text'];

  if (isset($_GET['html'])){
    $contenido = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $contenido);
    require(__DIR__ . '/html2pdf/vendor/autoload.php');
    $html2pdf = new HTML2PDF('P','A4','fr');
    $html2pdf->setTestIsImage(false);
    $html2pdf->WriteHTML($titulo.$contenido);
    $html2pdf->Output('exemple.pdf');
  }else{#textOnly
    $titulo   	= strip_tags(pdfCleaner( $titulo ));
    $contenido 	= trim(strip_tags(pdfCleaner( $contenido )));
    require('fpdf.php');
    include( 'class.ezpdf.php' );
    $pdf=new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial','',10);
    $pdf->MultiCell(0,3,utf8_decode($titulo.PHP_EOL.PHP_EOL.$contenido));
    $pdf->Output();
  }
	}
	else{
		echo(_AT('error'));
	}
}

function decodeHTML( $string ) {
	$string = strtr( $string, array_flip(get_html_translation_table( HTML_ENTITIES ) ) );
	$string = preg_replace( "/&#([0-9]+);/me", "chr('\\1')", $string );
	return $string;
}


function pdfCleaner( $text ) {
	// Ugly but needed to get rid of all the stuff the PDF class cant handle

	$text = str_replace( '<p>', 			"\n\n", 	$text );
	$text = str_replace( '<P>', 			"\n\n", 	$text );
	$text = str_replace( '<br />', 			"\n", 		$text );
	$text = str_replace( '<br>', 			"\n", 		$text );
	$text = str_replace( '<BR />', 			"\n", 		$text );
	$text = str_replace( '<BR>', 			"\n", 		$text );
	$text = str_replace( '<li>', 			"\n - ", 	$text );
	$text = str_replace( '<LI>', 			"\n - ", 	$text );
	$text = str_replace( '<table>','',$text );
	$text = str_replace( '<TABLE>','',$text );
	$text = str_replace( '</table>','',$text );
	$text = str_replace( '</TABLE>','',$text );
	$text = str_replace( '<tr>','',$text );
	$text = str_replace( '</tr>','',$text );
	$text = str_replace( '<TR>','',$text );
	$text = str_replace( '</TR>','',$text );
	$text = str_replace( '&nbsp;','',$text );

	$text = strip_tags( $text );
	$text = decodeHTML( $text );

	return $text;
}
?>
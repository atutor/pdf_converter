<?php
define('AT_INCLUDE_PATH', '../../include/');
require (AT_INCLUDE_PATH.'vitals.inc.php');
if(isset($_GET['cid'])){
	$sql = "SELECT title,text,content_type FROM %scontent WHERE content_id=%d";
	$content_row = queryDB($sql, array(TABLE_PREFIX, $_GET['cid']),true);
	if($content_row!=0){
		$titulo = '<h1 style="text-align:center;">'.$content_row['title'].'</h1>';

		// just for AContent content (inspired from content.php)
		if($content_row['content_type']==2){//if(strstr($fp, 'AContentXX')){
			$fp	= @file_get_contents($content_row['text']);
			// a new dom object
			$dom		= new DomDocument();
			libxml_use_internal_errors(TRUE);
			// load the html into the object
			$dom->loadHTML($fp);
			libxml_use_internal_errors(FALSE);
			$dom->formatOutput = TRUE;

			//discard white space
			$dom->preserveWhiteSpace = false;

			$node = $dom->getElementById('content-text');#4 OLD : id content-text are commented in AContent (1.4) course
			if(empty($node));
				$node = $dom->getElementById('content');#new id
			if(empty($node));
				$node = $dom->getElementById('middle-column');#Au cas ou
   
			$contenido	= $dom->saveXML($node, LIBXML_NOEMPTYTAG);
			$contenido	= html_entity_decode(	$contenido );

		}else{
			$contenido = $content_row['text'];
		}
		$contenido = preg_replace('#<script(.*?)>(.*?)</script>#is', '', $contenido);

  if (isset($_GET['html'])){
    require(__DIR__ . '/html2pdf/vendor/autoload.php');
    $html2pdf = new HTML2PDF('P','A4','fr');
    $html2pdf->setTestIsImage(false);
    $html2pdf->WriteHTML($titulo.$contenido);
    $html2pdf->Output('exemple.pdf');
  }else{#textOnly
    $titulo = pdfCleaner( $titulo );
    $contenido = trim(pdfCleaner( $contenido ));
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

function pdfCleaner( $text ) {
	// Ugly but needed to get rid of all the stuff the PDF class cant handle

	$text = html_entity_decode( $text );
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

	return $text;
}
?>
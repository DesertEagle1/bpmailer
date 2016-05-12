<?php
	$x = new XMLWriter();
	$x->openMemory();
	$x->setIndent(true);
	$x->startDocument('1.0','UTF-8');
	$x->startElement('AddressBook');

	foreach ($data as $key => $value) {
		 $x->startElement('e-mail');
         $x->text($value);
         $x->endElement();
	}

	$x->endElement();
	$x->endDocument();
	$xml = $x->outputMemory();

	$f = fopen('php://memory', 'w');

	fwrite($f, $xml);

	fseek($f, 0);
	header('Content-Type: text/xml');
	header('Content-Disposition: attachement; filename=export.xml');
	fpassthru($f);
?>
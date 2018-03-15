<?php

$empfaenger = 'runge.jan@gmx.de';
$betreff = 'Der Betreff';
$nachricht = 'Hallo';
$header = 'From: titurion@gmx.de' . "\r\n" .
    'Reply-To: titurion@gmx.de' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

mail($empfaenger, $betreff, $nachricht, $header);

?>
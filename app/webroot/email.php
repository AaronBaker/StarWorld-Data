<h1>HOLA EMAIL</h1>

<?php
$to      = 'baker.aaron@gmail.com';
$subject = 'the subject';
$message = 'hello';
$headers = 'From: webmaster@starworlddata.com' . "\r\n" .
    'X-Mailer: PHP/' . phpversion();



if(mail($to, $subject, $message, $headers))
	echo "MAILED!";
?>
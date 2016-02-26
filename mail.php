<?php
try {

mail('samuel@agilizelabs.com', 'My Subject', 'test mail');
$to = "itmyprofession@gmail.com";
$subject = "Test email";
$message = "This is a test email.";
$from = "samuel@agilizelabs.com";
$headers = "From:" . $from;
if (mail($to, $subject, $message, $headers)) {
	echo("Your message has been sent successfully");
	} else {
	echo("Sorry, your message could not be sent");
}
}
catch(Exception $e) {
	echo '<pre>';
	print_r($e);
}

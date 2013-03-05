<?
set_time_limit(600000);
require_once($_SERVER['DOCUMENT_ROOT'] . "/objects/common.php");
header("Content-Type: text/plain");



$mail = new Notification;
$mail->to = 1;
$mail->subject = "Test Message from Server";
$mail->body = "Just trying to send a test message from the server.";
$mail->link = "http://hofl.com/majors";
$mail->Create();

print "Mail Sent";


?>
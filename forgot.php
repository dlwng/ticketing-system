<html>
<head>
	<title>Assignment 3</title>
	<link rel="stylesheet" type="text/css" href="forgot.css">
</head>
<body>
<div id="content">
<?php

	$db = new mysqli('localhost', 'root', '', 'plwa');

	if ($db->connect_error):
        die ("Could not connect to db: " . $db->connect_error);
    endif;

    $username = strip_tags($_POST["username"]);
    $email = strip_tags($_POST["email"]);

    $sql = "SELECT username FROM Administrators WHERE email = '$email'";
	$result = $db->query($sql);
	$compare = $result->fetch_assoc();

	//$val = rand();
	$code = urlencode(rand());


	//echo $compare["username"];

	if($compare["username"] == $username) {

		$mailpath = '/Applications/XAMPP/xamppfiles/PHPMailer';
		// Add the new path items to the previous PHP path
		$path = get_include_path();
		set_include_path($path . PATH_SEPARATOR . $mailpath);
		require 'PHPMailerAutoload.php';

		//CONFIRMATION EMAIL TO USER
		$mail = new PHPMailer();

		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->SMTPAuth = true; // enable SMTP authentication
		$mail->SMTPSecure = "tls"; // sets tls authentication
		$mail->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server; or your email service
		$mail->Port = 587; // set the SMTP port for GMAIL server; or your email server port
		//$mail->Username = "email"; // email username
		//$mail->Password = "password"; // email password
		$mail->Username = "cs4501.fall15@gmail.com"; // email username
		$mail->Password = "UVACSROCKS"; // email password

		//$sender = strip_tags($_POST["sender"]);
		$receiver = strip_tags($_POST["email"]);
		//$subj = strip_tags($_POST["subject"]);
		//$msg = strip_tags($_POST["problem"]);

		// Put information into the message
		$mail->addAddress($email);
		$mail->SetFrom("dlw4dc@virginia.edu");
		$mail->Subject = "Reset Password";
		$mail->Body = "'Copy this link into your browser to reset your password: localhost/assignment2/verify.php?code=$code";

		// echo 'Everything ok so far' . var_dump($mail);
		if(!$mail->send()) {
		echo 'Reset password message could not be sent.';
		echo 'Mailer Error: ' . $mail->ErrorInfo;
		} 
		else { echo 'Reset password link has been sent'; }
		
	}

	else {
		echo "Your username and email do not match.<br/>";
		echo "<a href='forgot.html'>Try again.</a>";
	}

	$sql1 = "UPDATE Administrators SET password = '$code' WHERE username = '$username'";

	if ($db->query($sql1) === TRUE) {
    	echo "";
	} 

	else {
    	echo "Error: " . $sql1 . "<br>" . $db->error;
	}
	
?>
</div>

</body>
</html>
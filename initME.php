<html>
<head>
	<title>Assignment 3</title>
</head>
<style>
table, th, td {
     border: 1px solid black;
}
</style>
<body>
<?php
	
	session_start();

	$db = new mysqli('localhost', 'root', '', 'plwa');

	if ($db->connect_error):
        die ("Could not connect to db: " . $db->connect_error);
    endif;

	$db->query("drop table AllUsers");

	$result = $db->query("CREATE TABLE AllUsers (
		id int(30) AUTO_INCREMENT PRIMARY KEY,
		username varchar(50) NOT NULL,
		password varchar(50) NOT NULL,
		email varchar(50) NOT NULL,
		role varchar(50) NOT NULL)
	")


	or die ("Invalid: " . $db->error);
	echo "Database created <br/>";

	//insert the users

	$myfile = fopen("users.txt", "r");

	while(!feof($myfile)) {
		$line = fgets($myfile);
		$login = explode("#", $line);
		$u = trim($login[0]);
		$p = trim($login[1]);
		$e = trim($login[2]);
		$r = trim($login[3]); //role
		$hashp = md5($p);


		$sql = "INSERT INTO AllUsers (username, password, email, role) 
				VALUES ('$u', '$hashp', '$e', '$r')";

   	 	if ($db->query($sql) === TRUE) {
    		echo "New record created successfully <br/>";
		} 

		else {
    		echo "Error: " . $sql . "<br>" . $db->error;
		}
	}	

	//create tickets table

	$db->query("drop table Tickets");

	$result = $db->query("CREATE TABLE Tickets (
		Tickets_id int(30) AUTO_INCREMENT PRIMARY KEY, 
		reg_date timestamp NOT NULL, 
		sname varchar(30) NOT NULL,
		semail varchar(30) NOT NULL,
		subject varchar(30) NOT NULL,
		tech varchar(30),
		status varchar(30) NOT NULL)
	")

	or die ("Invalid: " . $db->error);
	echo "Database created <br/>";	

	fclose($myfile);

?>
	<a href="login.html">Continue to login</a>

</body>
</html>
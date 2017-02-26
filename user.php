<html>
<head>
	<title>Assignment 3</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>
<?php
	session_start();
	$username = $_SESSION["username"];

	echo "<h1>Welcome User!</h1>";
			//if its a user not admin
?>
			<div id = "usercontent">Please choose an option</div>
			<div id = "selected"></div>
			<br/><br/>
			<div id = "userbuttons">
			<!--SUBMIT NEW TICKET-->
			<input type = "submit" value = "Submit a New Ticket" onclick = "submit('<?php echo $username; ?>')">

			<!--SEE ALL OF MY TICKETS-->
			<input type = "submit" value = "See All of My Tickets" onclick = "processData(12, 0, '<?php echo $username;?>')">

			<!--CHANGE PASSWORD-->
			<input type = "submit" value = "Change Password" onclick = "changepass()">			
			</div>

			<!--LOGOUT-->
			<form action = 'login.html'>
				<input type = 'submit' value = 'Logout'>
			</form>		
			</div>
</body>

<script>
	function processData(type, id, username) {
        var httpRequest;
        var data;

        if (window.XMLHttpRequest) { // Mozilla, Safari, ...
            //alert('XMLHttpRequest');
            httpRequest = new XMLHttpRequest();
            if (httpRequest.overrideMimeType) {
                httpRequest.overrideMimeType('text/xml');
            }
        }

        else if (window.ActiveXObject) { // Older versions of IE
            //alert('IE -- XMLHTTP');
            try {
                httpRequest = new ActiveXObject("Msxml2.XMLHTTP");
                }
            catch (e) {
                try {
                    httpRequest = new ActiveXObject("Microsoft.XMLHTTP");
                }
                catch (e) {}
            }
        }

        if (!httpRequest) {
            alert('Giving up :( Cannot create an XMLHTTP instance');
            return false;
        }


		httpRequest.open("POST", "process.php", true);
		httpRequest.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        if (type == 1) {

			data = 'type=' + type + '&' + 'id=' + id + '&' + 'username=' + 'username';
			httpRequest.onreadystatechange = function() { selected(httpRequest); };
            httpRequest.send(data);
			
		}


		if (type == 12) { //user views their tickets
			data = 'type=' + type + '&' + 'id=' + id + '&' + 'username=' + username;
			httpRequest.onreadystatechange = function() { usertickets(httpRequest); };
            httpRequest.send(data);
		}

		if (type == 13) { //change password
			data = 'type=' + type + '&' + 'id=' + id + '&' + 'username=' + username;
			httpRequest.send(data);
			document.getElementById("content").innerHTML = "Ticket status has been changed. <br>";
		}

	}

	function selected(request) {
		document.getElementById("selected").innerHTML = request.responseText;
		document.getElementById("usercontent").style.display = "none";
	}

	function submit(username) {

		document.getElementById("usercontent").innerHTML = "<input type = 'text' name = 'subject' placeholder = 'subject' size = '30' maxlength = '30' required><br /><br /><textarea rows='4' cols='50' placeholder = 'problem' name = 'problem' required></textarea><br /><input type = 'submit' value = 'submit' onclick = 'processData(12, \'<?php echo $username;?>\')'>"

	}

	function usertickets(request) {
		document.getElementById("usercontent").innerHTML = request.responseText;
	}

	function changepass() {
		document.getElementById("usercontent").innerHTML = "<input type = 'text' name = 'username' placeholder = 'username' size = '30' maxlength = '30' required><br /><br /><input type = 'text' name = 'password' placeholder = 'password' size = '30' maxlength = '30' required></textarea><br /><br /><input type = 'text' name = 'confirm password' placeholder = 'confirm' size = '30' maxlength = '30' required><br /><input type = \"submit\" value = \"submit\" onclick = \"processData(13, '<?php echo $username;?>')\">"		
	}
</script>
</html>
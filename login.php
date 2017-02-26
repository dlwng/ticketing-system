<html>
<head>
	<title>Assignment 3</title>
	<link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>
<body>
<?php

	/*if($_SERVER["HTTPS"] != "on") {
		header("Location: https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]);
	}*/
	session_start();

	$db = new mysqli('localhost', 'root', '', 'plwa');

	if ($db->connect_error):
        die ("Could not connect to db: " . $db->connect_error);
    endif;

	$username = $_POST["username"];
	$password = $_POST["password"];
	$match = False;

	$hashp = md5($password);


	/*//create parent array
	$all = "SELECT * FROM Tickets";
    $allresult = $db->query($all);
    $parent = array();
    if ($allresult->num_rows) {
       	while($row = $allresult->fetch_assoc()) {
    	    array_push($parent, $row);
        }
    }*/

	$sql1 = "SELECT username FROM AllUsers WHERE password = '$hashp'";
	$result1 = $db->query($sql1);
	$compare = $result1->fetch_assoc();

	$sql3 = "SELECT role FROM AllUsers WHERE password = '$hashp'";
	$result3 = $db->query($sql3);
	$compare3 = $result3->fetch_assoc();

	
	if ($compare["username"] == $username) {
		if ($compare3["role"] == "admin" ) {
			echo "<h1>Welcome Admin!</h1>";

			//DISPLAY OPEN TICKETS
			$sql = "SELECT * FROM Tickets WHERE status = 'open'";
			$sql4 = "SELECT * FROM Tickets";
			
			$result = $db->query($sql);
			$result4 = $db->query($sql4);
			//$rows = $result->num_rows;

			$parent = array();
			if ($result4->num_rows) {
				while($row4 = $result4->fetch_assoc()) {
					array_push($parent, $row4);
				}
			}

			if ($result->num_rows) {
				echo "<table id= 'tablespace'>";
				echo "<table id='table'><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
				while($row = $result->fetch_assoc()) {
					$id = $row['Tickets_id'];
					$received = $row["reg_date"];
					$name = $row["sname"];
					$email = $row["semail"]; 
					$subject = $row["subject"];
					$tech = $row["tech"];
					$status = $row["status"];
					echo "<td id='id'>$id</td>";
					echo "<td>$received</td>";
					echo "<td>$name</td>";
					echo "<td>$email</td>";
					echo "<td>$subject</td>";
					echo "<td>$tech</td>";
					echo "<td>$status</td>";
					echo "<td><input type = \"radio\" name = \"select\" 
					       value = \"$id\" onclick = \"processData(1, $id, '$username')\"></td><tr/>";
				}
				echo "<tr><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(0)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(1)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(2)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(3)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(4)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(5)'></td><td></td><td></td></tr>";
				echo "</table>";
				echo "</table>";
				echo "<div id='content'></div>";
			}
			

			else {
			    echo "No open tickets to display";
			}

?>

			<br/>
			<br/>
			<div id="buttons"></div>

			<div id = "adminbutton">
				<input type ='submit' value = 'View All Tickets' onclick = 'processData(10)'>
				<input type = 'submit' value = 'View My Tickets' onclick = "processData(9, 0, '<?php echo $username;?>')">
				<input type = 'submit' value = 'View Unassigned Tickets' onclick = 'processData(11)'>
				<form action = 'login.html'>
					<input type = 'submit' value = 'Logout'>
				</form>
			</div>

<?php

			//$array = json_encode($parent);
			

		}

		else {
			Header("Location: user.php");
			


		}
	}

	else {
		echo "This is not a valid login. <br/>";
		echo "<a href='login.html'>Try again.</a>";
	}

	
	$db->close();
?>
	


</body>
<script type = "text/javascript">

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
        	/*console.log(id);
        	var table = document.getElementById("table"); //the whole table
			var select = document.getElementsByName("select"); //table by name
			var headers = table.rows[0]; //first row of table aka the headers
			var index; //var for the row we selected
			//console.log(table);
			for (var i = 0; i < select.length; i++) {
			    if (select[i].checked) { 
			    	index = i;     
		    	}
			}
			
			index += 1;
			var selected = table.rows[index];
			//console.log(index);
			//console.log(selected);
			table.style.display = "none";
			document.getElementById("adminbutton").style.display = "none";
			//document.getElementById("content").style.display = "none";

			for (var j = 0; j < selected.cells.length -1; j++) {
				// document.write("here");
				document.getElementById("content").innerHTML += "<b>" + headers.cells[j].innerHTML + ": </b>" + selected.cells[j].innerHTML + "<br>";
			}

			var buttons = document.createElement("div");
			buttons.innerHTML = "<div id='buttons'><input type = 'submit' value = 'Close/Reopen Ticket' onclick = 'processData(2, \'<?php echo $id;?>\')'><input type = 'submit' value = 'Assign to me' onclick = 'processData(3, \'<?php echo $id;?>\', \'<?php echo $username;?>\')'><input type = 'submit' value = 'Remove self' onclick = 'processData(4, \'<?php echo $id;?>\', \'<?php echo $username;?>\')'><input type = 'submit' value = 'Email submitter' onclick = 'processData(5, \'<?php echo $id;?>\')'><input type = 'submit' value = 'Delete Ticket' onclick = 'processData(5, \'<?php echo $id;?>\'')'><input type = 'submit' value = 'View Other Tickets by Submitter' onclick = 'processData(6, \'<?php echo $id;?>\')'><input type = 'submit' value = 'View Similar Tickets' onclick = processData(7, \'<?php echo $id;?>\')'><input type = 'submit' value = 'Email Submitter' onclick = 'email()'><input type = 'submit' value = 'Go Back to Admin' onclick = 'processData(8, \'<?php echo $id;?>\')'></div>";

			document.getElementById("buttons").appendChild(buttons.firstChild);

			document.getElementById("buttons").innerHTML = "<input type = 'submit' value = 'Close/Reopen Ticket' onclick = 'processData(2, \'<?php echo $id;?>\')'><input type = 'submit' value = 'Assign to me' onclick = 'processData(3, \'<?php echo $id;?>\', \'<?php echo $username;?>\')'><input type = 'submit' value = 'Remove self' onclick = 'processData(4, \'<?php echo $id;?>\', \'<?php echo $username;?>\')'><input type = 'submit' value = 'Email submitter' onclick = 'processData(5, \'<?php echo $id;?>\')'><input type = 'submit' value = 'Delete Ticket' onclick = 'processData(5, \'<?php echo $id;?>\'')'><input type = 'submit' value = 'View Other Tickets by Submitter' onclick = 'processData(6, \'<?php echo $id;?>\')'><input type = 'submit' value = 'View Similar Tickets' onclick = processData(7, \'<?php echo $id;?>\')'><input type = 'submit' value = 'Email Submitter' onclick = 'email()'><input type = 'submit' value = 'Go Back to Admin' onclick = 'processData(8, \'<?php echo $id;?>\')'>"*/

			data = 'type=' + type + '&' + 'id=' + id + '&' + 'username=' + 'username';
			httpRequest.onreadystatechange = function() { selected(httpRequest); };
            httpRequest.send(data);
			
		}


		if (type == 0) {
			data = 'type=' + type + '&' + 'id=' + id;
            httpRequest.send(data);
            document.getElementById("content").innerHTML = "Email sent. <br>";
		}

		if (type == 2) { //close & reopen
			data = 'type=' + type + '&' + 'id=' + id;
			console.log(id);
			httpRequest.send(data);
			document.getElementById("content").innerHTML = "Ticket status has been changed. <br>";	
		}


		if (type == 3) { //assign ticket
			data = 'type=' + type + '&' + 'id=' + id + '&' + 'username=' + username;
			httpRequest.send(data);
			document.getElementById("content").innerHTML = "Ticket has been assigned to you. <br>";
		}

		if (type == 4) { //remove me from ticket
			data = 'type=' + type + '&' + 'id=' + id + '&' + 'username=' + username;
			httpRequest.send(data);
			document.getElementById("content").innerHTML = "You have been removed from this ticket. <br>";
		}

		if (type == 5) { //delete
			data = 'type=' + type + '&' + 'id=' + id;
			httpRequest.send(data);
			document.getElementById("content").innerHTML = "Ticket has been deleted. <br>";
		}

		if (type == 6) { //view other tickets by submitter
			data = 'type=' + type + '&' + 'id=' + id;
			httpRequest.onreadystatechange = function() { samesub(httpRequest); };
            httpRequest.send(data);
		}	

		if (type == 7) { //view similar tickets
			data = 'type=' + type + '&' + 'id=' + id;
			httpRequest.onreadystatechange = function() { similar(httpRequest); };
            httpRequest.send(data);
		}	

		if (type == 8) { //go back to open tickets
			data = 'type=' + type + '&' + 'id=' + id;
			httpRequest.onreadystatechange = function() { opentickets(httpRequest); };
            httpRequest.send(data);
		}


		if (type == 9) { //view my tickets
			data = 'type=' + type + '&' + 'id=' + id + '&' + 'username=' + username;
			httpRequest.onreadystatechange = function() { mytickets(httpRequest); };
            httpRequest.send(data);
		}	

		if (type == 10) { //view all tickets
			data = 'type=' + type + '&' + 'id=' + id;
			httpRequest.onreadystatechange = function() { alltickets(httpRequest); };
            httpRequest.send(data);
		}	

		if (type == 11) { //view unassigned tickets
			data = 'type=' + type + '&' + 'id=' + id;
			httpRequest.onreadystatechange = function() { unassigned(httpRequest); };
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
		document.getElementById("content").innerHTML = request.responseText;
		document.getElementById("table").style.display = "none";  
		document.getElementById("buttons").innerHTML = "<input type = \"submit\" value = \"Close/Reopen Ticket\" onclick = \"processData(2, '<?php echo $id;?>')\"><input type = \"submit\" value = \"Assign to me\" onclick = \"processData(3, '<?php echo $id;?>', '<?php echo $username;?>')\"><input type = \"submit\" value = \"Remove self\" onclick = \"processData(4, '<?php echo $id;?>', '<?php echo $username;?>')\"><input type = \"submit\" value = \"Email submitter\" onclick = \"processData(5, '<?php echo $id;?>')\"><input type = \"submit\" value = \"Delete Ticket\" onclick = \"processData(5, '<?php echo $id;?>')\"><input type = \"submit\" value = \"View Other Tickets by Submitter\" onclick = \"processData(6, '<?php echo $id;?>')\"><input type = \"submit\" value = \"View Similar Tickets\" onclick = \"processData(7, '<?php echo $id;?>')\"><input type = \"submit\" value = \"Email Submitter\" onclick = \"email()\"><input type = \"submit\" value = \"Go Back to Admin\" onclick = \"processData(8, '<?php echo $id;?>')\">"
		document.getElementById("adminbutton").style.display = "none";
	}

	function alltickets(request) {
		document.getElementById("tablespace").innerHTML = request.responseText;
		document.getElementById("table").style.display = "none";  
		document.getElementById("content").style.display = "none"; 
	}

	function samesub(request) {
		document.getElementById("tablespace").innerHTML = request.responseText;
		document.getElementById("content").style.display = "none";
	}

	function mytickets(request) {
		document.getElementById("tablespace").innerHTML = request.responseText;
		document.getElementById("content").style.display = "none";
	}

	function unassigned(request) {
		document.getElementById("tablespace").innerHTML = request.responseText;
		document.getElementById("content").style.display = "none";
	}

	function similar(request) {
		document.getElementById("tablespace").innerHTML = request.responseText;
		document.getElementById("content").style.display = "none";
	}

	function opentickets(request) {
		document.getElementById("tablespace").innerHTML = request.responseText;
		document.getElementById("buttons").style.display = "none";
		document.getElementById("content").style.display = "none";
		document.getElementById("adminbutton").style.display = "";

	}



	function sort(type) {
	    var tbl = document.getElementById("table");
	    var j = type;
	    var store = [];
	    var length = tbl.rows.length-2;
	    for (var i = 1; i < length; i++) {
	    	var row = tbl.rows[i];
	    	var sort = parseFloat(row.cells[j].innerText);
	    	console.log(sort);
	    	if(!isNaN(sort)) {
	    		sort = parseFloat(row.cells[j].innerText.charCodeAt[0]);
	    		store.push([sort, row]);
	    	}
	    }
	    
	    store.sort(function(x, y) {
	    	return x[j] - y[j];
	    });
		for(var i=0, len=store.length; i<len; i++){
	        tbl.appendChild(store[i][1]);
	    }

	    store = null;

	}

	function changepass() {
		document.getElementById("usercontent").innerHTML += "<input type = 'text' name = 'username' placeholder = 'username' size = '30' maxlength = '30' required><br /><br /><textarea rows='4' cols='50' placeholder = 'password' name = 'password' required></textarea><br /><br /><textarea rows='4' cols='50' placeholder = 'confirm password' name = 'confirm' required></textarea><br /><input type = 'submit' value = 'submit' onclick = 'processData(13, \'<?php echo $username;?>\')'>"		
	}

	function email() {
		document.getElementById("content").innerHTML = 	"<p>All fields are required</span></p><input type = 'text' name = 'firstname' placeholder = 'first name' size = '30' maxlength = '30' required><input type = 'text' name = 'lastname' placeholder = 'lastname' size = '30' maxlength = '30' required><input type = 'text' name = 'email' placeholer = 'email' size = '30' maxlength = '30' required><input type = 'text' name = 'subject' placeholder = 'subject' size = '30' maxlength = '30' required><textarea rows='4' cols='50' name = 'message' placeholder = 'message' required></textarea><input type = 'submit' value = 'submit' onclick = 'processData(0, \'<?php echo $username;?>\')'>"
	}


		/*var parsed = JSON.parse(array);

	    var table = document.getElementById("table");

	    table.innerHTML = "";
	    var x = document.createElement("table");
	    x.setAttribute("id", "thetable")
	    var y = document.createElement("tr");
	    y.setAttribute("id", "headerrow");
	    table.appendChild(x);
	   	thetable.appendChild(y);
	    var header = document.createElement("th");
	    var h1 = document.createTextNode("Ticket #");
	    header.appendChild(h1);
	    //console.log(table);
	    document.getElementById("headerrow").appendChild(header);
	    //table.appendChild(header);
	    var header2 = document.createElement("th");
	    var h2 = document.createTextNode("Received");
	    header2.appendChild(h2);
	    //table.appendChild(header2);
	    document.getElementById("headerrow").appendChild(header2);
	    var header3 = document.createElement("th");
	    var h3 = document.createTextNode("Sender Name");
	    header3.appendChild(h3);
	    //table.appendChild(header3);
	    document.getElementById("headerrow").appendChild(header3);
	    var header4 = document.createElement("th");
	    var h4 = document.createTextNode("Sender Email");
	    header4.appendChild(h4);
	    //table.appendChild(header4);
	    document.getElementById("headerrow").appendChild(header4);
	    var header5 = document.createElement("th");
	    var h5 = document.createTextNode("Subject");
	    header5.appendChild(h5);
	    //table.appendChild(header5);
	    document.getElementById("headerrow").appendChild(header5);
	    var header6 = document.createElement("th");
	    var h6 = document.createTextNode("Tech");
	    header6.appendChild(h6);
	    //table.appendChild(header6);
	    document.getElementById("headerrow").appendChild(header6);
	    var header7 = document.createElement("th");
	    var h7 = document.createTextNode("Status");
	    header7.appendChild(h7);
	    //table.appendChild(header7);
	    document.getElementById("headerrow").appendChild(header7);
	    var header8 = document.createElement("th");
	    var h8 = document.createTextNode("Select");
	    header8.appendChild(h8);
	    //table.appendChild(header8);
	    document.getElementById("headerrow").appendChild(header8);

	    console.log(table);


	    var cell;
	    var text;
	    for (var i = 0; i < parsed.length; i++) {
			// creates a table row
			var row = document.createElement("tr");
			row.setAttribute("id", i);
			thetable.appendChild(row);
			for (var j = 0; j < 8; j++) {
				cell = document.createElement("td");
				if (j == 0) {
					text = document.createTextNode(parsed[i].Tickets_id);
					cell.appendChild(text);
					document.getElementById(i).appendChild(cell);
				}				
				if (j == 1) {
					text = document.createTextNode(parsed[i].reg_date);
					cell.appendChild(text);
					document.getElementById(i).appendChild(cell);
				}

				if (j == 2) {
					text = document.createTextNode(parsed[i].semail);
					cell.appendChild(text);
					document.getElementById(i).appendChild(cell);
				}
				if (j == 3) {
					text = document.createTextNode(parsed[i].sname);
					cell.appendChild(text);
					document.getElementById(i).appendChild(cell);
				}
				if (j == 4) {
					text = document.createTextNode(parsed[i].status);
					cell.appendChild(text);
					document.getElementById(i).appendChild(cell);
				}

				if (j == 5) {
					text = document.createTextNode(parsed[i].subject);
					cell.appendChild(text);
					document.getElementById(i).appendChild(cell);
				}

				if (j == 6) {
					text = document.createTextNode(parsed[i].tech);
					cell.appendChild(text);
					document.getElementById(i).appendChild(cell);
				}
				if (j == 7) {
					var input = document.createElement("INPUT");
    				input.setAttribute("type", "radio");
    				input.setAttribute("name", "select");
    				input.setAttribute("onclick", "processData(1)");
    				document.getElementById(i).appendChild(input);
				}

			}
		}*/






</script>

</html>
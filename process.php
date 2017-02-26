<?php

   $db = new mysqli('localhost', 'root', '', 'plwa');
   if ($db->connect_error):
      die ("Could not connect to db " . $db->connect_error);
   endif;

   $id = $_POST['id'];
   $type = $_POST['type'];

   if ($type == 1) { //selected ticket
      $username = $_POST['username'];
      $sql = "SELECT * FROM Tickets WHERE Tickets_id = $id";
      $result = $db->query($sql);

      if ($result->num_rows > 0) {
          // output data of each row
          while($row = $result->fetch_assoc()) {
              echo "Ticket #: " . $row["Tickets_id"] . "<br/>"; 
              echo "Received: " . $row["reg_date"] . "<br/>"; 
              echo "Sender Name: " . $row["sname"] . "<br/>";
              echo "Email: " . $row["semail"] . "<br/>"; 
              echo "Subject: " . $row["subject"] . "<br/>"; 
              echo "Tech: " . $row["tech"] . "<br/>"; 
              echo "Status: " . $row["status"] . "<br/>";
          }
          echo "</table>";

      } else {
          echo "0 results";
      }
   }


   if ($type == 2) { //close or open ticket
      $sql = "SELECT status FROM Tickets WHERE Tickets_id = $id"; #get tickets id!!!
      $query = $db->query($sql);
      $compare = $query->fetch_assoc();

      if($compare["status"] == "open") {
         $close = "UPDATE Tickets SET status = 'closed' WHERE Tickets_id = $id"; 
         $result = $db->query($close) || die(mysql_error());
      }

      else if ($compare["status"] == "closed") {
         $open = "UPDATE Tickets SET status = 'open' WHERE Tickets_id = $id"; 
         $result = $db->query($open) || die(mysql_error());
      }

      echo $result;  
   }

   if ($type == 3) { //assign ticket to me
      //console.log(1);
      $username = $_POST['username'];
      $assign = "UPDATE Tickets SET tech = '$username' WHERE Tickets_id = $id"; 
      $result = $db->query($assign) || die(mysql_error());

      echo $result; 
   }

   if ($type == 4) { //remove self from ticket
      $username = $_POST['username'];
      $remove = "UPDATE Tickets SET tech = NULL WHERE Tickets_id = $id"; 
      $result = $db->query($remove) || die(mysql_error());

      echo $result;
   }

   if ($type == 5) { //delete ticket
      $delete = "DELETE FROM Tickets WHERE Tickets_id = $id";
      $result = $db->query($delete) || die(mysql_error());

      echo $result;  
   }

   if ($type == 6) { //view other tickets by submitter
      $sqlname = "SELECT sname FROM Tickets where Tickets_id = $id";
      $result1 = $db->query($sqlname);
      $compare1 = $result1->fetch_assoc();
      $name = $compare1["sname"];

      $samesub = "SELECT * FROM Tickets WHERE sname = '$name'"; 
      $result = $db->query($samesub);
      if ($result->num_rows) {
         echo "<table id='table'><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
         while($row = $result->fetch_assoc()) {
            $ticketid = $row['Tickets_id'];
            $received = $row["reg_date"];
            $name = $row["sname"];
            $email = $row["semail"]; 
            $subject = $row["subject"];
            $tech = $row["tech"];
            $status = $row["status"];
            echo "<td id='id'>$ticketid</td>";
            echo "<td>$received</td>";
            echo "<td>$name</td>";
            echo "<td>$email</td>";
            echo "<td>$subject</td>";
            echo "<td>$tech</td>";
            echo "<td>$status</td>";
            echo "<td><input type = 'radio' name = 'select' 
                   value = '$ticketid' onclick = 'processData(1, $ticketid)'></td><tr/>";
         }
         echo "<tr><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(1)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(2)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(3)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(4)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(6)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(7)'></td><td></td><td></td></tr>";
         echo "</table>";
         echo "<div id='content'></div>";
         } 
      else {
         $result = die(mysql_error());
         echo $result;
      }
   }

   if ($type == 7) { //similar
      $sqlname = "SELECT subject FROM Tickets WHERE Tickets_id = $id";
      $result1 = $db->query($sqlname);
      $compare1 = $result1->fetch_assoc();
      $subject = $compare1["subject"];

      $word = explode(" ", $subject);

      for ($i = 0; $i < count($word); $i++) {
         $sql = "SELECT * FROM Tickets WHERE subject LIKE '%$word[$i]%'";
         $result = $db->query($sql);
         if ($result->num_rows > 1) {
            echo "<br/>" . "<br/>" . "Tickets with " . "'$word[$i]'". "<br/>" . "<br/>";

            echo "<table><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
            while($row = $result->fetch_assoc()) {
                $ticketid = $row["Tickets_id"];
                echo "<tr><td>".$row["Tickets_id"]."</td><td>".$row["reg_date"]."</td><td>".$row["sname"]."</td>
                    <td>".$row["semail"]."</td><td>".$row["subject"]."</td><td>".$row["tech"]."</td>
                    <td>".$row["status"]."</td><td><input type = 'radio' name = 'select' 
                   value = '<?php echo $ticketid; ?>' onclick = 'processData(1, $ticketid)'></td></tr>";
            }
            echo "<tr><td><a href='tsort.php'>Sort</a></td><td><a href='rsort.php'>Sort</a></td><td><a href='nsort.php'>Sort</a></td><td><a href='esort.php'>Sort</a></td><td><a href='ssort.php'>Sort</a></td><td></td><td></td><td></td></tr>";
            echo "</table>";
         }

         else {
            echo "no similar tickets to display";
         }
      }

   }

   if ($type == 8) { //view open tickets - go bakc to admin button
      $sql = "SELECT * FROM Tickets WHERE status = 'open'";
      $result = $db->query($sql);

      if ($result->num_rows) {
         echo "<table id='table'><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
         while($row = $result->fetch_assoc()) {
            $ticketid = $row['Tickets_id'];
            $received = $row["reg_date"];
            $name = $row["sname"];
            $email = $row["semail"]; 
            $subject = $row["subject"];
            $tech = $row["tech"];
            $status = $row["status"];
            echo "<td id='id'>$ticketid</td>";
            echo "<td>$received</td>";
            echo "<td>$name</td>";
            echo "<td>$email</td>";
            echo "<td>$subject</td>";
            echo "<td>$tech</td>";
            echo "<td>$status</td>";
            echo "<td><input type = 'radio' name = 'select' 
                   value = '$ticketid' onclick = 'processData(1, $ticketid)'></td><tr/>";
         }
         echo "<tr><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(1)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(2)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(3)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(4)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(6)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(7)'></td><td></td><td></td></tr>";
         echo "</table>";
         echo "<div id='content'></div>";
         } 
      else {
         $result = die(mysql_error());
         echo $result;
      }
   }

   if ($type == 9) { //my tickets
      $username = $_POST['username'];
      $mine = "SELECT * FROM Tickets WHERE tech = '$username'";
      $result = $db->query($mine);
      if ($result->num_rows) {
         echo "<table id='table'><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
         while($row = $result->fetch_assoc()) {
            $ticketid = $row['Tickets_id'];
            $received = $row["reg_date"];
            $name = $row["sname"];
            $email = $row["semail"]; 
            $subject = $row["subject"];
            $tech = $row["tech"];
            $status = $row["status"];
            echo "<td id='id'>$ticketid</td>";
            echo "<td>$received</td>";
            echo "<td>$name</td>";
            echo "<td>$email</td>";
            echo "<td>$subject</td>";
            echo "<td>$tech</td>";
            echo "<td>$status</td>";
            echo "<td><input type = 'radio' name = 'select' 
                   value = '$ticketid' onclick = 'processData(1, $ticketid)'></td><tr/>";
         }
         echo "<tr><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(1)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(2)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(3)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(4)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(6)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(7)'></td><td></td><td></td></tr>";
         echo "</table>";
         echo "<div id='content'></div>";
         } 
      else {
         $result = die(mysql_error());
         echo $result;
      }
   }

   if ($type == 10) { //all tickets
      $all = "SELECT * FROM Tickets";
      $result = $db->query($all);
      if ($result->num_rows) {
         echo "<table id='table'><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
         while($row = $result->fetch_assoc()) {
            $ticketid = $row['Tickets_id'];
            $received = $row["reg_date"];
            $name = $row["sname"];
            $email = $row["semail"]; 
            $subject = $row["subject"];
            $tech = $row["tech"];
            $status = $row["status"];
            echo "<td id='id'>$ticketid</td>";
            echo "<td>$received</td>";
            echo "<td>$name</td>";
            echo "<td>$email</td>";
            echo "<td>$subject</td>";
            echo "<td>$tech</td>";
            echo "<td>$status</td>";
            echo "<td><input type = 'radio' name = 'select' 
                   value = '$ticketid' onclick = 'processData(1, $ticketid)'></td><tr/>";
         }
         echo "<tr><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(1)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(2)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(3)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(4)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(6)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(7)'></td><td></td><td></td></tr>";
         echo "</table>";
         echo "<div id='content'></div>";
         } 
      else {
         $result = die(mysql_error());
         echo $result;
      }
   }

   if ($type == 11) { //unassigned tix
      $unassigned = "SELECT * FROM Tickets WHERE tech is NULL";
      $result = $db->query($unassigned);
      if ($result->num_rows) {
         echo "<table id='table'><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
         while($row = $result->fetch_assoc()) {
            $ticketid = $row['Tickets_id'];
            $received = $row["reg_date"];
            $name = $row["sname"];
            $email = $row["semail"]; 
            $subject = $row["subject"];
            $tech = $row["tech"];
            $status = $row["status"];
            echo "<td id='id'>$ticketid</td>";
            echo "<td>$received</td>";
            echo "<td>$name</td>";
            echo "<td>$email</td>";
            echo "<td>$subject</td>";
            echo "<td>$tech</td>";
            echo "<td>$status</td>";
            echo "<td><input type = 'radio' name = 'select' 
                   value = '$ticketid' onclick = 'processData(1, $ticketid)'></td><tr/>";
         }
         echo "<tr><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(1)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(2)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(3)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(4)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(6)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(7)'></td><td></td><td></td></tr>";
         echo "</table>";
         echo "<div id='content'></div>";
         } 
      else {
         $result = die(mysql_error());
         echo $result;
      }
   }

   if ($type == 12) { //users tickets
      $username = $_POST['username'];
      $mine = "SELECT * FROM Tickets WHERE tech = '$username'";
      $result = $db->query($mine);
      if ($result->num_rows) {
         echo "<table id='table'><tr><th>Ticket #</th><th>Received</th><th>Sender Name</th><th>Sender Email</th><th>Subject</th><th>Tech</th><th>Status</th><th>Select</th></tr>";
         while($row = $result->fetch_assoc()) {
            $ticketid = $row['Tickets_id'];
            $received = $row["reg_date"];
            $name = $row["sname"];
            $email = $row["semail"]; 
            $subject = $row["subject"];
            $tech = $row["tech"];
            $status = $row["status"];
            echo "<td id='id'>$ticketid</td>";
            echo "<td>$received</td>";
            echo "<td>$name</td>";
            echo "<td>$email</td>";
            echo "<td>$subject</td>";
            echo "<td>$tech</td>";
            echo "<td>$status</td>";
            echo "<td><input type = 'radio' name = 'select' 
                   value = '$ticketid' onclick = 'processData(1, $ticketid)'></td><tr/>";
         }
         echo "<tr><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(1)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(2)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(3)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(4)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(6)'></td><td>Sort By<input type = 'radio' name = 'sort' onclick = 'sort(7)'></td><td></td><td></td></tr>";
         echo "</table>";
         echo "<div id='content'></div>";
         } 
      else {
         $result = die(mysql_error());
         echo $result;
      }

   }

   if ($type == 13) {
      $username = $_POST['username'];
      $password = $_POST["password"];
      $hashp = md5($password);
      $newpass = "UPDATE Administrators SET password = '$hashp' WHERE username = '$username'";
      $result = $db->query($assign) || die(mysql_error());

      echo $result; 

   }

   if ($type == 0) {
      function test_input($data) {
         $data = trim($data);
         $data = stripslashes($data);
         $data = htmlspecialchars($data);
         return $data;
      }

      $sql = "SELECT sname FROM Tickets WHERE Tickets_id = $id"; #need tix id
      $receiver = "SELECT semail FROM Tickets WHERE Tickets_id = $id";

       if ($db->query($sql) === TRUE) {
         $mailpath = '/Applications/XAMPP/xamppfiles/PHPMailer';
         // Add the new path items to the previous PHP path
         $path = get_include_path();
         set_include_path($path . PATH_SEPARATOR . $mailpath);
         require 'PHPMailerAutoload.php';

         //CONFIRMATION EMAIL TO USER
         $mailer = new PHPMailer();

         $mailer->IsSMTP(); // telling the class to use SMTP
         $mailer->SMTPAuth = true; // enable SMTP authentication
         $mailer->SMTPSecure = "tls"; // sets tls authentication
         $mailer->Host = "smtp.gmail.com"; // sets GMAIL as the SMTP server; or your email service
         $mailer->Port = 587; // set the SMTP port for GMAIL server; or your email server port
         //$mail->Username = "email"; // email username
         //$mail->Password = "password"; // email password
         $mailer->Username = "cs4501.fall15@gmail.com"; // email username
         $mailer->Password = "UVACSROCKS"; // email password

         //$sender = strip_tags($_POST["sender"]);
         $receiver = strip_tags($_POST["email"]);
         $subj = strip_tags($_POST["subject"]);
         $msg = strip_tags($_POST["message"]);

         // Put information into the message
         $mailer->addAddress($receiver);
         $mailer->SetFrom($email);
         $mailer->Subject = $subject;
         $mailer->Body = $message;

      $result = $mailer->send();
      echo $result;
   }
}

?>
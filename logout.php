<?php
//restart the whole session when log out
   session_start();
   
   if(session_destroy()) {
      header("Location: login.php");
   }
?>

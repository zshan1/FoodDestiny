<?php
include 'top.php';
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//user can edit their personal informaton by clicking on the url
//user can also choose to logout form this page
if($username){
print"<ol class='usercenteropt'>";
print '<li class="usercenterlst"><a href="form-user-info.php">Edit password, Email, shipping address</a></li>';
print '<li class="usercenterlst"><a href="logout.php">Logout</a></li>';

    
print"</ol>";
}else{
    print"<h2>You need to log in to reach this page.</h2>";
}

include 'footer.php';
?>

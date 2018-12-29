<?php

include 'top.php';
if ($isAdmin) {
    //get the status ID and cataID and read productid product name product image product price and
    //description form the database
    if(isset($_GET["statusID"])&& $_GET["statusID"] == 1){
        $query = "SELECT `pmkProductID`, `fldProductName`, `fldProductImage`, `fldProductPrice`, `fldDescription` FROM `tblProducts` WHERE `pfkActive` = 1";
     }else if(isset($_GET["statusID"])&& $_GET["statusID"] == 0){
        $query = "SELECT `pmkProductID`, `fldProductName`, `fldProductImage`, `fldProductPrice`, `fldDescription` FROM `tblProducts` WHERE `pfkActive` = 0";
    }else if(isset($_GET["cataID"])){
        $pfkcataID = (int) htmlentities($_GET["cataID"], ENT_QUOTES, "UTF-8");
        $query = "SELECT `pmkProductID`, `fldProductName`, `fldProductImage`, `fldProductPrice`, `fldDescription` FROM `tblProducts` WHERE `pfkCata` = ?";
    }
    //read data from the query
    if(isset($_GET["statusID"])){
    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $informations = $thisDatabaseReader->select($query, '');
    }
    }else if(isset($_GET["cataID"])){
        $find = array($pfkcataID);
        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $informations = $thisDatabaseReader->select($query, $find);
    }
    }
    //debug the data just read from the query, make sure there is data stored in the arrary
    if (DEBUG) {
        print '<p>Contents of the array<pre>';
        print_r($informations);
        print '</pre></p>';
    }
   //display the table for admin view only
    print"<table class='adminlist'>";
    print "<tr class='adminlistrow'>";
    print "<th>Image</th>";
    print "<th>Name</th>"; 
    print "<th>Price</th>";
    print "<th>Discription</th>";
    print "</tr>";
    //display the information stored in the array
    if (is_array($informations)) {
        foreach ($informations as $information) {
            print '<tr class="adminlistrow"><td><a href="product-info-admin.php?id=' . $information['pmkProductID'] . '"><img class="adminproductimg" src="images/' . $information['fldProductImage'] . '.jpg"></a></td><td><a href="product-info-admin.php?id=' . $information['pmkProductID'] . '">' . $information['fldProductName'] . '</a></td><td><a href="product-info-admin.php?id=' . $information['pmkProductID'] . '">' . $information['fldProductPrice'] . '</a></td><td><a href="product-info-admin.php?id=' . $information['pmkProductID'] . '">'.$information['fldDescription'].'</a></td></tr>';
        }
        //if there is nothing in the array, then display the message.
    }if(empty($informations)){
        print '<tr class="adminlistrow"><td>No result found</td><td>No result found</td><td>No result found</td><td>No result found</td></tr>';
    }
    print"</table>";
    //display a message to show user who are not admin but enter this page from url
}else{
    print"<h2>You do not have access to this page!</h2>";
}
include 'footer.php';
?>
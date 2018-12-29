<?php

include 'top.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$fnkKeyWords = -1;
$informations = "";

if (DEBUG) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}

//get keywords from header form
if (isset($_GET["Keywords"])) {
    if ($_GET["Keywords"] == "") {
        print "<p>No results found</p>";
    } else {
        $fnkKeyWords = htmlentities($_GET["Keywords"], ENT_QUOTES, "UTF-8");

        $query = "
    SELECT `pmkProductID`, `fldProductName`, `fldProductImage`, `fldProductPrice`
    FROM `tblProducts` 
    WHERE `fldProductName` REGEXP ? AND `pfkActive`=1";

        $data = array($fnkKeyWords);
        if ($thisDatabaseReader->querySecurityOk($query, 1,1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $informations = $thisDatabaseReader->select($query, $data);
        }

if (DEBUG) {
        print '<p>Contents of the array<pre>';
        print_r($informations);
        print '</pre></p>';
}
//display result in list
print"<ol class='searchol'>";
        if (is_array($informations)) {
            foreach ($informations as $information) {
                print '<li class="searchli"><a class="seachspecial" href="goods.php?productId=' . $information['pmkProductID'] . '"><ol class="searchdisplayleft"><li><img class="adminproductimg" src="images/' . $information['fldProductImage'] .'.jpg" alt="product icon"></li></ol><ol class="searchdisplayright"><li>'. $information['fldProductName'] .'</li><li class="searchproductprice">$'. $information['fldProductPrice'] . '</li></ol></a></li>';
 
            }
            print"</ol>";
        }
        //if the result is empty, display no results found
        if(empty($informations)){
            print "<p>No results found</p>";
        }
        
    }
    //if no value, display nothing to display
} else {
    print "<p>Nothing to display</p>";
}

include 'footer.php';
?>
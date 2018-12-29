<?php
include 'top.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
$pfkCata = -1;
if (DEBUG) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}
//get information under catagories
if (isset($_GET["pfkcata"])) {
    $pfkCata = htmlentities($_GET["pfkcata"], ENT_QUOTES, "UTF-8");
        $query = "
    SELECT `pmkProductID`, `fldProductName`, `fldProductImage`, `fldProductPrice`
    FROM `tblProducts` 
    WHERE `pfkActive`=? AND `pfkcata` = ?";
        $data = array(1,$pfkCata);
        if ($thisDatabaseReader->querySecurityOk($query, 1,1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $Categories = $thisDatabaseReader->select($query, $data);
        }
if (DEBUG) {
        print '<p>Contents of the array<pre>';
        print_r($Categories);
        print '</pre></p>';
}
//display product in list
print"<ol class='searchol'>";
        if (is_array($Categories)) {
            foreach ($Categories as $Category) {
print '<li class="searchli"><a class="seachspecial" href="goods.php?productId=' . $Category['pmkProductID'] . '"><ol class="searchdisplayleft"><li><img class="adminproductimg" src="images/' . $Category['fldProductImage'] .'.jpg" alt="product icon"></li></ol><ol class="searchdisplayright"><li>'. $Category['fldProductName'] .'</li><li class="searchproductprice">$'. $Category['fldProductPrice'] . '</li></ol></a></li>'; 
            }
            print"</ol>";
        }
        //if no result under this category, then display no result found 
        if(empty($Categories)){
            print "<p>No results found</p>";
        }
} 
include 'footer.php';
?>
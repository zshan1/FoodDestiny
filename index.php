<?php
include 'top.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
print '<main>';
if (DEBUG) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}
if ($isAdmin) {
    //for admin display choices to edit add and product
    ?>
    <form class="adminchoice">
        <input type="button" class="adminchoiceadd" value="Add New Product" onclick="window.location.href = 'product-info-admin.php'" />
        <input type="button" class="adminchoiceedit" value="Edit Active Product" onclick="window.location.href = 'admin-list.php?statusID=1'" />
         <input type="button" class="admininactiveedit" value="Edit Inactive Product" onclick="window.location.href = 'admin-list.php?statusID=0'" />
    </form>
    <?php
    //for user display the latest products
} else {
    $loadnewitem = "SELECT `pmkProductID`, `fldProductName`, `fldProductImage` FROM `tblProducts` WHERE `pfkActive`= 1 ORDER BY `pmkProductID` DESC LIMIT 7";
    if ($thisDatabaseReader->querySecurityOk($loadnewitem, 1, 1)) {
        $loadnewitem = $thisDatabaseReader->sanitizeQuery($loadnewitem);
        $newitems = $thisDatabaseReader->select($loadnewitem, '');
    }
    if (DEBUG) {
        print '<p>New Items:</p><pre>';
        print_r($newitems);
        print '</pre>';
    }
//slider found on w3school
    print '<div id="slider" class="w3-content w3-display-container">';

    if (is_array($newitems)) {
        foreach ($newitems as $newitem) {
            print '<a href="goods.php?productId=' . $newitem['pmkProductID'] . '">';
            print '<div class="mySlides">';
            print '<img  class="slideImg" id="slideImg'.$newitem['pmkProductID'].'" src="images/' . $newitem["fldProductImage"] . '.jpg" alt="newly added product">';
            print '<div class="newIntro" id="newIntro'.$newitem['pmkProductID'].'">' . $newitem["fldProductName"];
            print'</div></div></a>';
        }
    }
    print'
</div>
<script>
var myIndex = 0;
carousel();

function carousel() {
    var i;
    var x = document.getElementsByClassName("mySlides");
    for (i = 0; i < x.length; i++) {
       x[i].style.display = "none";  
    }
    myIndex++;
    if (myIndex > x.length) {myIndex = 1}    
    x[myIndex-1].style.display = "block";  
    setTimeout(carousel, 2000); 
}
</script>';


    print PHP_EOL . '<!-- SECTION: 1. debugging setup -->' . PHP_EOL;
// We print out the post array so that we can see our form is working.
    if (DEBUG) {
        print '<p>Post Array:</p><pre>';
        print_r($_POST);
        print '</pre>';
    }
}
print '</main>';
include 'footer.php';
?>
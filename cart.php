<?php
include 'top.php';
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
if (!empty($_SESSION['cart'])) {
    print '<h2 class="alternateRows">Items in the Cart</h2>';

    print '<ol class="searchol">';
//sql statement that seelect product name product image and product price from data base

    foreach ($_SESSION['cart'] as $key => $value) {
        $data = array($key);
        $query = "SELECT `fldProductName`, `fldProductImage`, `fldProductPrice` FROM `tblProducts` WHERE `pmkProductID`=?";
//store the data into array records.

        if ($thisDatabaseReader->querySecurityOk($query, 1)) {
            $query = $thisDatabaseReader->sanitizeQuery($query);
            $records = $thisDatabaseReader->select($query, $data);
        }
                //display the content when user click on the cart icon

        print "<li class='searchli'>";
        print '<a id="seachspecial" href="goods.php?productId=' . $key . '"><ol class="searchdisplayleft"><li><img class="adminproductimg" src="images/' . $records[0]['fldProductImage'] . '.jpg"></li></ol><ol class="searchdisplayright"><li>' . $records[0]['fldProductName'] . "</li></a><li>Total: $" . $records[0]['fldProductPrice'] * $value . "</li><li>";
                //get the total price of the food price times its quantity

        $total += $records[0]['fldProductPrice'] * $value;
        //enable user to modifty quantity in their cart.
        if (isset($_POST["btnUpdateSubmit"])) {
            $updateQuantity = htmlentities($_POST["txtQUpdate"], ENT_QUOTES, "UTF-8");
             //validation for user input quantity, must be numeric numbers.
            if ($updateQuantity == "") {
                $errorMsg4[] = "Please Enter the quantity!";
                $updateQuantityERROR = true;
            } elseif (!verifyNumeric($updateQuantity)) {
                $errorMsg4[] = "Quantity must be integer.";
                $$updateQuantityERROR = true;
            }
             //after user press the submit, display a message that shows the quantity have been modified successfully.
            if (isset($_POST["btnUpdateSubmit"]) AND empty($errorMsg4)) { // closing of if marked with: end body submit
                print '<script> alert("Successfully changed your cart!")</script>';
            }
            print PHP_EOL . '<!-- SECTION 3b Error Messages -->' . PHP_EOL;
//display error message 
            if ($errorMsg4) {
                echo "<script type='text/javascript'> alert(" . json_encode($errorMsg4) . ") </script>";
            }
        }
        //display the from for user to modify the product quantity stored in the cart
        ?>


        <form action = "<?php print $phpSelf; ?>"
              id = "frmUpdate"
              method = "post">
            <input type="hidden" id="hidProductid" name="hidProductid" value= "<?php print $key; ?>">
            <input 
                maxlength = "100"
                name = "txtQUpdate"
                onfocus = "this.select()"
                tabindex = "120"
                type = "text"
                id="inUpdate"
                value = "<?php print $value; ?>"
                >

            <input class = "button" id = "btnUpdateSubmit" name = "btnUpdateSubmit" tabindex = "900" type = "submit" value = "Edit" >
            <input class = "button" id = "btnUpdateDelete" name = "btnUpdateDelete" tabindex = "900" type = "submit" value = "Delete" >
        </form> 
        <?php
        print "</li></ol></li>";
    }
    print"<li class='carttotal'>Total: $" . $total . "</li>";
    ?>
    </ol>
    <form action="checkout.php"
          method="post"
          id="toCheckout">
              <?php
              foreach ($_SESSION['cart'] as $key => $value) {
                  $data = array($key);
                  $query = "SELECT `fldProductName`, `fldProductImage`, `fldProductPrice` FROM `tblProducts` WHERE `pmkProductID`=?";

                  if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                      $query = $thisDatabaseReader->sanitizeQuery($query);
                      $records = $thisDatabaseReader->select($query, $data);
                  }
                  print'<input type="hidden" id="hidProductName' . $key . '" name="hidProductName' . $key . '" value= "' . $records[0]['fldProductName'] . '">';
                  print'<input type="hidden" id="hidProductPrice' . $key . '" name="hidProductPrice' . $key . '" value= "' . $records[0]['fldProductPrice'] . '">';
              }
              ?>
        <input type="hidden" id="hidTotalPrice" name="hidTotalPrice" value= "<?php print $total; ?>">
        <input type="submit" id="btnCheckout" name="btnCheckout" value="Check Out" tabindex="900" class="button">
    </form>
    <?php
} else {
    print '<h2 class="alternateRows">There is nothing in your cart!</h2>';
}
include 'footer.php';
?>
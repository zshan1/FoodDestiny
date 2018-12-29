<?php
include 'top.php';

print PHP_EOL . '<!-- SECTION: 1a. debugging setup -->' . PHP_EOL;
//debug the post value
if (DEBUG) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}
//create the form varible
print PHP_EOL . '<!-- SECTION: 1b form variables -->' . PHP_EOL;
$currentProductID = -1;
$currentDate = "";
$currentQuantity = "";
$currentDateProduct = "";
$currentProductName = "";
$currentProductImage = "";
$currentproductPrice = "";
$currentproductIntro = "";
if (isset($_GET["productId"])) {
//convert the id into a int html entity so that it can be used in form
    $currentProductID = (int) htmlentities($_GET["productId"], ENT_QUOTES, "UTF-8");
    $query = 'SELECT `pmkProductID`, `fldProductName`, `fldProductImage`, `fldProductPrice`, `fldDescription`';
    $query .= 'FROM `tblProducts` WHERE pmkProductID = ?';

    $cart = "SELECT `pmkDateProduct`, `pfkProductID`, `fldQuantity` FROM `tblCart` WHERE `pfkUserName`=? AND `pfkProductID`=?";
//store the pmk trails into an array
    $data = array($currentProductID);
    $searchinfo = array($username, $currentProductID);

    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $productinfos = $thisDatabaseReader->select($query, $data);
    }
    if (DEBUG) {
        print '<p>Post Array:</p><pre>';
        print_r($productinfos);
        print '</pre>';
    }
    $currentProductName = $productinfos[0]["fldProductName"];
    $currentProductImage = $productinfos[0]["fldProductImage"];
    $currentproductPrice = $productinfos[0]["fldProductPrice"];
    $currentproductIntro = $productinfos[0]["fldDescription"];

    
    //store data into array
    if ($thisDatabaseReader->querySecurityOk($cart, 1, 1)) {
        $cart = $thisDatabaseReader->sanitizeQuery($cart);
        $cartinfos = $thisDatabaseReader->select($cart, $searchinfo);
    }
    if (DEBUG) {
        print '<p>Post Array:</p><pre>';
        if (!empty($cartinfos)) {
            print_r($cartinfos);
        } elseif (empty($cartinfos)) {
            print '<p>Post Array:</p>';
        }
        print '</pre>';
    }
//give default value for quantity and dataproduct
    if (!empty($cartinfos)) {
        $alreadyQuantity = $cartinfos[0]["fldQuantity"];
        $alreadyDateProduct = $cartinfos[0]["pmkDateProduct"];
    }

    print PHP_EOL . '<!-- SECTION: 1c form error flags -->' . PHP_EOL;
    $currentQuantityERROR = false;


    print PHP_EOL . '<!-- SECTION: 1d misc variables -->' . PHP_EOL;
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
    $errorMsg3 = array();

    print PHP_EOL . '<!-- SECTION: 2 Process for when the form is submitted -->' . PHP_EOL;
//when user pressed submit
    if (isset($_POST["btnATC"])) {

        print PHP_EOL . '<!-- SECTION: 2a Security -->' . PHP_EOL;

//check if the URL match
        $thisURL = DOMAIN . $phpSelf;

        if (!securityCheck($thisURL)) {
            $msg = '<p>Sorry you cannot access this page.</p>';
            $msg.= '<p>Security breach detected and reported.</p>';
            die($msg);
        }

        print PHP_EOL . '<!-- SECTION: 2b Sanitize (clean) data  -->' . PHP_EOL;

        $currentQuantity = htmlentities($_POST["txtQuantity"], ENT_QUOTES, "UTF-8");
        $currentDate = date("Y-m-d");
        $currentDateProduct = $username . '-' . $currentDate . '-' . $currentProductID;


        print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;
//for textbox, it cannot be empty
//trailname must pass the AplhaNum function
        if ($currentQuantity == "") {
            $errorMsg3[] = "Please Enter the quantity!";
            $currentQuantityERROR = true;
        } elseif (!verifyNumeric($currentQuantity)) {
            $errorMsg3[] = "Quantity must be integer.";
            $currentQuantityERROR = true;
        }

        print PHP_EOL . '<!-- SECTION: 2d Process Form - Passed Validation -->' . PHP_EOL;
//check if the form pass the validation
        if (!$errorMsg3) {
            if (DEBUG)
                print '<p>Form is valid</p>';

            print PHP_EOL . '<!-- SECTION: 2e Save Data -->' . PHP_EOL;
//save the data if it is an update or add


            print PHP_EOL . '<!-- SECTION: 2f Create message -->' . PHP_EOL;
        }
    }

    print PHP_EOL . '<!-- SECTION 3 Display Form -->' . PHP_EOL;
    ?>
    <main>     
        <?php
        print PHP_EOL . '<!-- SECTION 3a  -->' . PHP_EOL;
        //if the user clicked submit and there is no error for their input, then display the message
        if (isset($_POST["btnATC"]) AND empty($errorMsg3)) { // closing of if marked with: end body submit
            print '<script> alert("Successfully add to cart!")</script>';
        }
        print PHP_EOL . '<!-- SECTION 3b Error Messages -->' . PHP_EOL;

        if ($errorMsg3) {
            echo "<script type='text/javascript'> alert(".json_encode($errorMsg3).") </script>";
        }


        print PHP_EOL . '<!-- SECTION 3c html Form -->' . PHP_EOL;
        //ol element for display goods with its name price and description
        ?>
        <ol class="displayGood">
            <?php
            print '<li>';
            print '<ul id="displayleft">';
            print '<li id="productimg"><img id="olproductimg" src="images/' . $currentProductImage . '.jpg" alt="display product"></li>';
            print '</ul>';
            print'</li><li>';
            print '<ul id="displayright">';
            print '<li id="productname"> ' . $currentProductName . '</li>';
            print '<li id="productprice">$' . $currentproductPrice . '</li>';
            print '<li id="productintro">' . $currentproductIntro . '</li>';
            print '<li id="productaddcart">';
//            print '</ul>';
            //form that allows user to add product with different quantity into cart.
            ?>

            <form action="<?php print $phpSelf . '?productId=' . $currentProductID; ?>"
                  method="post"
                  id="addToCart">
                <input id ="inQuantity"
                       type = "text"
                       onfocus = "this.select()"
                       name ="txtQuantity"
                       tabindex = "120"
                       maxlength = "10"
                       <?php
                       if ($currentQuantityERROR)
                           print 'class="mistake"';
                       ?>
                       value = "<?php print $currentQuantity; ?>" 
                       >       
                <input type="submit" id="btnATC" name="btnATC" value="Add to Cart" tabindex="900" class="button">
            </form>
            </li>
            </ul>
        </li>
        </ol>
    </main>   
    <?php
}else {
    print '<h2 class="alternateRows">You do not have the access to this page.</h2>';
}
include 'footer.php';
?>


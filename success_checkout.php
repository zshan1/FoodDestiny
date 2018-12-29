<?php
include 'top.php';

if(isset($_GET['txn_id'])&&isset($_GET['bookID'])&&isset($_GET['currency'])&&isset($_GET['amount'])&&isset($_GET['payerID'])&&isset($_GET['pstatus'])){
print PHP_EOL . '<!-- SECTION: 1 Initialize variables -->' . PHP_EOL;
//initilalize the variables
$data[0] = $_GET['txn_id'];
$data[1] = $_GET['bookID'];
$data[2] = $_GET['currency'];
$data[3] = $_GET['amount'];
$data[4] = $_GET['payerID'];
$data[5] = $_GET['pstatus'];


print PHP_EOL . '<!-- SECTION: 1a. debugging setup -->' . PHP_EOL;
// We print out the post array so that we can see our form is working.
if (DEBUG) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}

//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1b form variables -->' . PHP_EOL;
//give default value for error variables
$data0ERROR=false;
$data1ERROR=false;
$data2ERROR=false;
$data3ERROR=false;
$data4ERROR=false;
$data5ERROR=false;


//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1c form error flags -->' . PHP_EOL;
//
// Initialize Error Flags one for each form element we validate
// in the order they appear on the form

print PHP_EOL . '<!-- SECTION: 1d misc variables -->' . PHP_EOL;
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsgtrans = array();

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
print PHP_EOL . '<!-- SECTION: 2 Process for when the form is submitted -->' . PHP_EOL;

print PHP_EOL . '<!-- SECTION: 2a Security -->' . PHP_EOL;

    // the url for this form https://zshan.w3.uvm.edu/cs148/dev-final/success_checkout.php?txn_id=57F81736J76768820&bookID=90048630024435&currency=USD&amount=5.70&payerID=Z4B6BMKBAKXR4&pstatus=VERIFIED
        $thisURL = DOMAIN . $phpSelf ;

   

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!-- SECTION: 2b Sanitize (clean) data  -->' . PHP_EOL;
//sanitize the data into html form
    $orderdate = date("Y-m-d-H-i-s");
    $data0= htmlentities($username . "-" . $orderdate, ENT_QUOTES, "UTF-8");
    $data1= htmlentities($data[0], ENT_QUOTES, "UTF-8");
    $data2= htmlentities($data[3], ENT_QUOTES, "UTF-8");
    $data3= htmlentities($data[5], ENT_QUOTES, "UTF-8");
    $data4= htmlentities($username, ENT_QUOTES, "UTF-8");
    
    print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;
    //display the error message
    if($data[0]==''){
        $errorMsgtrans[]="Some info is missing!";
        $data0ERROR = True; 
    }
        if($data[1]==''){
        $errorMsgtrans[]="Some info is missing!";
        $data1ERROR = True; 
    }
        if($data[2]==''){
        $errorMsgtrans[]="Some info is missing!";
        $data2ERROR = True; 
    }
        if($data[3]==''){
        $errorMsgtrans[]="Some info is missing!";
        $data3ERROR = True; 
    }
        if($data[4]==''){
        $errorMsgtrans[]="Some info is missing!";
        $data4ERROR = True; 
    }
    
    
if (!empty($username) AND $isUser AND !$errorMsgtrans) {
        $dataEntered = false;
        $dataRecord1 = array();
        $dataDeleted = false;      
        $dataRecord1[] = $data0;
        $dataRecord1[] = $data1;
        $dataRecord1[] = $data2;
        $dataRecord1[] = $data3;
        //begin transaction for inster into the table orders with order id payment id and totalpays
        try {
                $thisDatabaseWriter->db->beginTransaction();
                $query = "INSERT INTO `tblOrders`(`pmkOrderID`, `fldPaymentID`, `fldTotalPay`, `fldPaymentStatus`) VALUES (?,?,?,?)";
                
                    $thisDatabaseWriter->TestSecurityQuery($query, 0);
                    print_r($dataRecord);
                

                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);

                    $results = $thisDatabaseWriter->insert($query, $dataRecord1);

                    $primaryKey = $thisDatabaseWriter->lastInsert();

                    if (DEBUG) {
                        print "<p>pmk= " . $primaryKey;
                    }
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                
                    print "<p>transaction complete ";
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg2[] = "There was a problem with accepting your data please contact us directly.";
            }
//pass value to those variables
    foreach ($_SESSION['cart'] as $key => $value) {
        $dataEntered = false;
        $dataRecord = array();
        $dataDeleted = false;
        $currentDate = date("Y-m-d H:i:s");
        $orderdate = date("Y-m-d-H-i-s");
        // assign values to the dataRecord array
        $dataRecord[] = $data4;
        $dataRecord[] = $data0;
        $dataRecord[] = $key;
        $dataRecord[] = $value;
        $dataRecord[] = $_SESSION['total'][$key];
        $dataRecord[] = $currentDate;
       //begin transcation for insterting data into order details table
        try {
            $thisDatabaseWriter->db->beginTransaction();
            $query = "INSERT INTO `tblOrderDetails`(`pfkUserName`, `pfkOrderID`, `pfkProductID`, `fldQuantity`, `fldPrice`, `fldDate`) VALUES (?,?,?,?,?,?)";

            if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                $query = $thisDatabaseWriter->sanitizeQuery($query);

                $results = $thisDatabaseWriter->insert($query, $dataRecord);

                $primaryKey = $thisDatabaseWriter->lastInsert();
            }
            $dataEntered = $thisDatabaseWriter->db->commit();
        } catch (PDOExecption $e) {
            $thisDatabase->db->rollback();
            if (DEBUG)
                print "Error!: " . $e->getMessage() . "</br>";
        }
    }
}
print PHP_EOL . '<!-- SECTION: 2f Create message -->' . PHP_EOL;
//redirect after complete
//display the message form thankyou.php
?>

<script>
            window.location.href = "thankyou.php"
        </script>
        
        <?php
}else{
    print"<h2>You do not have access to this page!</h2>";
}
include 'footer.php';
?>
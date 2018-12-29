<?php
$phpSelf = htmlentities($_SERVER['PHP_SELF'], ENT_QUOTES, "UTF-8");

$path_parts = pathinfo($phpSelf);
?>
<?php
if(!isset($_SESSION)){
    session_start();
}
//when press the submit 
if (isset($_POST["btnSubmit"]) AND empty($errorMsg)) {
    header("Location: search.php?Keywords=" . htmlentities($_POST["txtSearch"], ENT_QUOTES, "UTF-8"));
    exit;
}
//when user press login to the website
if (isset($_POST["btnloginSubmit"])) {
    include 'lib/constants.php';
    include LIB_PATH . '/Connect-With-Database.php';
    $currentUserName = htmlentities($_POST["txtUserName"], ENT_QUOTES, "UTF-8");
    $currentUserPassword = htmlentities($_POST["txtUserPassword"], ENT_QUOTES, "UTF-8");
    //sql statement for select user id from database
    $infoquery = "SELECT `pmkUserId` FROM `tblUsers` WHERE `pmkUserName`=? AND `fldPassword`=?";
    $inputdata = array($currentUserName, $currentUserPassword);
    //store userinfo into array
    if ($thisDatabaseReader->querySecurityOk($infoquery, 1, 1)) {
        $infoquery = $thisDatabaseReader->sanitizeQuery($infoquery);
        $userinfos = $thisDatabaseReader->select($infoquery, $inputdata);
    }
    if (empty($userinfos)) {
        $error = true;
    } else {
        $_SESSION['login_user'] = $currentUserName;
        header("Location: index.php");
        exit;
    }
}
//when user press the button for atc
if (isset($_POST["btnATC"])) {
    include_once 'lib/validation-functions.php';
    $currentQuantity = htmlentities($_POST["txtQuantity"], ENT_QUOTES, "UTF-8");
    if ($currentQuantity != "" && verifyNumeric($currentQuantity)) {
        $_SESSION['cart'][htmlentities($_GET["productId"], ENT_QUOTES, "UTF-8")] += htmlentities($_POST["txtQuantity"], ENT_QUOTES, "UTF-8");
    }
}
//when user press the button for submit
if(isset($_POST["btnUpdateSubmit"])){
    include_once 'lib/validation-functions.php';
    $updateQuantity = htmlentities($_POST["txtQUpdate"], ENT_QUOTES, "UTF-8");
    if ($updateQuantity != "" && verifyNumeric($updateQuantity)) {
        $_SESSION['cart'][htmlentities($_POST["hidProductid"], ENT_QUOTES, "UTF-8")] = htmlentities($_POST["txtQUpdate"], ENT_QUOTES, "UTF-8");
    }
}
//when user press the button for delete
if(isset($_POST["btnUpdateDelete"])){
    unset($_SESSION['cart'][htmlentities($_POST["hidProductid"], ENT_QUOTES, "UTF-8")]);
    unset($_SESSION['total'][htmlentities($_POST["hidProductid"], ENT_QUOTES, "UTF-8")]);
    unset($_SESSION['name'][htmlentities($_POST["hidProductid"], ENT_QUOTES, "UTF-8")]);
}
//when user press the button for checkout
if(isset($_POST["btnCheckout"])){
    foreach ($_SESSION['cart'] as $key => $value) {
        $_SESSION['name'][$key] = (string)htmlentities($_POST["hidProductName".$key], ENT_QUOTES, "UTF-8");
        $_SESSION['total'][$key] = (string)htmlentities($_POST["hidProductPrice".$key], ENT_QUOTES, "UTF-8");
    }
    $_SESSION['checkout'] = (string)htmlentities($_POST["hidTotalPrice"], ENT_QUOTES, "UTF-8");
}
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <title>Food Destiny</title>
        <meta charset="utf-8">
        <meta name="author" content="Robert Erickson, Zixiao Shan and Junfei Ma">
        <meta name="description" content="Official website for Food Destiny, for those people who love Asian foods">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!--[if lt IE 9]>
        <script src="//html5shim.googlecode.com/sin/trunk/html5.js"></script>
        <![endif]-->

        <link rel="stylesheet" href="css/custom.css" type="text/css" media="screen">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
        <link href="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/themes/smoothness/jquery-ui.css" rel="stylesheet">
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.12.1/jquery-ui.min.js"></script>
        <script>
            $(document).ready(function () {
                $(".datepicker").datepicker({dateFormat: 'yy-mm-dd'});
            });
        </script>


<?php
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
        // inlcude all libraries. 
// 
// %^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
print '<!-- begin including libraries -->';
// Step one: generally this code is in top.php
include 'lib/constants.php';
require_once 'lib/security.php';
include_once 'lib/validation-functions.php';
include_once 'lib/mail-message.php';
include LIB_PATH . '/Connect-With-Database.php';
print '<!-- libraries complete-->';

$isAdmin = false;
$isUser = false;
$username = $_SESSION['login_user'];
//if the user is already login check if this user is admin
if(!empty($username)){
$getadmin = "		
             SELECT `pmkAdmin` 		
             FROM `tblAdmins`";		
		
         if ($thisDatabaseReader->querySecurityOk($getadmin, 0)) {		
             $getadmin = $thisDatabaseReader->sanitizeQuery($getadmin);		
             $adminlists = $thisDatabaseReader->select($getadmin, '');		
         }			
		
         if (in_array($username, array_column($adminlists, 'pmkAdmin'))) {		
             $isAdmin = true;		
         } 	
         if(!in_array($username, array_column($adminlists, 'pmkAdmin'))){		
             $isUser = true;		
         }
}
?>	
    </head>

    <!-- **********************     Body section      ********************** -->
<?php
print '<body id="' . $PATH_PARTS['filename'] . '">';
include 'nav.php';
include 'header.php';

if (DEBUG) {
    print '<p>user info:</p><pre>';
    print_r($_SESSION);
    print '</pre>';
}
?>
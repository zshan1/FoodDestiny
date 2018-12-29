<?php
include 'top.php';
if (!empty($_SESSION['cart'])){
$_SESSION['cart']=array();
$_SESSION['name']=array();
$_SESSION['total']=array();
unset($_SESSION['checkout']);
}
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
//display the message after user press checkout
?>

<h1>We have received your order, thank you for your purchase!</h1>
<script>
setTimeout(function () {
   window.location.href= 'index.php'; // the redirect goes here

},4000);
</script>
<?php
include 'footer.php';
?>
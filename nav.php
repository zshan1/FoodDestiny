<!-- ######################     Main Navigation   ########################## -->

<?php
print '<ol class = "topnav">';
// This sets a class for current page so you can style it differently
if (!empty($username)) {
    if ($isAdmin) {
        print '<li id = "navinfo">Hello, Admin!</li>';
    } else {
        print '<li id = "navinfo">Hello, ' . $username . '!</li>';
    }
} else {
    print '<li id = "navinfo">Hello, visitor!</li>';
}
print '<li id="navright"';
if ($PATH_PARTS['filename'] == 'cart') {
    print ' class="activePage" ';
}
if(!$isAdmin){
if (empty($_SESSION['cart'])) {
    print '><a href="cart.php"><img src="images/cart.jpg" class="navimg" alt="cart icon"></a></li>';
} else {
    print '><a href="cart.php"><img src="images/cartfull.jpg" class="navimg" alt="cartfull icon"></a></li>';
}
}
if (!empty($username)) {
    print '<li id="navright2"';
    if ($PATH_PARTS['filename'] == 'usercenter') {
        print ' class="activePage" ';
    }
    print '><a href="usercenter.php"><img src="images/account.jpg" class="navimg" alt="user center icon"></a></li>';
} else {
    print '<li id="navright2"';
    if ($PATH_PARTS['filename'] == 'login') {
        print ' class="activePage" ';
    }
    print '><a href="login.php"><img src="images/account.jpg" class="navimg" alt="user center icon"></a></li>';
}
?>
</ol>

<!-- #################### Ends Main Navigation    ########################## -->


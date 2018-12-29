<?php
include 'top.php';
print "</main>";
print "<ol class='searchol'>";
//sql statement for select product name, product image and product price from database
foreach ($_SESSION['cart'] as $key => $value) {
    $data = array($key);
    $query = "SELECT `fldProductName`, `fldProductImage`, `fldProductPrice` FROM `tblProducts` WHERE `pmkProductID`=?";
//stored data into array records.
    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $records = $thisDatabaseReader->select($query, $data);
    }
    print "<li class='searchli'>";
    print '<ol class="searchdisplayleft"><li><img class="adminproductimg" src="images/' . $records[0]['fldProductImage'] . '.jpg"></li></ol><ol class="searchdisplayright"><li>' . $records[0]['fldProductName'] . "</li><li>Quantity: " . $value . "</li><li>Total: $" . $records[0]['fldProductPrice'] * $value . "</li></ol>";
}
//make sure that the checkout page must associate with a username
if (!empty($username)) {
    print "<li class='searchli'>";
    print "<ol class='addressdisplayleft'>";
    $data = array($username);
        //sql statement for select product information and customer information
    $query = "SELECT `fldProductName`, `fldProductImage`, `fldProductPrice` FROM `tblProducts` WHERE `pmkProductID`=?";
    $reload = "
            SELECT `fldFirstName`, `fldLastName`, `fldPhoneNumber`, `fldAddress`, `fldCity`, `fldState`, `fldCountry`, `fldZipNumber` 
            FROM `tblCustomerAddresses` 
            WHERE `pfkUserName`=?";
    //store the data into an array userinfor by using the reload sql staement
    if ($thisDatabaseReader->querySecurityOk($reload, 1)) {
        $reload = $thisDatabaseReader->sanitizeQuery($reload);
        $userinfo = $thisDatabaseReader->select($reload, $data);
    }
    //set up default value for user information
    if (!empty($userinfo[0])) {
        print '<li>Shipping Address: </li>';
        print '<li>Name: ' . $userinfo[0]['fldFirstName'] . " " . $userinfo[0]['fldLastName'] . "</li>";
        print '<li>Phone: ' . $userinfo[0]['fldPhoneNumber'] . "</li>";
        print '<li>Address: ' . $userinfo[0]['fldAddress'] . ", " . $userinfo[0]['fldCity'] . ", " . $userinfo[0]['fldState'] . ", " . $userinfo[0]['fldCountry'] . ", " . $userinfo[0]['fldZipNumber'] . "</li>";
        ?>  
        </ol>
        <ol class="addressdisplayright">
            <form><input type = 'button' id = 'btnUpdateGo' value = 'Edit' onclick='window.location.href = "form-user-info.php"'></form>
        </ol>
        <?php
        //if there is no shipping information associated with the username in database, display a message.
    } else {
        print '<li>No Shipping Address in your profile!</li>';
        print '</ol>';
        print '<ol class="addressdisplayright">';
        ?>
        <form><input type = 'button' id = 'btnUpdateGo' value = 'Create' onclick='window.location.href = "form-user-info.php"'></form>
        </ol>
        <?php
    }

    print '</li></ol>';
    if (!empty($userinfo[0])) {
        ?>
        <div id="paypal-button-container"></div>
        <script src="https://www.paypalobjects.com/api/checkout.js"></script>
        <script>
                    // Render the PayPal button
                    var EXECUTE_PAYMENT_URL = 'https://zshan.w3.uvm.edu/cs148/dev-final/success_checkout.php';
                    paypal.Button.render({
                    // Set your environment
                    env: 'sandbox', // sandbox | production

                            // Specify the style of the button
                            style: {
                            layout: 'vertical', // horizontal | vertical
                                    size:   'medium', // medium | large | responsive
                                    shape:  'rect', // pill | rect
                                    color:  'gold'       // gold | blue | silver | white | black
                            },
                            // Specify allowed and disallowed funding sources
                            //
                            // Options:
                            // - paypal.FUNDING.CARD
                            // - paypal.FUNDING.CREDIT
                            // - paypal.FUNDING.ELV
                            funding: {
                            allowed: [
                                    paypal.FUNDING.CARD,
                                    paypal.FUNDING.CREDIT
                            ],
                                    disallowed: []
                            },
                            // Enable Pay Now checkout flow (optional)
                            commit: true,
                            // PayPal Client IDs - replace with your own
                            // Create a PayPal app: https://developer.paypal.com/developer/applications/create
                            client: {
                            sandbox: 'AZ4GbFcrHuJq66TmlBSlcDXs0TYK5j56T4Ig3KBwSjI-LoDkMUR0X2xR5GhgDalDZR8_rZxbspHaRITT',
                                    production: 'EFjpiBPM0NDjzmSjuPIONfriImzJr5NgEuu-LRoT59Z4EiMBECZv0BJhX5AfMd1Gi71ZvziLQE8O1tbd'
                            },
                            payment: function (data, actions) {
                            return actions.payment.create({
                            payment: {
                            transactions: [
                            {
                            amount: {
                            total: <?php echo json_encode($_SESSION['checkout'], JSON_HEX_TAG); ?>,
                                    currency: 'USD'
                            }, description: 'The payment transaction description.',
                                    custom: '90048630024435',
                                    payment_options: {
                                    allowed_payment_method: 'INSTANT_FUNDING_SOURCE'
                                    },
                                    soft_descriptor: 'ECHI5786786',
                                    item_list: {
                                    items:[



        <?php
        $numItems = count($_SESSION['cart']);
        $i = 0;
        foreach ($_SESSION['cart'] as $key => $value) {
            ?>
                                        {
                                        name: <?php echo json_encode($_SESSION['name'][$key], JSON_HEX_TAG); ?>,
                                                quantity: <?php echo json_encode($value, JSON_HEX_TAG); ?>,
                                                price: <?php echo json_encode($_SESSION['total'][$key], JSON_HEX_TAG); ?>,
                                                sku: <?php echo json_encode($key, JSON_HEX_TAG); ?>,
                                                currency: 'USD'
                                        }
            <?php
            if (++$i !== $numItems) {
                echo ",";
            }
        }
        ?>


                                    ],
                                            shipping_address: {
                                            recipient_name: <?php echo json_encode($userinfo[0]['fldFirstName'] . " " . $userinfo[0]['fldLastName'], JSON_HEX_TAG); ?>,
                                                    line1: <?php echo json_encode($userinfo[0]['fldAddress'], JSON_HEX_TAG); ?>,
                                                    city: <?php echo json_encode($userinfo[0]['fldCity'], JSON_HEX_TAG); ?>,
                                                    country_code: <?php echo json_encode($userinfo[0]['fldCountry'], JSON_HEX_TAG); ?>,
                                                    postal_code: <?php echo json_encode($userinfo[0]['fldZipNumber'], JSON_HEX_TAG); ?>,
                                                    phone: <?php echo json_encode($userinfo[0]['fldPhoneNumber'], JSON_HEX_TAG); ?>,
                                                    state: <?php echo json_encode($userinfo[0]['fldState'], JSON_HEX_TAG); ?>
                                            }}

                            }
                            ]
                            }
                            });
                            },
                            onAuthorize: function(data, actions) {
                            return actions.payment.get().then(function(payment) {
                            //debugger;
                            console.log(payment);
                                    var txn_id = payment.cart;
                                    var bookID = payment.transactions[0].custom;
                                    var currency = payment.transactions[0].amount["currency"];
                                    var amount = payment.transactions[0].amount["total"];
                                    var payerID = payment.payer.payer_info["payer_id"];
                                    var pstatus = payment.payer.status;
                                    var successUrl = EXECUTE_PAYMENT_URL + '?txn_id=' + txn_id + '&bookID=' + bookID + '&currency=' + currency + '&amount=' + amount + '&payerID=' + payerID + '&pstatus=' + pstatus;
                                    //console.log(newUrl);
                                    window.location.replace(successUrl);
                            });
                            }
                    }, '#paypal-button-container');</script>
        <?php
    }
} else {
    print "<h2>You need a account to checkout!</h2>";
    ?>

    <form>
        <input type='button' value='login' onclick='window.location.href = "login.php"' />
        <input type="button" value="sign up" onclick="window.location.href = 'form-user-info.php'" />
    </form>   

    </main>
    <?php
}
include 'footer.php';
?>
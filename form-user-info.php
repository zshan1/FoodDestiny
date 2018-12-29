<?php
include 'top.php';
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//       
print PHP_EOL . '<!-- SECTION: 1 Initialize variables -->' . PHP_EOL;
// initialize update to allow the form achieve multiple purpose

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
//
// Initialize variables one for each form element
// in the order they appear on the form
$currentUserName = "";
$currentUserPassword = "";
$currentUserRePassword = "";
$currentFirstName = "";
$currentLastName = "";
$currentPhoneNumber = "";
$currentEmail = "";
$currentAddress = "";
$currentCity = "";
$currentState = "";
$currentCountry = "";
$currentZipNumber = "";
//sql statement that select all the users information from the database
if ($isUser) {
    $currentUserName = htmlentities($username, ENT_QUOTES, "UTF-8");
    $reload = "
            SELECT `fldFirstName`, `fldLastName`, `fldPhoneNumber`, `fldEmail`, `fldAddress`, `fldCity`, `fldState`, `fldCountry`, `fldZipNumber` 
            FROM `tblCustomerAddresses` 
            WHERE `pfkUserName`=?";
        //store the information into array

    $name = array($currentUserName);
    if ($thisDatabaseReader->querySecurityOk($reload, 1)) {
        $reload = $thisDatabaseReader->sanitizeQuery($reload);
        $userinfo = $thisDatabaseReader->select($reload, $name);
    }
        //debug the array that just stored data from dababase.

    if (DEBUG) {
        print '<p>Contents of the array<pre>';
        print_r($userinfo);
        print '</pre></p>';
    }
        //default value for all the user information variables.

    if(!empty($userinfo)){
    $currentFirstName = $userinfo[0]['fldFirstName'];
    $currentLastName = $userinfo[0]['fldLastName'];
    $currentPhoneNumber = $userinfo[0]['fldPhoneNumber'];
    $currentEmail = $userinfo[0]['fldEmail'];
    $currentAddress = $userinfo[0]['fldAddress'];
    $currentCity = $userinfo[0]['fldCity'];
    $currentState = $userinfo[0]['fldState'];
    $currentCountry = $userinfo[0]['fldCountry'];
    $currentZipNumber = $userinfo[0]['fldZipNumber'];
    }
}

//sql statement for select country 

$countryquery = 'SELECT `fnkCountryName`, `fnkCountryCode` FROM `tblCountry`';

//stored the information into array
if ($thisDatabaseReader->querySecurityOk($countryquery, 0)) {
        $countryquery = $thisDatabaseReader->sanitizeQuery($countryquery);
        $countrycodes = $thisDatabaseReader->select($countryquery, '');
}

//debug the array
if (DEBUG) {
        print '<p>New Items:</p><pre>';
        print_r($countrycodes);
        print '</pre>';
    }
    
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1c form error flags -->' . PHP_EOL;
//
// Initialize Error Flags one for each form element we validate
// in the order they appear on the form
//give default value false for every error flags
$currentUserNameERROR = false;
$currentUserPasswordERROR = false;
$currentUserRePasswordERROR = false;
$currentFirstNameERROR = false;
$currentLastNameERROR = false;
$currentPhoneNumberERROR = false;
$currentEmailERROR = false;
$currentAddressERROR = false;
$currentCityERROR = false;
$currentStateERROR = false;
$currentCountryERROR = false;
$currentZipNumberERROR = false;

////%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1d misc variables -->' . PHP_EOL;
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg2 = array();

//@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
//
print PHP_EOL . '<!-- SECTION: 2 Process for when the form is submitted -->' . PHP_EOL;
//
if (isset($_POST["btninfoSubmit"])) {

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!-- SECTION: 2a Security -->' . PHP_EOL;

    // the url for this form
    $thisURL = DOMAIN . $phpSelf;

    if (DEBUG) {
        print '<p>Contents of the URL<pre>';
        print_r($phpSelf);
        print '</pre></p>';
    }

    if (!securityCheck($thisURL)) {
        $msg = '<p>Sorry you cannot access this page.</p>';
        $msg.= '<p>Security breach detected and reported.</p>';
        die($msg);
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!-- SECTION: 2b Sanitize (clean) data  -->' . PHP_EOL;
    // remove any potential JavaScript or html code from users input on the
    // form. Note it is best to follow the same order as declared in section 1c.       


    $currentUserName = htmlentities($_POST["txtUserName"], ENT_QUOTES, "UTF-8");
    $currentUserPassword = htmlentities($_POST["txtPassword"], ENT_QUOTES, "UTF-8");
    $currentUserRePassword = htmlentities($_POST["txtRePassword"], ENT_QUOTES, "UTF-8");
    $currentFirstName = htmlentities($_POST["txtFirstName"], ENT_QUOTES, "UTF-8");
    $currentLastName = htmlentities($_POST["txtLastName"], ENT_QUOTES, "UTF-8");
    $currentPhoneNumber = htmlentities($_POST["txtPhoneNumber"], ENT_QUOTES, "UTF-8");
    $currentEmail = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL); 
    $currentAddress = htmlentities($_POST["txtAddress"], ENT_QUOTES, "UTF-8");
    $currentCity = htmlentities($_POST["txtCity"], ENT_QUOTES, "UTF-8");
    $currentState = htmlentities($_POST["txtState"], ENT_QUOTES, "UTF-8");
    $currentCountry = htmlentities($_POST["lstCountry"], ENT_QUOTES, "UTF-8");
    $currentZipNumber = htmlentities($_POST["txtZipCode"], ENT_QUOTES, "UTF-8");

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;
    //
    // Validation section. Check each value for possible errors, empty or not what we expect. 
    if ($currentUserName == "") {
        $errorMsg[] = "Please enter user name!";
        $currentUserNameERROR = true;
    } elseif (!verifyAlphaNum($currentUserName)) {
        $errorMsg[] = "The name you typed in seems not correct!";
        $currentUserNameERROR = true;
    }elseif (in_array($currentUserName, array_column($usernameinfos, 'pmkUserName'))){
        $errorMsg[] = "The name already exists!";
        $currentUserNameERROR = true;
    }
    if ($currentUserPassword == "") {
        $errorMsg[] = "Please enter user password!";
        $currentUserPasswordERROR = true;
    } elseif (!verifyNumeric($currentUserPassword)) {
        $errorMsg[] = "The password you typed in seems not correct!";
        $currentUserPasswordERROR = true;
    }
    if ($currentUserRePassword == "") {
        $errorMsg[] = "Please re-enter user password";
        $currentUserRePasswordERROR = true;
    } elseif ($currentUserPassword !== $currentUserRePassword) {
        $errorMsg[] = "Password do not correspond!";
        $currentUserRePasswordERROR = true;
    }
    if ($currentFirstName == "") {
        $errorMsg2[] = "Please enter your first name!";
        $currentFirstNameERROR = true;
    } elseif (!verifyAlphaNum($currentFirstName)) {
        $errorMsg2[] = "This does not seems like a first name";
        $currentFirstNameERROR = true;
    }
    if ($currentLastName == "") {
        $errorMsg2[] = "Please enter your last name!";
        $currentLastNameERROR = true;
    } elseif (!verifyAlphaNum($currentLastName)) {
        $errorMsg2[] = "This does not seems like a last name";
        $currentLastNameERROR = true;
    }
    if ($currentPhoneNumber == "") {
        $errorMsg2[] = "Please enter your phone number!";
        $currentPhoneNumberERROR = true;
    } elseif (!verifyPhone($currentPhoneNumber)) {
        $errorMsg2[] = "This does not seems like a phone number";
        $currentPhoneNumberERROR = true;
    }
     if ($currentEmail == "") {
        $errorMsg[] = 'Please enter your email address';
        $currentEmailERROR = true;
    } elseif (!verifyEmail($currentEmail)) {       
        $errorMsg[] = 'Your email address appears to be incorrect.';
        $currentEmailERROR = true;    
    }    
    if ($currentAddress == "") {
        $errorMsg2[] = "Please enter your address!";
        $currentAddressERROR = true;
    } elseif (!verifyAlphaNum($currentAddress)) {
        $errorMsg2[] = "This does not seems like an address";
        $currentAddressERROR = true;
    }
    if ($currentCity == "") {
        $errorMsg2[] = "Please enter your city!";
        $currentCityERROR = true;
    } elseif (!verifyAlphaNum($currentCity)) {
        $errorMsg2[] = "The does not seems like a city";
        $currentCityERROR = true;
    }
    if ($currentState == "") {
        $errorMsg2[] = "Please enter your state!";
        $currentStateERROR = true;
    } elseif (!verifyAlphaNum($currentState)) {
        $errorMsg2[] = "The does not seems like a state";
        $currentStateERROR = true;
    }
    if ($currentCountry == "") {
        $errorMsg2[] = "Please enter your country!";
        $currentCountryERROR = true;
    } elseif (isset($_REQUEST['lstCountry']) && $_REQUEST['lstCountry'] == '0') {
        $errorMsg2[] = "The does not seems like a country";
        $currentCountryERROR = true;
    }

    if ($currentZipNumber == "") {
        $errorMsg2[] = "Please enter your zip code!";
        $currentZipNumberERROR = true;
    } elseif (!verifyNumeric($currentZipNumber)) {
        $errorMsg2[] = "The does not seems like a zip code";
        $currentZipNumber = true;
    }

    //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
    print PHP_EOL . '<!-- SECTION: 2d Process Form - Passed Validation -->' . PHP_EOL;
    //
    // Process for when the form passes validation (the errorMsg array is empty)
    //    
    if (!$errorMsg2) {
        if (DEBUG)
            print '<p>Form is valid</p>';
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        print PHP_EOL . '<!-- SECTION: 2e Save Data -->' . PHP_EOL;
        //save data into a array for upload
        //if it is update, update text field first, then delete tags in db and push new tags to db
        if ($isUser == false) {
            $dataEntered = false;
            $dataRecord = array();
            $dataDeleted = false;
            // assign values to the dataRecord array
            $dataRecord[] = $currentUserName;
            $dataRecord[] = $currentFirstName;
            $dataRecord[] = $currentLastName;
            $dataRecord[] = $currentPhoneNumber;
            $dataRecord[] = $currentEmail;
            $dataRecord[] = $currentAddress;
            $dataRecord[] = $currentCity;
            $dataRecord[] = $currentState;
            $dataRecord[] = $currentCountry;
            $dataRecord[] = $currentZipNumber;
            //begin transsaction, pass the user's information into database.
            try {
                $thisDatabaseWriter->db->beginTransaction();
                $query = "INSERT INTO `tblCustomerAddresses`(`pfkUserName`, `fldFirstName`, `fldLastName`, `fldPhoneNumber`, `fldEmail`, `fldAddress`, `fldCity`, `fldState`, `fldCountry`, `fldZipNumber`) VALUES (?,?,?,?,?,?,?,?,?,?)";
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 0);
                    print_r($dataRecord);
                }
                if ($thisDatabaseReader->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->update($query, $dataRecord);
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                if (DEBUG)
                    print "<p>transaction complete ";
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                if (DEBUG)
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg2[] = "There was a problem with accepting your data please contact us directly.";
            }
           $dataEntered = false;
           $dataRecordmain=array();
           $dataRecordmain[]=$currentUserName;
           $dataRecordmain[]=$currentUserPassword;
           //begin transaction for storing users username and password.
           try {
                $thisDatabaseWriter->db->beginTransaction();
                $query = "INSERT INTO `tblUsers`(`pmkUserName`, `fldPassword`) VALUES (?,?)";
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 0);
                    print_r($dataRecordmain);
                }
                if ($thisDatabaseReader->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->update($query, $dataRecordmain);
                    $primaryKey = $thisDatabaseWriter->lastInsert();

                    if (DEBUG) {
                        print "<p>pmk= " . $primaryKey;
                    }
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                if (DEBUG)
                    print "<p>transaction complete ";
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                if (DEBUG)
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
            }
        } 
        //if the username is exist then begin transcation for user to update their information
        elseif ($isUser == true) {
            $dataEntered = false;
            $dataRecord = array();
            $dataDeleted = false;
            // assign values to the dataRecord array

            $dataRecord[] = $currentFirstName;
            $dataRecord[] = $currentLastName;
            $dataRecord[] = $currentPhoneNumber;
            $dataRecord[] = $currentEmail;
            $dataRecord[] = $currentAddress;
            $dataRecord[] = $currentCity;
            $dataRecord[] = $currentState;
            $dataRecord[] = $currentCountry;
            $dataRecord[] = $currentZipNumber;
            $dataRecord[] = $currentUserName;
            //begin transcation for updating user's information

            try {
                $thisDatabaseWriter->db->beginTransaction();
                $query = "UPDATE `tblCustomerAddresses` SET `fldFirstName`=?,`fldLastName`=?,`fldPhoneNumber`=?,`fldEmail`=?,`fldAddress`=?,`fldCity`=?,`fldState`=?,`fldCountry`=?,`fldZipNumber`=? WHERE `pfkUserName`=?";

                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 1);
                    print_r($dataRecord);
                }

                if ($thisDatabaseWriter->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);

                    $results = $thisDatabaseWriter->insert($query, $dataRecord);

                    $primaryKey = $thisDatabaseWriter->lastInsert();

                    if (DEBUG) {
                        print "<p>pmk= " . $primaryKey;
                    }
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                if (DEBUG)
                    print "<p>transaction complete ";
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                if (DEBUG)
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg2[] = "There was a problem with accepting your data please contact us directly.";
            }
            $dataEntered = false;
            $dataRecordmain=array();
           $dataRecordmain[]=$currentUserPassword;
           $dataRecordmain[]=$currentUserName;
           //begin transaction for updating user's passwork
           try {
                $thisDatabaseWriter->db->beginTransaction();
                $query = "UPDATE `tblUsers` SET `fldPassword`=? WHERE `pmkUserName` =?";
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 1);
                    print_r($dataRecordmain);
                }
                if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->update($query, $dataRecordmain);
                    $primaryKey = $thisDatabaseWriter->lastInsert();

                    if (DEBUG) {
                        print "<p>pmk= " . $primaryKey;
                    }
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                if (DEBUG)
                    print "<p>transaction complete ";
            } catch (PDOExecption $e) {
                $thisDatabase->db->rollback();
                if (DEBUG)
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
            }
        }
        // all sql statements are done so lets commit to our changes
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
        //
        print PHP_EOL . '<!-- SECTION: 2f Create message -->' . PHP_EOL;
        //
        // build a message to display on the screen in section 3a and to mail
        // to the person filling out the form (section 2g).

        $message = '<h3>Submission Receipt:</h3>';

        foreach ($_POST as $htmlName => $value) {

            $message .= '<p class="success">';
            // breaks up the form names into words. for example
            // txtFirstName becomes First Name       
            $camelCase = preg_split('/(?=[A-Z])/', substr($htmlName, 3));

            foreach ($camelCase as $oneWord) {
                $message .= $oneWord . ' ';
            }

            $message .= ' = ' . htmlentities($value, ENT_QUOTES, "UTF-8") . '</p>';
        }
        //@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@
    //
        print PHP_EOL . '<!-- SECTION: 2g Mail to user -->' . PHP_EOL;
        //
        // Process for mailing a message which contains the forms data
        // the message was built in section 2f.
        $to = $currentEmail; // the person who filled out the form     
        $cc = '';       
        $bcc = '';
        $from = 'Food Destiny <customer.service@fooddestiny.com>';
        // subject of mail should make sense to your form
        $subject = 'Your info submitted successfully!';
        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);

    } // end form is valid     
}   // ends if form was submitted.
//#############################################################################
//
print PHP_EOL . '<!-- SECTION 3 Display Form -->' . PHP_EOL;
//
?>       
<main>     
    <article>
        <?php
//####################################
//
        print PHP_EOL . '<!-- SECTION 3a  -->' . PHP_EOL;
// 
// If its the first time coming to the form or there are errors we are going to display the form.

        if (isset($_POST["btninfoSubmit"]) AND empty($errorMsg2)) {
             // closing of if marked with: end body submit
                if ($isUser == true) {
                    print '<h2>Edit submitted successfully! </h2>';
                } else {
                    print '<h2>Address info submitted successfully!</h2>';
                }
                print '<fieldset class = "yescss">';
                print $message;
                print '</fieldset>';
            } else {
                if ($isUser == true) {
                    print '<h2>Please edit your information!</h2>';
                } else {
                    print '<h2>Please enter your information!</h2>';
                }

                //####################################
                //
        print PHP_EOL . '<!-- SECTION 3b Error Messages -->' . PHP_EOL;
                //
                // display any error messages before we print out the form

                if ($errorMsg) {
                    print '<div id="errors">' . PHP_EOL;
                    print '<h2>Your form has the following mistakes that need to be fixed.</h2>' . PHP_EOL;
                    print '<ol>' . PHP_EOL;

                    foreach ($errorMsg as $err) {
                        print '<li>' . $err . '</li>' . PHP_EOL;
                    }

                    print '</ol>' . PHP_EOL;
                    print '</div>' . PHP_EOL;
                
            }
            //####################################
            //
        print PHP_EOL . '<!-- SECTION 3c html Form -->' . PHP_EOL;
            // Display the HTML form. note that the action is to this same page. $phpSelf is defined in top.php
            ?>    
            <form action = "<?php print $phpSelf; ?>"
                  id = "frmRegister"
                  method = "post">
                <input type="hidden" id="hidnetId" name="hidnetId" value= "<?php print $currentpmkNetId; ?>">

                <fieldset class = "contact"> 
                    <fieldset class = "yescss">
                        <p>
                                <label class = "required">UserName: </label>
                                <input 
                                <?php if ($currentUserNameERROR) print 'class="mistake"'; ?>
                                    maxlength = "100"
                                    name = "txtUserName"
                                    onfocus = "this.select()"
                                    tabindex = "120"
                                    type = "text"
                                    value = "<?php print $currentUserName; ?>"
                                    >
                            </p>
                            <p>
                                <label class = "required">Password: </label>
                                <input 
                                <?php if ($currentUserPasswordERROR) print 'class="mistake"'; ?>
                                    maxlength = "100"
                                    name = "txtPassword"
                                    onfocus = "this.select()"
                                    tabindex = "120"
                                    type = "password"
                                    value = "<?php print $currentUserPassword; ?>"
                                    >
                            </p>
                            <p>
                                <label class = "required">Confirm Password: </label>
                                <input 
                                <?php if ($currentUserRePasswordERROR) print 'class="mistake"'; ?>
                                    maxlength = "100"
                                    name = "txtRePassword"
                                    onfocus = "this.select()"
                                    tabindex = "120"
                                    type = "password"
                                    value = "<?php print $currentUserRePassword; ?>"
                                    >
                            </p>
                        <p>
                            <label class = "required">First Name: </label>
                            <input 
                            <?php if ($currentFirstNameERROR) print 'class="mistake"'; ?>
                                maxlength = "100"
                                name = "txtFirstName"
                                onfocus = "this.select()"
                                tabindex = "120"
                                type = "text"
                                value = "<?php print $currentFirstName; ?>"
                                >
                        </p>
                        <p>
                            <label class = "required">Last Name: </label>
                            <input 
                            <?php if ($currentLastNameERROR) print 'class="mistake"'; ?>
                                maxlength = "100"
                                name = "txtLastName"
                                onfocus = "this.select()"
                                tabindex = "120"
                                type = "text"
                                value = "<?php print $currentLastName; ?>"
                                >
                        </p>
                        <p>
                            <label class = "required">Phone Number: </label>
                            <input 
                            <?php if ($currentPhoneNumberERROR) print 'class="mistake"'; ?>
                                maxlength = "100"
                                name = "txtPhoneNumber"
                                onfocus = "this.select()"
                                tabindex = "120"
                                type = "text"
                                placeholder="hh:mm:ss"
                                value = "<?php print $currentPhoneNumber; ?>"
                                >
                        </p>
                         <p>
                        <label class = "required" for = "txtEmail">Email</label>
                            <input 
                                   <?php if ($currentEmailERROR) print 'class="mistake"'; ?>
                                   id = "txtEmail"     
                                   maxlength = "45"
                                   name = "txtEmail"
                                   onfocus = "this.select()"
                                   placeholder = "Enter your email address"
                                   tabindex = "120"
                                   type = "text"
                                   value = "<?php print $currentEmail; ?>"
                            >
                    </p>     
                        <p>
                            <label class = "required">Address: </label>
                            <input 
                            <?php if ($currentAddressERROR) print 'class="mistake"'; ?>
                                maxlength = "100"
                                name = "txtAddress"
                                onfocus = "this.select()"
                                tabindex = "120"
                                type = "text"
                                value = "<?php print $currentAddress; ?>"
                                >
                        </p>
                        <p>
                            <label class = "required">City: </label>
                            <input 
                            <?php
                            if ($currentCityERROR)
                                print 'class="mistake"';
                            ?>
                                maxlength = "100"
                                name = "txtCity"
                                onfocus = "this.select()"
                                tabindex = "120"
                                type = "text"
                                value = "<?php print $currentCity; ?>"
                                >
                        </p>
                        <p>
                            <label class = "required">State: </label>
                            <input 
                            <?php
                            if ($currentStateERROR)
                                print 'class="mistake"';
                            ?>
                                maxlength = "100"
                                name = "txtState"
                                onfocus = "this.select()"
                                tabindex = "120"
                                type = "text"
                                value = "<?php print $currentState; ?>"
                                >
                        </p>
                        <p>
                            <label class = "required">Country: </label>
                            <?php
                            print '<select id="lstCountry" ';
                                print '        name="lstCountry"';
                                print '        tabindex="520" >';
                            if (is_array($countrycodes)) {
                                    foreach ($countrycodes as $countrycode) {

                                        print '<option ';
                                        if ($currentCountry == $countrycode["fnkCountryCode"])
                                            print "selected = 'selected'";

                                        print ' value="' . $countrycode["fnkCountryCode"]  .'">'.$countrycode["fnkCountryName"];
                                    print '</option>';  
                                }

                                print '</select>';
                                }
                                ?>
                        </p>
                        <p>
                            <label class = "required">Zip Code: </label>
                            <input 
                            <?php
                            if ($currentZipNumberERROR)
                                print 'class="mistake"';
                            ?>
                                maxlength = "100"
                                name = "txtZipCode"
                                onfocus = "this.select()"
                                tabindex = "120"
                                type = "text"
                                value = "<?php print $currentZipNumber; ?>"
                                >
                        </p>
                    </fieldset>
                </fieldset> <!-- ends contact -->
                <fieldset class="buttons">
                    <legend>By submit this form, you agree to share your information with us.</legend>
                    <input class = "button" id = "btninfoSubmit" name = "btninfoSubmit" tabindex = "900" type = "submit" value = "Submit" >
                </fieldset> <!-- ends buttons -->
            </form>     
            <?php
        } // ends body submit
        ?>
    </article>     
</main>     
<?php
include 'footer.php';
?>
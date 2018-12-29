<?php
include 'top.php';
print PHP_EOL . '<!-- SECTION: 1 Initialize variables -->' . PHP_EOL;
$update = false;
$target_dir = "/users/z/s/zshan/www-root/cs148/dev-final/images/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
print PHP_EOL . '<!-- SECTION: 1a. debugging setup -->' . PHP_EOL;
//debug the post value
if (DEBUG) {
    print '<p>Post Array:</p><pre>';
    print_r($_POST);
    print '</pre>';
}
print PHP_EOL . '<!-- SECTION: 1b form variables -->' . PHP_EOL;
//initialize the variable and give a default value to the pmlTrailsId
//The -1 is used to check is there is pmkTrailsID passed here since the pmk cannot be -1
$pmkProductID = -1;
$currentProductName = '';
$currentProductPrice = '';
$currentDescription = '';
$currentProductImage = '';
$currentCategory = '';
$currentActive = 0;
$currentEmail = "";
//get the id use the get method
if (isset($_GET["id"])) {
    //convert the id into a int html entity so that it can be used in form
    $pmkProductID = (int) htmlentities($_GET["id"], ENT_QUOTES, "UTF-8");
    //if it is a update request, need to intialize the values from the tbltrails
    $query = 'SELECT `fldProductName`, `fldProductPrice`, `fldDescription`, `fldProductImage`, `pfkCata`, `pfkActive` FROM `tblProducts` WHERE `pmkProductID`=?';
    //store the pmk trails into an array
    $data = array($pmkProductID);

    if ($thisDatabaseReader->querySecurityOk($query, 1)) {
        $query = $thisDatabaseReader->sanitizeQuery($query);
        $products = $thisDatabaseReader->select($query, $data);
    }
    //santize the product if this is an updatem get all the varilable name with corresponing pmk product
    $currentProductName = $products[0]["fldProductName"];
    $currentProductPrice = $products[0]["fldProductPrice"];
    $currentDescription = $products[0]["fldDescription"];
    $currentProductImage = $products[0]["fldProductImage"];
    $currentCategory = $products[0]["pfkCata"];
    $currentActive = $products[0]["pfkActive"];
    
}

//load tags and catagories from database
$query2 = 'SELECT `pmkCata`, `fldCata` FROM `tblCata`';
if ($thisDatabaseReader->querySecurityOk($query2, 0)) {
    $query2 = $thisDatabaseReader->sanitizeQuery($query2);
    $Catas = $thisDatabaseReader->select($query2, '');
}

$tag = "
SELECT `pmkTag` , `fldDefaultValue`
FROM `tblTags`";

if ($thisDatabaseReader->querySecurityOk($tag, 0)) {
    $tag = $thisDatabaseReader->sanitizeQuery($tag);
    $tagdisplays = $thisDatabaseReader->select($tag, '');
}

if (DEBUG) {
    print '<p>Contents of tag list<pre>';
    print_r($tagdisplays);
    print '</pre></p>';
}

//if this is update, check all the checked tags
if (isset($_GET["id"])) {
    $checks = "SELECT `pfkTag` FROM `tblProductTags` WHERE `pfkProductID`= ?";
    $data = array($pmkProductID);
    if ($thisDatabaseReader->querySecurityOk($checks, 1)) {
        $checks = $thisDatabaseReader->sanitizeQuery($checks);
        $checkreloads = $thisDatabaseReader->select($checks, $data);
    }
    if (DEBUG) {
        print '<p>Contents of the chosen array in db<pre>';
        print_r($data);
        print_r($checkreloads);
        print '</pre></p>';
    }
    foreach ($checkreloads as $checkreload) {
        foreach ($tagdisplays as &$tagdisplay) {
            if ($checkreload["pfkTag"] == $tagdisplay["pmkTag"]) {
                $tagdisplay["fldDefaultValue"] = 1;
            }
        }
    }
    unset($tagdisplay);


    if (DEBUG) {
        print '<p>Contents of the array after load from db<pre>';
        print_r($tagdisplays);
        print '</pre></p>';
    }
}
//%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1c form error flags -->' . PHP_EOL;
//
// Initialize Error Flags one for each form element we validate
// in the order they appear on the form
$currentProductNameERROR = false;
$currentProductPriceERROR = false;
$currentDescriptionERROR = false;
$currentProductImageERROR = false;
$currentCategoryERROR = false;
$currentActiveERROR = false;
$currentEmailERROR = false;
////%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%^%
//
print PHP_EOL . '<!-- SECTION: 1d misc variables -->' . PHP_EOL;
//
// create array to hold error messages filled (if any) in 2d displayed in 3c.
$errorMsg = array();
//initaized the tags from tbltags and stored in th tags variables
print PHP_EOL . '<!-- SECTION: 2 Process for when the form is submitted -->' . PHP_EOL;
//when user pressed submit
if (isset($_POST["btnProductInfoSubmit"])) {
    print PHP_EOL . '<!-- SECTION: 2a Security -->' . PHP_EOL;

    //check if the URL match
    $thisURL = DOMAIN . $phpSelf;

    if (!securityCheck($thisURL)) {
        $msg = '<p>Sorry you cannot access this page.</p>';
        $msg.= '<p>Security breach detected and reported.</p>';
        die($msg);
    }

    print PHP_EOL . '<!-- SECTION: 2b Sanitize (clean) data  -->' . PHP_EOL;
    //sanitize the pmkId
    $pmkProductID = (int) htmlentities($_POST["hidProductID"], ENT_QUOTES, "UTF-8");
    //while the pmkId's default value is not equal to -1, meaning this is an update
    if ($pmkProductID > 0) {
        $update = true;
    }

    //sanitize all the other variable used for textbox.   
    $currentProductName = htmlentities($_POST["txtProductName"], ENT_QUOTES, "UTF-8");
    $currentProductPrice = htmlentities($_POST["txtProductPrice"], ENT_QUOTES, "UTF-8");
    $currentDescription = htmlentities($_POST["txtDescription"], ENT_QUOTES, "UTF-8");
    $currentProductImage = htmlentities(pathinfo($_FILES['fileToUpload']['name'], PATHINFO_FILENAME), ENT_QUOTES, "UTF-8");
    $currentCategory = htmlentities($_POST["radCategories"], ENT_QUOTES, "UTF-8");
    $currentActive = htmlentities($_POST["radactive"], ENT_QUOTES, "UTF-8");
    $currentEmail = filter_var($_POST["txtEmail"], FILTER_SANITIZE_EMAIL);
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);


    //store checked tags from POST array to a new array
    foreach ($_POST as $htmlName => $value) {
        $camelCase = str_split($htmlName, 3);
        if ($camelCase[0] == "chk") {
            $currentProductTag[] = htmlentities($_POST[$htmlName], ENT_QUOTES, "UTF-8");
        }
    }
    //recheck the checkboxes from the array just created if the form has error
    foreach ($currentProductTag as $correct) {
        foreach ($tagdisplays as &$tagdisplay) {
            if ($tagdisplay["pmkTag"] == $correct) {
                $tagdisplay["fldDefaultValue"] = 1;
            }
        }
    }
    unset($tagdisplay);
    if (DEBUG) {
        print '<p>Contents of the currentTrail <pre>';
        print_r($currentProductTag);
        print '</pre></p>';
    }
    print PHP_EOL . '<!-- SECTION: 2c Validation -->' . PHP_EOL;

    if ($currentProductName == "") {
        $errorMsg[] = "Please Enter the name!";
        $currentProductNameERROR = true;
    }

    if ($currentProductPrice == "") {
        $errorMsg[] = "Please Enter the price!";
        $currentProductPriceERROR = true;
    } elseif (!verifyNumeric($currentProductPrice)) {
        $errorMsg[] = "Price can only be numbers!";
        $currentProductPriceERROR = true;
    }

    if ($currentDescription == "") {
        $errorMsg[] = "Please Enter the description!";
        $currentDescriptionERROR = true;
    }
    if ($check === false) {
        $errorMsg[] = "File is not an image!";
        $currentProductImageERROR = true;
    }


    if ($currentActive != 0 AND $currentActive != 1) {
        $errorMsg[] = "Please choose a status!";
        $currentActiveERROR = true;
    }
    if ($currentEmail == "") {
        $errorMsg[] = 'Please enter your email address';
        $currentEmailERROR = true;
    } elseif (!verifyEmail($currentEmail)) {
        $errorMsg[] = 'Your email address appears to be incorrect.';
        $currentEmailERROR = true;
    }


// Check file size
    if ($_FILES["fileToUpload"]["size"] > 500000) {
        $errorMsg[] = "Your file is too large!";
        $currentProductImageERROR = true;
    }
// only allow jpg here
    if ($imageFileType != "jpg") {
        $errorMsg[] = "Only JPG is allowed!";
        $currentProductImageERROR = true;
    }

    if ($currentCategory == "") {
        $errorMsg[] = "No category selected! Please choose category!";
        $currentCategoryERROR = true;
    } elseif (isset($_REQUEST['lstCategories']) && $_REQUEST['lstCategories'] == '0') {
        $errorMsg[] = 'The category appears to be incorrect!';
        $currentCategoryERROR = true;
    }

    foreach ($currentProductTag as $correct) {
        if (!in_array($correct, array_column($tagdisplays, 'pmkTag'))) {
            if (DEBUG) {
                print '<p>validate:</p><pre>';
                print_r($correct);
                print_r($tagdisplays[0]['pmkTag']);
                print '</pre>';
            }
            $errorMsg[] = "The tag you choose seems not correct!";
            $currentProductTagERROR = true;
        }
    }
    unset($tagdisplay);



    print PHP_EOL . '<!-- SECTION: 2d Process Form - Passed Validation -->' . PHP_EOL;
    //check if the form pass the validation
    if (!$errorMsg) {
        if (DEBUG)
            print '<p>Form is valid</p>';

        print PHP_EOL . '<!-- SECTION: 2e Save Data -->' . PHP_EOL;
        //save the data if it is an update or add
        $dataEntered = false;
        //store the valus into an array
        $dataRecord = array();
        $data1 = array();
        $dataRecord[] = $currentProductName;
        $dataRecord[] = $currentProductImage;
        $dataRecord[] = $currentProductPrice;
        $dataRecord[] = $currentDescription;
        $dataRecord[] = $currentCategory;
        $dataRecord[] = $currentActive;
        //debug the array
        if (DEBUG) {
            print '<p>Post Array:</p><pre>';
            print_r($data);
            print '</pre>';
        }
        //if this is an update and user press submit
        //give defalut value for the delete transaction 
        // all sql statements are done so lets commit to our changes
        //the transcation for inserting data for adding and updating
        if ($update) {
            try {
                $thisDatabaseWriter->db->beginTransaction();
                $query = "UPDATE `tblProducts` SET `fldProductName`=?,`fldProductImage`=?,`fldProductPrice`=?,`fldDescription`=?,`pfkCata`=?, `pfkActive`=? WHERE `pmkProductID`=?";
                $dataRecord[] = $pmkProductID;
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 1);
                    print_r($dataRecord);
                }
                if ($thisDatabaseReader->querySecurityOk($query, 1)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->update($query, $dataRecord);
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                if (DEBUG)
                    print "<p>transaction complete ";
                $thisDatabaseWriter->db->beginTransaction();
                $query2 = "DELETE FROM `tblProductTags` WHERE `pfkProductID` =?";
                $data1[] = $pmkProductID;
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query2, 1);
                    print_r($data1);
                }
                if ($thisDatabaseReader->querySecurityOk($query2, 1)) {
                    $query2 = $thisDatabaseWriter->sanitizeQuery($query2);
                    $results2 = $thisDatabaseWriter->delete($query2, $data1);
                }
                $dataDeleted = $thisDatabaseWriter->db->commit();
                $thisDatabaseWriter->db->beginTransaction();
                for ($x = 0; $x < sizeof($currentProductTag); $x++) {
                    $dataRecord2 = array();
                    $dataRecord2[] = $pmkProductID;
                    $dataRecord2[] = $currentProductTag[$x];
                    $query = "INSERT INTO `tblProductTags`(`pfkProductID`, `pfkTag`) VALUES (?,?)";
                    if (DEBUG) {
                        $thisDatabaseWriter->TestSecurityQuery($query, 0);
                        print_r($dataRecord2);
                    }

                    if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                        $query = $thisDatabaseWriter->sanitizeQuery($query);

                        $results = $thisDatabaseWriter->insert($query, $dataRecord2);
                    }
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                if (DEBUG)
                    print "<p>transaction complete ";
            } catch (PDOExecption $e) {
                //other errors for transcation
                $thisDatabase->db->rollback();
                if (DEBUG)
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
            }
        } else {
            try {
                $thisDatabaseWriter->db->beginTransaction();
                $query = "INSERT INTO `tblProducts`(`fldProductName`, `fldProductImage`, `fldProductPrice`, `fldDescription`, `pfkCata`, `pfkActive`) VALUES (?,?,?,?,?,?)";
                if (DEBUG) {
                    $thisDatabaseWriter->TestSecurityQuery($query, 0);
                    print_r($dataRecord);
                }
                if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                    $query = $thisDatabaseWriter->sanitizeQuery($query);
                    $results = $thisDatabaseWriter->insert($query, $dataRecord);
                    $primaryKey = $thisDatabaseWriter->lastInsert();
                    $pmkProductID = $primaryKey;
                    if (DEBUG) {
                        print "<p>pmk= " . $primaryKey;
                    }
                }
                if (!empty($currentProductTag)) {
                    for ($x = 0; $x < sizeof($currentProductTag); $x++) {
                        $dataRecord2 = array();
                        $dataRecord2[] = $primaryKey;
                        $dataRecord2[] = $$currentProductTag[$x];
                        $query = "INSERT INTO `tblProductTags`(`pfkProductID`, `pfkTag`) VALUES (?,?)";
                        if (DEBUG) {
                            $thisDatabaseWriter->TestSecurityQuery($query, 0);
                            print_r($dataRecord2);
                        }

                        if ($thisDatabaseWriter->querySecurityOk($query, 0)) {
                            $query = $thisDatabaseWriter->sanitizeQuery($query);

                            $results = $thisDatabaseWriter->insert($query, $dataRecord2);
                        }
                    }
                }
                $dataEntered = $thisDatabaseWriter->db->commit();
                if (DEBUG)
                    print "<p>transaction complete ";
            } catch (PDOExecption $e) {
                //other errors for transcation
                $thisDatabase->db->rollback();
                if (DEBUG)
                    print "Error!: " . $e->getMessage() . "</br>";
                $errorMsg[] = "There was a problem with accepting your data please contact us directly.";
            }
        }

        // all sql statements are done so lets commit to our changes


        print PHP_EOL . '<!-- SECTION: 2f Create message -->' . PHP_EOL;
        $message = '<h3>Submission Receipt:</h3>';
        if (file_exists($target_file)) {
            unlink($target_file);
        }
        if ($currentCategoryERROR == true) {
            $errorMsg[] = "Sorry, your file was not uploaded.";
        } else {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $message .= "<p class='success'>The file " . basename($_FILES["fileToUpload"]["name"]) . " has been uploaded.</p>";
            } else {
                $errorMsg[] = "Sorry, there was an error uploading your file.";
            }
        }



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
        $subject = 'Product information submitted successfully!';
        $mailed = sendMail($to, $cc, $bcc, $from, $subject, $message);
    }
}
print PHP_EOL . '<!-- SECTION 3 Display Form -->' . PHP_EOL;
?>

<main>     
    <article>
        <?php
        print PHP_EOL . '<!-- SECTION 3a  -->' . PHP_EOL;
        if (isset($_POST["btnProductInfoSubmit"]) AND empty($errorMsg)) {
            if (isset($_GET["id"])) {
                print '<h2>Edit submitted successfully! </h2>';
            } else {
                print '<h2>New Product submitted successfully!</h2>';
            }
            print '<fieldset class = "yescss">';
            print $message;
            print '</fieldset>';
        } else {
            if (isset($_GET["id"])) {
                print '<h2>Please edit product!</h2>';
            } else {
                print '<h2>Please enter new product!</h2>';
            }

            print PHP_EOL . '<!-- SECTION 3b Error Messages -->' . PHP_EOL;
            //display the error message
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

            print PHP_EOL . '<!-- SECTION 3c html Form -->' . PHP_EOL;
            //only the admin can view the form
            //prvent people from bypass and enter the from url directly
            if ($isAdmin) {
//form for five textboxs, add input type hidden if this is an update, 
//also including error function to check input validation
                ?>   

                <form action="<?php print $phpSelf; ?>"
                      method="post"
                      id="frmRegister"
                      enctype="multipart/form-data">
                    <input type="hidden" id="hidProductID" name="hidProductID"
                           value="<?php print $pmkProductID; ?>"
                           >
                    <fieldset class = "contact">
                        <p>
                            <label for="txtProductName" class="required">Product Name
                                <input type="text" id="txtProductName" name="txtProductName"
                                       value="<?php print $currentProductName; ?>"
                                       tabindex="100" maxlength="45" placeholder="Enter the product name"
                                       <?php if ($currentProductNameERROR) print 'class="mistake"'; ?>
                                       onfocus="this.select()"
                                       autofocus>
                            </label>
                        </p>

                        <p>
                            <label for="txtProductPrice" class="required">Product Price
                                <input type="text" id="txtProductPrice" name="txtProductPrice"
                                       value="<?php print $currentProductPrice; ?>"
                                       tabindex="100" maxlength="45" placeholder="Enter the product price"
                                       <?php if ($currentProductPriceERROR) print 'class="mistake"'; ?>
                                       onfocus="this.select()"
                                       >
                            </label>
                        </p>

                        <p>
                            <label for="txtDescription" class="required">Description
                                <textarea type="text" id="txtDescription" name="txtDescription"
                                          tabindex="100" maxlength="45" placeholder="Enter the description"
                                          <?php if ($currentDescriptionERROR) print 'class="mistake"'; ?>
                                          onfocus="this.select()"
                                          ><?php print $currentDescription; ?></textarea>
                            </label> 
                        </p>

                        <p>
                            <label for="txtProductImage" class="required">Product Image
                                <input type="file" name="fileToUpload" id="fileToUpload">
                            </label> 
                        </p>
                        <p>
                            <?php
                            print '<label for="radCategories"';
                            if ($currentCategoryERROR) {
                                print ' class = "mistake"';
                            }
                            print '> Category: ';

                            if (is_array($Catas)) {
                                foreach ($Catas as $Cata) {
                                    print '<input type="radio" name = "radCategories" ';
                                    if ($currentCategory == $Cata["pmkCata"]) {
                                        print "checked = 'checked'";
                                    }
                                    print 'value="' . $Cata["pmkCata"] . '">';
                                    print '<label class = radiobtm>' . $Cata["fldCata"] . '</label>';
                                }
                            }
                            ?>
                        </p>
                        <p>
                            <label class = "required"
                            <?php
                            if ($currentProductTagERROR) {
                                print ' class = "mistake"';
                            }
                            ?>
                                   >Tag: </label>
                                   <?php
                                   if (is_array($tagdisplays)) {
                                       foreach ($tagdisplays as $tagdisplay) {
                                           print '<input type="checkbox" ';
                                           if ($tagdisplay["fldDefaultValue"]) {
                                               print " checked ";
                                           }
                                           print 'name = "chkTag' . str_replace(" ", "", $tagdisplay["pmkTag"]) . '" id = "chkTag' . str_replace(" ", "", $tagdisplay["pmkTag"]) . '" ';
                                           print 'value="' . $tagdisplay["pmkTag"] . '">';
                                           print '<label class = checkbox for=chkTag' . str_replace(" ", "", $tagdisplay["pmkTag"]) . '>' . $tagdisplay["pmkTag"] . '</label>';
                                       }
                                   }
                                   ?>
                        </p>
                        <p>
                            <label class = "required"
                            <?php
                            if ($currentActiveERROR) {
                                print ' class = "mistake"';
                            }
                            ?>
                                   >Status: </label>
                                   <?php
                                   print '<input type="radio" name = "radactive" ';


                                   if ($currentActive == 0)
                                       print "checked = 'checked'";

                                   print ' value="0">';
                                   print '<label class = radiobtm>Inactive</label>';

                                   print '<input type="radio" name = "radactive" ';


                                   if ($currentActive == 1)
                                       print "checked = 'checked'";

                                   print ' value="1">';
                                   print '<label class = radiobtm>Active</label>';
                                   ?>


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
                    </fieldset>
                    <fieldset class="buttons">

                        <input type="submit" id="btnProductInfoSubmit" name="btnProductInfoSubmit" value="Submit" tabindex="900" class="button">
                    </fieldset> <!-- ends buttons -->
                    </form> 
                    <?php
                } else {
                    //display the error message if someone tring to view the from who is not admin.
                    print '<h2>You have no authority to view or edit this form!</h2>';
                }
                ?>  
              
            <?php
        }
        ?>

    </article>     
</main>     

<?php include 'footer.php'; ?>
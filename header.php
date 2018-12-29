<!-- %%%%%%%%%%%%%%%%%%%%%%     Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
<!-- A form that allow user to type in key word and search-->
<header>
    <a href="index.php"><img src="images/logo.jpg" alt="website icon"></a>
    
    <form action = "<?php print $phpSelf; ?>"
                      id = "frmSearch"
                      method = "post">
                    <input 
                        maxlength = "100"
                        name = "txtSearch"
                        onfocus = "this.select()"
                        tabindex = "120"
                        type = "text"
                        id="fldSearch"
                        value = "<?php print $currentSearch; ?>"
                        >
                     <input class = "button" id = "btnSubmit" name = "btnSubmit" tabindex = "900" type = "submit" value = "" >
                </form>
    <?php
print '<ol class = "headernav">';
$loadcatalist = "SELECT `pmkCata`, `fldCata` FROM `tblCata`";
    if ($thisDatabaseReader->querySecurityOk($loadcatalist, 0)) {
        $loadcatalist = $thisDatabaseReader->sanitizeQuery($loadcatalist);
        $catalistreloads = $thisDatabaseReader->select($loadcatalist, '');
    }
    if (DEBUG) {
        print '<p>New Items:</p><pre>';
        print_r($catalistreloads);
        print '</pre>';
    }
// display categories
    if (is_array($catalistreloads)) {
        foreach ($catalistreloads as $catalistreload) {
            print '<li ';
            if ($PATH_PARTS['filename'] == 'nav-category') {
                print ' class="activePage" ';
                
            }
            if($isAdmin){
                print '><a href="admin-list.php?cataID='.$catalistreload["pmkCata"].'">'.$catalistreload["fldCata"].'</a></li>';
            }else{
                print '><a href="nav-category.php?pfkcata='.$catalistreload["pmkCata"].'">'.$catalistreload["fldCata"].'</a></li>';
            }
        }
        }
        print '<li ';
        if ($PATH_PARTS['filename'] == 'tables') {
                print ' class="activePage" ';
                
            }
                print '><a href="tables.php">table</a></li>';

?>
</ol>
</header>
<!-- %%%%%%%%%%%%%%%%%%%%% Ends Page header   %%%%%%%%%%%%%%%%%%%%%%%%%%%%%% -->
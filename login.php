<?php
include 'top.php';
//page for user to login, check password and username combination, if correspond go to index page
?>
<main>
    <article>
        <?php if(!$username){?>
        <form action = "<?php print $phpSelf; ?>"
              id = "frmRegister"
              method = "post">
            <p>
                <label class = "required">User Name: </label>
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
                    name = "txtUserPassword"
                    onfocus = "this.select()"
                    tabindex = "120"
                    type = "password"
                    value = "<?php print $currentUserPassword; ?>"
                    >
            </p>
            <input class = "button" id = "btnloginSubmit" name = "btnloginSubmit" tabindex = "900" type = "submit" value = "login" >
        </form>
        <form action="form-user-info.php"><input type="submit" value="signup" /></form>
        <?php 
        if ($error) {
            //if not correspnd then show alert
            print '<script> alert("Incorrect user name or password!")</script>';
            }
        }else{
            //if already log in then don't show the form
            print '<script> 
            alert("You already log in!");
            window.location.href = "index.php";
            </script>';
        }
        ?>
    </article>
</main>
<?php
include 'footer.php';
?>
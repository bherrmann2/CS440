<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        require_once 'UserInterface.php';
        
        if (isset($_POST['resubmit'])){
            $ui = new UserInterface();
            if ($ui->postData() == 0){
                echo "<h2 align=center>An error occurred</h2>";
            }else{
                echo "<h2 align=center>Success</h2>";
            }
        }else{
        ?>
        <h1 align="center">Renew Book</h1>
        <form action="renew.php" method="POST">
            <p align="center">User's name<br><input type="text" name="user" style=width:8em;>
            <p align="center">Admin's name<input type="text" name="admin" style=width:8em;>
            Admin's password<input type="password" name="pass" style=width:8em;>
            <p align="center">ISBN<br><input type="text" name="isbn" style=width:8em;>
            <p align="center"><input type="submit" name="resubmit" value="Enter">
        </form>
    <?php
        }
    ?>
    </body>
    <br><p align="center"><a href="main.php">Return to Main Page</a>
</html>

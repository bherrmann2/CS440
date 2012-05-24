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
	if (isset($_POST['asubmit'])){
            require_once 'UserInterface.php';
            $ui = new UserInterface();
	    $return = $ui->postData();
	    if ($return == 0)
	    {
                echo "<h2 align=center>An error occurred</h2>";
	    }
	    else if($return == -1)
	    {
		    echo "<h2 align=center>Login Failure</h2>";
	    }
	    else
	    {
                echo "<h2 align=center>Success</h2>";
            }
	    //unset($_POST['asubmit']);
        }else{
        ?>
        <div id="top">
            <h1 align="center">Add Book</h1>
        </div>
        <form action="add.php" method="POST">
            <table align="center">
                <tbody>
                    <tr>
                        <td>Admin's username</td><td><input type="text" name="username" style=width:8em;></td>
                        <td>Password</td><td><input type="password" name="password" style=width:8em;></td>
                        <td>ISBN</td><td><input type="text" name="isbn" style=width:8em;></td>
                        <td><input type="submit" name="asubmit" value="Enter"></td>
                    </tr>
                </tbody>
            </table>
        </form>
        <?php
        }
        ?>
        <br><p align="center"><a href="main.php">Return to Main Page</a>
    </body>
</html>

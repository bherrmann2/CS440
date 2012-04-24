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
        <div id="top">
            <h1 align="center">Add Book</h1>
        </div>
        <form action="UserInterface.php" method="POST">
            <table align="center">
                <tbody>
                    <tr>
                        <td>Username</td><td><input type="text" name="username" style=width:8em;></td>
                        <td>Password</td><td><input type="password" name="password" style=width:8em;></td>
                        <td>ISBN</td><td><input type="text" name="isbn" style=width:8em;></td>
                        <td><input type="submit" name="asubmit" value="Enter"></td>
                    </tr>
                </tbody>
            </table>
        </form>
        <?php
        // put your code here
        ?>
    </body>
</html>

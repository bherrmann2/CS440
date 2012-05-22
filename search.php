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
            <h1 align="center">Search</h1>
        </div>
        
        <?php
            ini_set("error_reporting", E_ALL);
            require_once('UserInterface.php');
            if (isset($_POST['ksubmit']) || isset($_POST['isubmit'])){
                $ui = new UserInterface();
                $books = $ui->postData();
                if (empty($books)){
                    echo "<p align=center>No Results Found</p>";
                }else{
                    foreach($books as $book){
                        $isbn = $book->getISBN();
                        $title = $book->getName();
                        $authors = $book->getAuthor()->getAuthor();
                        $author = $authors[0];
                        echo "<p align=center><a href=\"view.php?isbn=$isbn\">\"$title\" by: $author</a></p>";
                    }
                }
            }else{
        ?>
            <form action="search.php" method="POST">
                <p align="center">
                    ISBN:<br><input type="text" name="isbn" autofocus="autofocus" style=width:8em;>
                    <br><input type="submit" name="isubmit" value="Enter">
                </p>
            </form>
                    <br>
                    <p align="center">Or</p>
                    <br>
            <form action="search.php" method="POST">  
                <p align="center">
                    Title:<br><input type="text" name="title" style=width:8em;>
                    <br>Author:<br><input type="text" name="author" style=width:8em;>
                    <br>Keywords:<br><input type="text" name="keywords" style=width:8em;>
                    <br><input type="submit" name="ksubmit" value="Enter">
                </p>
            </form>
        <?php
            }
        // put your code here
        ?>
    </body>
    <br><p align="center"><a href="main.php">Return to Main Page</a>
</html>

<!--
To change this template, choose Tools | Templates
and open the template in the editor.
-->
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
        <style>
	td
	{
	    border: solid windowtext 1.0pt;
	    padding: 4pt 4pt 4pt 4pt;
	}

	table
	{
	    font-size:8.5pt;
	    font-family: Tahoma;
	}
	</style>
        <title></title>
    </head>
    <body>
        <?php
        
            if (isset($_POST['submit'])){
                $isbn = $_POST['isbn'];
                echo "<h1 align=\"center\">Please Login</h1>";
                if ($_POST['submit'] == "Remove"){ //show the fields for removal
                    echo <<<_END
                    <form action="view.php" method="POST">
                        <input type="hidden" name=isbn value=$isbn>
                        <input type="hidden" name=type value="Remove">
                        <p align=center>Admin's name<input type="text" name="admin">
                        Password<input type="password" name="pass"></p>
                        <p align=center><input type="submit" name="rsubmit" value="Submit">
                    </form>
_END;
                }else{ //show the fields for checking in/out
                    echo <<<_END
                    <form action="view.php" method="POST">
                        <input type="hidden" name=isbn value=$isbn>
                        <input type="hidden" name=type value="{$_POST['submit']}">
                        <p align=center>Admin's name<input type="text" name="admin">
                        Password<input type="password" name="pass"></p>
                        <p align=center>User's name<input type="text" name="user"></p>
                        <p align=center><input type="submit" name="csubmit" value="Submit">
                    </form>
_END;
                }
            }else if(isset($_POST['csubmit']) || isset($_POST['rsubmit'])){
                require_once 'UserInterface.php';
                $ui = new UserInterface();
                if($ui->postData() == 0){
                    echo "<h2 align=center>An error occurred</h2>";
                }else{
                    echo "<h2 align=center>Success</h2>";
                }
            }else{ //display the book
                require_once 'UserInterface.php';
                $ui = new UserInterface();
                echo "<h1 align=\"center\">View Book</h1>";
                if (isset($_POST['esubmit'])){ //edit the book then display it
                    if($ui->postData() == 0){
                        echo "<h2 align=center>An error occurred</h2>";
                        exit();
                    }else{
                        $_GET['isbn'] = $_POST['isbn'];
                    }
                }else{
                    //if (empty($_GET['isbn'])){
                        //echo "<p align=center>Nothing to display</p>";
                        //exit();
                    //}
                }
                $book = $ui->getData();
                $isbn = $_GET['isbn'];
                $name = $book->getName();
                $authors = $book->getAuthor()->getAuthor();
                $publishers = $book->getPublisher()->getPublisher();
                $pdate = $book->getPublisher()->getPublishDate();
                $pcount = $book->getPCount();
                $description = $book->getDescription();
                $quantity = $book->getQuantity();
                $avail = $book->getNumAvailable();

                echo <<<_END
                    <table align=center cellspacing=0 cellpadding=0 border=1 style="border: solid windowtext 1.0pt">
                        <tr>
                            <td><b>ISBN</td><td><b>Quantity</td><td><b>Availabe</td>
                        </tr>
                            <td>$isbn</td>
                            <td>$quantity</td>
                            <td>$avail</td>
                        </tr>
                        <tr>
                            <td><b>Title</td><td><b>Pages</td><td></td>
                        </tr>
                        <tr>
                            <td>$name</td><td>$pcount</td><td></td>
                        </tr>
                        <tr>
                            <td><b>Authors</td><td></td><td></td></tr><tr>
_END;
                for($i=0; $i<3; $i++){
                    if ($i < count($authors)){
                        echo "<td>{$authors[$i]}</td>";
                    }else{
                        echo "<td></td>";
                    }
                }

                echo "</tr><tr><td><b>Publishers</td><td></td><td></td></tr><tr>";

                for($i=0; $i<3; $i++){
                    if ($i < count($publishers)){
                        echo "<td>{$publishers[$i]}</td>";
                    }else{
                        echo "<td></td>";
                    }
                }
                echo "</tr><tr><td><b>Publish Date</td><td></td><td></td></tr><tr><td>$pdate</td><td></td><td></td></tr>";
                echo "<tr><td><b>Description</td><td></td><td></td></tr><tr><td colspan=3><textarea readonly=\"readonly\" style=\"resize: none;\" cols=50 rows=5>$description</textarea></td></tr>";
                echo "</table>";

                echo <<<_END
                        <form action="edit.php" method="GET">
                            <input type="hidden" name=isbn value=$isbn>
                            <p align=center><input type="submit" name="submit" value="Edit"></p>
                        </form>
                        <form action="view.php" method="POST">
                            <input type="hidden" name=isbn value=$isbn>
                            <p align=center>
                            <input type="submit" name="submit" value="Checkout">
                            <input type="submit" name="submit" value="Check In">
                            <input type="submit" name="submit" value="Remove">
                            </p>
                        </form>
_END;
            }
        ?>
        
    </body>
</html>

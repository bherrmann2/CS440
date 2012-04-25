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
        <h1 align="center">Edit Book</h1>
        <?php
        require_once 'UserInterface.php';
        if (empty($_GET['isbn'])){
            echo "<p align=center>Nothing to display</p>";
            exit();
        }
        $ui = new UserInterface();
        $book = $ui->getData();
        $isbn = $_GET['isbn'];
        $name = $book->getName();
        $authors = $book->getAuthor()->getAuthor();
        $publishers = $book->getPublisher()->getPublisher();
        $pdate = $book->getPublisher()->getPublishDate();
        $pcount = $book->getPCount();
        $description = $book->getDescription();
        
        echo <<<_END
            <form action="view.php" method="POST">
            <table align=center cellspacing=0 cellpadding=0 border=1 style="border: solid windowtext 1.0pt">
                <tr>
                    <td><b>Title</td><td><b>Pages</td><td></td>
                </tr>
                <tr>
                    <td><input type="text" name="name" value="$name"></td><td><input type="text" name="pcount" value="$pcount"></td><td></td>
                </tr>
                <tr>
                    <td><b>Authors</td><td></td><td></td></tr><tr>
_END;
        for($i=0; $i<3; $i++){
            if ($i < count($authors)){
                echo "<td><input type=\"text\" name=\"author$i\" value=\"{$authors[$i]}\"></td>";
            }else{
                echo "<td><input type=\"text\" name=\"author$i\"></td>";
            }
        }

        echo "</tr><tr><td><b>Publishers</td><td></td><td></td></tr><tr>";

        for($i=0; $i<3; $i++){
            if ($i < count($publishers)){
                echo "<td><input type=\"text\" name=\"publisher$i\" value=\"{$publishers[$i]}\"></td>";
            }else{
                echo "<td><input type=\"text\" name=\"publisher$i\"></td>";
            }
        }
        echo "</tr><tr><td><b>Publish Date</td><td></td><td></td></tr><tr><td><input type=\"text\" name=\"pdate\" value=\"$pdate\"></td><td></td><td></td></tr>";
        echo "<tr><td><b>Description</td><td></td><td></td></tr><tr><td colspan=3><textarea name=\"desc\" style=\"resize: none;\" cols=50 rows=5>$description</textarea></td></tr>";
        echo "</table>";
        echo "<input type=hidden name=isbn value=$isbn>";
        echo "<p align=\"center\">Admin's name<input type=\"text\" name=\"admin\" style=width:8em;>Admin's password<input type=\"password\" name=\"pass\" style=width:8em;>";
        echo "<p align=center><input type=\"submit\" name=\"esubmit\" value=\"Submit\"></p></form>";
        ?>
    </body>
</html>

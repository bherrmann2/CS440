<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UserInterface
 *
 * @author Brad
 */
require_once 'OperationsController.php';

class UserInterface {
    protected $ops;
    
    public function __construct() {
        $this->ops = new OperationsController();
    }
    
    //re-write all of this shit to use functions instead. only did it this way
    //due to an oversight on the design doc and didn't want to lose points
    public function postData(){
        if (isset($_POST['isubmit'])){
            $isbn = $_POST['isbn'];
            $books = $this->ops->search($isbn, "", "", "");
            echo "<html><body><h1 align=center>Results</h1>";
            foreach($books as $book){
                
                echo <<<_END
                    <p align="center"><a href="view.php?isbn" 
            
_END;
            }
        }else if(isset($_POST['ksubmit'])){ //keywords search
            $keywords = $_POST['keywords'];
            $author = $_POST['author'];
            $title = $_POST['title'];
            return $this->ops->search("", $author, $title, $keywords);
        }else if(isset($_POST['asubmit'])){ //add book
            $username = $_POST['username'];
            $password = $_POST['password'];
            $isbn = $_POST['isbn'];
            if ($this->ops->addBook($isbn, $username, $password) == 0){
                //error
            }
        }else if(isset($_POST['rsubmit'])){ //remove book
            $admin = $_POST['admin'];
            $pass = $_POST['pass'];
            $isbn = $_POST['isbn'];
            if ($this->ops->removeBook($isbn, $admin, $pass)==0){
                return "An error occured";
            }else{
                return "Success";
            }
        }else if(isset($_POST['csubmit'])){ //checkout/checkin
            if ($_POST['csubmit'] == "Checkout"){
                $isbn = $_POST['isbn'];
                $admin = $_POST['admin'];
                $pass = $_POST['pass'];
                $user = $_POST['user'];
                if ($this->ops->checkoutBook($isbn, $user, $admin, $pass)==0){
                    return "An error occured";
                }else{
                    return "Success";
                }
            }else{
                $isbn = $_POST['isbn'];
                $admin = $_POST['admin'];
                $pass = $_POST['pass'];
                $user = $_POST['user'];
                if ($this->ops->returnBook($isbn, $user, $admin, $pass)==0){
                    return "An error occured";
                }else{
                    return "Success";
                }
            }
        }else if(isset($_POST['esubmit'])){
            
        }
    }
    
    public function getData(){
        if (isset($_GET['isbn'])){
            $isbn = $_GET['isbn'];
            $books = $this->ops->search($isbn, "", "", "");
            return $books[0];
        }
    }
    
    public function browseData(){
           return $this->ops>search("","","","");
    }
}

?>

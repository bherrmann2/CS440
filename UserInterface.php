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
        if (isset($_POST['isubmit'])){ //isbn search DONE
            if ($_POST['isbn']==""){
                $isbn=-1;
            }else{
                $isbn = $_POST['isbn'];
            }
            return $this->ops->search($isbn, "", "", "");
        }else if(isset($_POST['ksubmit'])){ //keywords search DONE
            $keywords = $_POST['keywords'];
            $author = $_POST['author'];
            $title = $_POST['title'];
            return $this->ops->search("", $author, $title, $keywords);
        }else if(isset($_POST['asubmit'])){ //add book DONE
            $username = $_POST['username'];
            $password = $_POST['password'];
            $isbn = $_POST['isbn'];
            return $this->ops->addBook($isbn, $username, $password);
        }else if(isset($_POST['rsubmit'])){ //remove book DONE
            $admin = $_POST['admin'];
            $pass = $_POST['pass'];
            $isbn = $_POST['isbn'];
            return $this->ops->removeBook($isbn, $admin, $pass);
        }else if(isset($_POST['csubmit'])){ //checkout/checkin DONE
            if ($_POST['type'] == "Checkout"){
                $isbn = $_POST['isbn'];
                $admin = $_POST['admin'];
                $pass = $_POST['pass'];
                $user = $_POST['user'];
                return $this->ops->checkoutBook($isbn, $user, $admin, $pass);
            }else{
                $isbn = $_POST['isbn'];
                $admin = $_POST['admin'];
                $pass = $_POST['pass'];
                $user = $_POST['user'];
                return $this->ops->returnBook($isbn, $user, $admin, $pass);
            }
        }else if(isset($_POST['esubmit'])){ //edit book DONE
            $admin = $_POST['admin'];
            $pass = $_POST['pass'];
            return $this->ops->updateBook($_POST, $admin, $pass);
        }else if(isset($_POST['resubmit'])){ //renew book DONE
            $admin = $_POST['admin'];
            $pass = $_POST['pass'];
            $user = $_POST['user'];
            $isbn = $_POST['isbn'];
            return $this->ops->renew($isbn, $user, $admin, $pass);
        }
    }
    
    public function getData(){
        if (isset($_GET['isbn'])){
            $isbn = $_GET['isbn'];
            $books = $this->ops->search($isbn, "", "", "");
            return $books[0];
        }
    }
}

?>

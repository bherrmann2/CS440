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
    //put your code here
    protected $ops;
    
    public function __construct() {
        $this->ops = new OperationsController();
    }
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
        }else if($_POST['ksubmit']){
            $keywords = $_POST['keywords'];
            $author = $_POST['author'];
            $title = $_POST['title'];
            $return = $this->ops->search("", $author, $title, $keywords);
        }else if($_POST['asubmit']){
            $username = $_POST['username'];
            $password = $_POST['password'];
            $isbn = $_POST['isbn'];
            if ($this->ops->addBook($isbn, $username, $password) == 0){
                //error
            }
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

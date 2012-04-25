<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Author
 *
 * @author Brad
 */
class Author {
    protected $authors = array();
    
    public function getAuthor(){
        return $this->authors;
    }
    
    public function addAuthor($author){
        array_push($this->authors, $author);
        return 1;
    }
    
    public function removeAuthor($author){
        $index = array_search($author, $this->authors);
        if ($index == 0){
            if ($this->authors[0] != $author){
                return 0;
            }
        }
        unset($this->authors[$index]);
        $this->authors = array_merge($this->authors);
        return 1;
        
    }
}

?>

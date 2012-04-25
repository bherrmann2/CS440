<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Publisher
 *
 * @author Brad
 */
class Publisher {
    protected $publishers = array();
    protected $publish_date = 0;
    
    public function addPublisher($publisher){
        array_push($this->publishers, $publisher);
        return 1;
    }
    
    public function removePublisher($publisher){
        $index = 0;
        $index = array_search($publisher, $this->publishers);
        if ($index == 0){
            if ($this->publishers[0] != $publisher){
                return 0;
            }
        }
        unset($this->publishers[$index]);
        $this->publishers = array_merge($this->publishers);
        return 1;
    }
    
    public function getPublisher(){
        return $this->publishers;
    }
    
    public function setPublishDate($date){
        $this->publish_date = $date;
        return 1;
    }
    
    public function getPublishDate(){
        return $this->publish_date;
    }
}

?>

<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // action body
         echo "hello world";
    }
    
    public function testAction(){
        $this->view->testData = "balls";
    }

}


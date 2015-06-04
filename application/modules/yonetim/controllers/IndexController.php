<?php

class Yonetim_IndexController extends Admin_Action
{

    public function init()
    {
        parent::init();
        parent::initScripts();
        $this->view->headTitle()->prepend('Dashboard');
    }

    public function indexAction()
    {

    }

    public function testAction()
    {

    }


}




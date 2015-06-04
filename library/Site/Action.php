<?php

class Site_Action extends Zend_Controller_Action
{
    public function init()
    {
        parent::init();

        if(Site_Helper::getSes('siteHata')){
            $this->view->messages = array('err'=>1, 'msg'=>Site_Helper::getSes('siteHata'));
            Site_Helper::setSes('siteHata',null);
        }else if(Site_Helper::getSes('siteBilgi')){
            $this->view->messages = array('err'=>0 , 'msg'=>Site_Helper::getSes('siteBilgi'));
            Site_Helper::setSes('siteBilgi',null);
        }

        $this->view->headTitle(PANEL_ADI) ->setSeparator(' - ')->setAutoEscape(false);

        $this->view->headScript()->appendFile(
            '/js/prototype.js',
            'text/javascript',
            array('conditional' => 'lt IE 7')
        );

    }

    public function initScripts(){

        $this->view->headLink()->setStylesheet('/admin/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/font-icons/entypo/css/entypo.css');
        $this->view->headLink()->appendStylesheet('http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/bootstrap.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-core.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-theme.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-forms.css');

    }

    public function preDispatch() {


    }
}
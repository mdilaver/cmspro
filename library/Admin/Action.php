<?php

class Admin_Action extends Zend_Controller_Action
{
    public function init()
    {
        parent::init();

        $this->_helper->layout->setLayout('admin');

        if(Site_Helper::getSes('hata')){
            $this->view->messages = array('err'=>1, 'msg'=>Site_Helper::getSes('hata'));
            Site_Helper::setSes('hata',null);
        }else if(Site_Helper::getSes('bilgi')){
            $this->view->messages = array('err'=>0 , 'msg'=>Site_Helper::getSes('bilgi'));
            Site_Helper::setSes('bilgi',null);
        }

        $controller = $this->getRequest()->getControllerName();
        $action = $this->getRequest()->getActionName();

        $this->controller = $controller;
        $this->action = $action;

        $this->view->controller = $controller;
        $this->view->action = $action;

        $subview=new Zend_View();
        $subview->addBasePath(ROOT_DIR . '/application/views');

        $this->view->subview=$subview;

    }
    public function preDispatch() {

        $user = Site_Helper::getSes('user');

       if(!$user)
           $this->_redirect('/yonetim/auth/login');

        $activeGroup = Site_Helper::getSes('grup_kodu');

        if($activeGroup!=ADMIN_YETKI_KODU)
        {
            $acl = Site_Helper::getSes('acl');
            if($acl->has($this->getRequest()->getControllerName()))
            {
                try
                {
                    if(!$acl->isAllowed($activeGroup ,$this->_request->getControllerName(),$this->getRequest()->getActionName()))
                    {

                        Site_Helper::setSes("hata","Bu İşleme Yetkiniz Yok!");
                        echo 'yetkiyok'; exit;
                        $this->_redirect('/yonetim/index');
                        /*
                         * @todo error page yazılacak
                         * $this->_redirect('/error/error');
                        */
                        exit;
                    }
                }
                catch(Zend_Exception $e){
                    echo $e->getMessage();exit;
                }
            }
            else{

                echo 'yetkiyok'; exit;
                Site_Helper::setSes("hata","Bu İşleme Yetkiniz Yok!");
                $this->_redirect('/yonetim/index');

            }
        }

    }

    public function initScripts(){

        $this->view->headTitle(PANEL_ADI) ->setSeparator(' | ')->setAutoEscape(false);
        $this->view->headLink()->setStylesheet('/admin/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/font-icons/entypo/css/entypo.css');
        $this->view->headLink()->appendStylesheet('http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/bootstrap.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-core.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-theme.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-forms.css');

        $this->view->headScript()->appendFile('/admin/assets/js/gsap/main-gsap.js');
        $this->view->headScript()->appendFile('/admin/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/bootstrap.js');
        $this->view->headScript()->appendFile('/admin/assets/js/joinable.js');
        $this->view->headScript()->appendFile('/admin/assets/js/resizeable.js');
        $this->view->headScript()->appendFile('/admin/assets/js/wysihtml5/wysihtml5-0.4.0pre.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/neon-api.js');
        $this->view->headScript()->appendFile('/admin/assets/js/neon-custom.js');
        $this->view->headScript()->appendFile('/admin/assets/js/neon-demo.js');

        $this->view->headMeta()->appendName('keywords', SITE_KEYWORDS);
        $this->view->headMeta()->appendName('description', SITE_DESC);


    }
    public function postDispatch(){

    }

}


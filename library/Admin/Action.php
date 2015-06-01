<?php

class Admin_Action extends Zend_Controller_Action
{
    public function init()
    {
        parent::init();

        $this->_helper->layout->setLayout('panel');

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

        $viMenu = new ViMenu();
        $menu = $viMenu->liste(array('durum='=>1),array('ust_modul_id','sira'))->rows;
        $arr = array();
        $currentMenu = array();

        foreach($menu as $m){

            if($m['controller'] == $controller && $m['action'] == $action)
                $currentMenu = $m;

            if($m['ust_modul_id']==0)
                $arr[$m['id']] = $m;
            else
                $arr[$m['ust_modul_id']]['alt_menu'][] = $m;
        }

        $this->view->menu = $arr;
        $this->view->title = $currentMenu['aciklama'];
        $this->view->currentMenu = $currentMenu;

        $subview=new Zend_View();
        $subview->addBasePath(ROOT_DIR . '/application/views');

        $this->view->subview=$subview;

        if(!Site_Helper::getSes('userGroups')){
            $tblParametre = new TblParametre();
            $param = $tblParametre->liste(array('kod'=>Site_Helper::getSes('groupCodes')))->rows;
            $new = array();
            foreach($param as $p){
                $new[$p['kod']] = $p;
            }
            Site_Helper::setSes('userGroups',$new);
        }
        $this->view->userGroups = Site_Helper::getSes('userGroups');


    }
    public function preDispatch() {

        $user = Site_Helper::getSes('user');

        if(!$user)
            $this->_redirect('/admin/auth/logout');

        $activeGroup = Site_Helper::getSes('active_group');

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

                        /*
                         * @todo error page yazılacak
                         * $this->_redirect('/error/error');
                        */
                    }
                }
                catch(Zend_Exception $e){
                    echo $e->getMessage();exit;
                }
            }
            else{
                Site_Helper::setSes("hata","Bu İşleme Yetkiniz Yok!");
                /*
                 * @todo error page yazılacak
                 * $this->_redirect('/error/error');
                 */
            }
        }

    }

    public function postDispatch(){

    }

}


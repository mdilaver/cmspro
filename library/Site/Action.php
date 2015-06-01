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
    }
    public function preDispatch() {

        $user = Site_Helper::getSes('site_user');

        if(!$user)
            $this->_redirect('/auth/logout');
        else{

            if(is_file(DATA_URL."ogrenciler/".$user['username'].".jpg")){
                $user['img'] = "/assets/data/ogrenciler/".$user['username'].".jpg";
            }else{
                $user['img'] = "/assets/site/img/profile.jpg";
            }

            $user['kisa_adi'] = $user['kb_adi']." ".mb_strtoupper(mb_substr($user['kb_soyadi'],0,1,"utf-8"),"utf-8").".";
            $this->view->user = $user;
        }
    }
}
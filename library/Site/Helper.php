<?php
/**
 * Created by PhpStorm.
 * User: alkan
 * Date: 5/12/15
 * Time: 4:38 PM
 */
class Site_Helper
{
    public static function setSes($key ,$val){
        $sessions =new  Zend_Session_Namespace('sessions');
        $sessions->unlock();
        $sessions->__set($key,$val);
        $sessions->lock();
    }

    public static function getSes($key){
        $sessions =new  Zend_Session_Namespace('sessions');
        return $sessions->__get($key);
    }

    public static function isValid($post,$rules){
        $arr = array();
        foreach($rules as $r){
            if(!strlen($post[$r])){
                $arr[] = $r;
            }
        }

        if(sizeof($arr)){
            $msg = sizeof($arr) ==1 ? implode(", ",$arr)." alanını doldurmak zorundasınız!" : implode(", ",$arr)." alanlarını doldurmak olmak zorundasınız!";
            Site_Helper::setSes('hata',$msg);
            return false;
        }else{
            return true;
        }
    }

    public function pagination($data) {

        $adapter = new Zend_Paginator_Adapter_DbTableSelect($data);

        $paginator = new Zend_Paginator($adapter);

        $page = ($this->getRequest()->getParam('page')) ? $this->getRequest()->getParam('page') : 1;

        $paginator->setCurrentPageNumber($this->getRequest()->getParam('page'));

        $paginator->setDefaultScrollingStyle('Elastic');

        $paginator->setItemCountPerPage(MAX_ROW);

        return $paginator;
    }

    public static function sessionOrPost($post, $key)
    {
        $fc = Zend_Controller_Front::getInstance();

        $ses = new Zend_Session_Namespace("userSession");

        if($post['reset'])
        {
            $post = array();

            $ses->unlock();

            $ses->$key = '';

            $ses->lock();

            return $post;
        }

        /*
         * Posttan veri varsa postu döndür sessiondaki kelimeyi sil
         * sessionda kelime hala varsa onu döndür yoksa sessiondaki postu döndür
         */
        if(sizeof($post))
        {
            $ses->unlock();

            $ses->$key = $post;

            $ses->lock();

            return $post;
        }
        else
            return $ses->$key;
    }

    public static function api($url,$parametre=array(),$type='POST'){
        $client = new Site_Client($url);
        $client->setMethod($type);
        $client->setPostData($parametre);
        $client->send();

        if($client->isSuccess()){
            $response = json_decode($client->getHttpResponse(),1);
        }else{
            $response = json_decode($client->getErrorMessage(),1);
        }
        return $response;
    }

    public static function sezonGetir()
    {
        $ay = date('m');
        $yil = date('Y');
        $egitimyil = $ay < 9 ? ($yil - 1) . "-" . $yil : $yil . "-" . ($yil + 1);
        return $egitimyil;
    }

    public static function donemGetir()
    {
        $ay = date('m');
        $donem = $ay > 2 ? 2 : 1;
        return $donem;
    }

}
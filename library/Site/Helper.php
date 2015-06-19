<?php

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

    public static function pagination($data) {

        $fc =Zend_Controller_Front::getInstance();

        $adapter = new Zend_Paginator_Adapter_DbTableSelect($data);

        $paginator = new Zend_Paginator($adapter);

        $page = ($fc->getRequest()->getParam('sayfa')) ? $fc->getRequest()->getParam('sayfa') : 1;

        $paginator->setCurrentPageNumber($fc->getRequest()->getParam('sayfa'));

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
    /* ISO formatında tarihi alır.
     * ayGorunumu değeri 0 da Ay adını uzun döndürür. 1 de kısa döndürür. 3 te rakam döndürür
     * yilgorunumu 1 or 0
     */
    public static function getTarih($tarih, $ayGorunumu=0, $yilGorunumu=1)
    {
        $trh = strtotime($tarih);
        $gunint = date("d", $trh);
        $ayint = date("m", $trh);
        $yilint = date("Y", $trh);

        if($ayGorunumu==0) {

            $aylar = array(
                "01"=>"Ocak","02"=>"Şubat","03"=>"Mart","04"=>"Nisan","05"=>"Mayıs","06"=>"Haziran",
                "07"=>"Temmuz","08"=>"Ağustos","09"=>"Eylül","10"=>"Ekim","11"=>"Kasım","12"=>"Aralık");
            $ay = $aylar[$ayint];

        }
        elseif($ayGorunumu==1) {

            $aylar=array(
                "01"=>"Oca","02"=>"Şub","03"=>"Mar","04"=>"Nis","05"=>"May","06"=>"Haz",
                "07"=>"Tem","08"=>"Ağu","09"=>"Eyl","10"=>"Ekm","11"=>"Kas","12"=>"Ara");
            $ay = $aylar[$ayint];

        }
        else {
            $ay = $ayint;
        }

        if($yilGorunumu)
            $tarih =  $gunint . " " . $ay. " " . $yilint;
        else
            $tarih = $gunint . " " . $ay;

        return $tarih;
    }

    public static function getZaman($tarih)
    {
        $trh = strtotime($tarih);

        $gun = date("d", $trh);
        $ay = date("m", $trh);
        $yil = date("Y", $trh);

        return date('H:i',$trh);
    }


}
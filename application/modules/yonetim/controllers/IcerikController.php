<?php

class Yonetim_IcerikController extends Admin_Action
{

    public function init()
    {
        parent::init();
        parent::initScripts();
    }

    public function indexAction()
    {
        $this->view->headTitle()->prepend('İçerikler');
        $this->view->headLink()->appendStylesheet('/admin/assets/js/datatables/responsive/css/datatables.responsive.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/js/select2/select2-bootstrap.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/js/select2/select2.css');

        $this->view->headScript()->appendFile('/admin/assets/js/select2/select2.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/wysihtml5/bootstrap-wysihtml5.js');

        $tblicerik = new TblIcerik();
        $icerikler = $tblicerik->liste(null, array("yayin_tarihi desc"), null, null, null, null, null, 1);
        $paginator = Site_Helper::pagination($icerikler);
        $this->view->paginator = $paginator;
    }

    public function duzenleAction()
    {
        $this->view->headLink()->appendStylesheet('/admin/assets/js/wysihtml5/bootstrap-wysihtml5.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/js/selectboxit/jquery.selectBoxIt.css');
        $this->view->headScript()->appendFile('/admin/assets/js/wysihtml5/bootstrap-wysihtml5.js');
        $this->view->headScript()->appendFile('/admin/assets/js/jquery.multi-select.js');
        $this->view->headScript()->appendFile('/admin/assets/js/fileinput.js');
        $this->view->headScript()->appendFile('/admin/assets/js/bootstrap-datepicker.js');
        $this->view->headScript()->appendFile('/admin/assets/js/bootstrap-timepicker.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/selectboxit/jquery.selectBoxIt.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/bootstrap-tagsinput.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/ckeditor/ckeditor.js');
        $this->view->headScript()->appendFile('/admin/assets/js/ckeditor/adapters/jquery.js');


        $id = $this->getRequest()->getParam('id');
        $this->view->id = $id;
        $tblicerik = new TblIcerik();

        if($id){
            $this->view->headTitle()->prepend('İçerik Düzenle');
            $post = $tblicerik->getPost($id);
            $this->view->post = $post;

        }
        else{
            $this->view->headTitle()->prepend('Yeni İçerik');
        }

    }

    public function silAction()
    {
        // action body
    }

    public function kaydetAction()
    {
        $post = $this->getRequest()->getPost();
        $tblicerik = new TblIcerik();
        $id = $post['id'];
        $data = array(
            'baslik' => $post['baslik'],
            'icerik' => $post['icerik'],
        );

        if ($id) {
            try {
                $data['guncelleme_tarihi'] = Zend_Date::now()->getIso();
                $tblicerik->guncelle($id, $data);
            } catch (Zend_Exception $e) {
                echo $e->getMessage();exit;
                //@todo Error Page
            }
        } else {
            try {
                $data['yayin_tarihi'] = $post['yayin_tarihi'] ? $post['yayin_tarihi'] : Zend_Date::now()->getIso();
                $id = $tblicerik->insert($data);
            } catch (Zend_Exception $e) {
                echo $e->getMessage();exit;
                //@todo Error Page
            }
        }


        $this->_redirect('/yonetim/icerik/duzenle/id/' . $id);
    }


}








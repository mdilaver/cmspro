<?php

class Yonetim_AuthController extends Zend_Controller_Action
{

    public function init()
    {
        parent::init();
        $this->view->headTitle(PANEL_ADI) ->setSeparator(' | ')->setAutoEscape(false);
        $this->view->headTitle()->prepend('Login');
        $this->view->headLink()->setStylesheet('/admin/assets/js/jquery-ui/css/no-theme/jquery-ui-1.10.3.custom.min.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/font-icons/entypo/css/entypo.css');
        $this->view->headLink()->appendStylesheet('http://fonts.googleapis.com/css?family=Noto+Sans:400,700,400italic');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/bootstrap.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-core.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-theme.css');
        $this->view->headLink()->appendStylesheet('/admin/assets/css/neon-forms.css');
        $this->view->headScript()->setFile('/admin/assets/js/jquery-1.11.0.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/gsap/main-gsap.js');
        $this->view->headScript()->appendFile('/admin/assets/js/jquery-ui/js/jquery-ui-1.10.3.minimal.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/bootstrap.js');
        $this->view->headScript()->appendFile('/admin/assets/js/joinable.js');
        $this->view->headScript()->appendFile('/admin/assets/js/resizeable.js');
        $this->view->headScript()->appendFile('/admin/assets/js/neon-api.js');
        $this->view->headScript()->appendFile('/admin/assets/js/jquery.validate.min.js');
        $this->view->headScript()->appendFile('/admin/assets/js/neon-login.js');

    }

    public function indexAction()
    {
       Zend_Debug::dump(1);exit;
    }

    public function loginAction()

    {
        $this->view->headLink()->appendStylesheet('/admin/assets/css/custom.css');
    }

    public function kontrolAction()
    {
        $post = $this->getRequest()->getPost();
        $db = Zend_Db_Table::getDefaultAdapter();
        $rules = array('kullanici', 'sifre');

        if (Site_Helper::isValid($post, $rules)) {

            $authAdapter = new Zend_Auth_Adapter_DbTable($db);
            $authAdapter->setTableName('tbl_kullanici')
                ->setIdentityColumn('kullanici_adi')
                ->setCredentialColumn('sifre');

            $authAdapter->setIdentity($post['kullanici'])
                ->setCredential(md5($post['sifre']));

            $auth = Zend_Auth::getInstance();
            try {
                $result = $auth->authenticate($authAdapter);

            } catch (Zend_Exception $e) {
                $e->getMessage();
            }
            if (!$result->isValid()) {
                Site_Helper::setSes("hata", "Kullanıcı doğrulama başarısız!");
                $this->_redirect("/yonetim/auth/login");
            } else {
                $tbl = new TblKullanici();
                $user = $tbl->liste(array("kullanici_adi=" => $post['kullanici'], 'sifre=' => md5($post['sifre'])))->rows[0];
                Site_Helper::setSes('user', $user);

                $tblAcl = new TblAcl();
                $aclData = $tblAcl->liste(array('grup_kodu' => $user['grup_kodu']))->rows;

                $acl = new Zend_Acl();

                foreach ($aclData as $gHak) {
                    if (!$acl->hasRole($gHak['grup_kodu'])) {
                        $role = new Zend_Acl_Role($gHak['grup_kodu']);
                        $acl->addRole($role);
                    }

                    if (!$acl->has(new Zend_Acl_Resource($gHak['controller']))) {
                        $acl->add(new Zend_Acl_Resource($gHak['controller']));
                    }

                    $acl->allow($gHak['grup_kodu'], $gHak['controller'], $gHak['action']);

                    if ($acl->has($gHak['grup_kodu'], 'index', 'index')) {
                        $acl->allow($gHak['grup_kodu'], 'index', 'index');
                    }

                }

                Site_Helper::setSes('acl', $acl);
                Site_Helper::setSes('grup_kodu', $user['grup_kodu']);

                $this->_redirect("/yonetim/index");
            }
        }
    }

    public function logoutAction()
    {
        Site_Helper::setSes('user', null);
        $this->_redirect('/yonetim/auth/login');
    }


}






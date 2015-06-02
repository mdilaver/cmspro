<?php

class Yonetim_AuthController extends Zend_Controller_Action
{

    public function _init()
    {
    }

    public function indexAction()
    {
       Zend_Debug::dump(1);exit;
    }

    public function loginAction()
    {
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
                $user = $tbl->liste(array("kullanici_adi=" => $post['kullanici_adi'], 'sifre=' => md5($post['parola'])))->rows[0];
                if ($user['tip'] == 'site') {
                    Site_Helper::setSes('hata', 'Site kullanıcısı ile panele giriş yapamazsınız!');
                    $this->_redirect('/yonetim/auth/login');
                }

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
                Site_Helper::setSes('groupCodes', $grupCodes);
                Site_Helper::setSes('active_group', $grupCodes[0]);

                $this->_redirect("/yonetim/index");
            }
        }
    }

    public function logoutAction()
    {
        Site_Helper::setSes('user', null);
        $this->_redirect('/admin/auth/login');
    }


}






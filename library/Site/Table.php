<?php

class Site_Table extends Zend_Db_Table_Abstract
{
    protected $_select;
    protected $_formId;
    public $_error;

    public function getPrimary()
    {
        if(isset($this->_formId))
            return $this->_formId;
        else
            return $this->_primary;
    }

    /**
     * Tabloya yeni kayıt ekler
     *
     * @param array $post tabloya eklenecek değerler
     * @return mixed primary key değeri
     */
    public function ekle($post)
    {
        try
        {
            if($post)
            {
                $id=$this->insert($post);

                if(!$id)
                    $id=$this->_db->lastInsertId();

                if($id)
                    return $id;
                else
                    return null;
            }
            return null;
        }
        catch (Zend_Exception $e)
        {

            $this->_error=$e;
            return false;
        }
    }

    /**
     * satır güncelleme
     *
     * @param mixed $id primary key değeri veya where kriterleri
     * @param array $post değerler
     * @param bool $noId primary key yerine
     * @return bool
     * @author Sinan Kambur
     */
    public function guncelle($id, $post, $noId=false)
    {
        try
        {

            if(sizeof($this->_primary)==1)
            {
                $where=array();
                if(!$noId)
                    $where=array($this->getAdapter()->quoteInto($this->_primary[0] .' = ?', $id));
                else
                {
                    foreach ($id as $key=>$value)
                    {
                        if(is_array($value))
                            array_push($where, $key ." in ('". implode("','", $value). "')");
                        else
                            array_push($where, $this->getAdapter()->quoteInto($key .' = ?', $value));
                    }
                }

                $r=$this->update($post, $where);

                return $r;
            }
            else
            {
                $where = array();



                if(!$noId)
                {
                    foreach ($this->_primary as $key)
                    {
                        array_push($where , $key. " = '". $id[$key]."'");
                    }
                }
                else
                {
                    foreach ($id as $key=>$value)
                    {
                        if(is_array($value))
                            array_push($where, $key ." in ('". implode("','", $value). "')");
                        else
                            array_push($where, $this->getAdapter()->quoteInto($key .' = ?', $value));
                    }

                }

                $where = implode(" AND ",$where);

                try
                {
                    return $this->update($post,$where);
                }
                catch(Zend_Exception $e)
                {
                    //Zend_Debug::dump($e->getMessage());
                    //exit;
                    //$this->_error=$e;
                    $frontController = Zend_Controller_Front::getInstance();
                    $request = $frontController->getRequest();
                    //Ubit_Helper::mailGonder('sinan@ugurbilgi.net','OİS-Hata-'.$request->getControllerName() ."-".$request->getActionName(), $e->getMessage()."<br>".Ubit_Session::getSession()->user['kullanici_id']."<br>".Ubit_Session::getSession()->user['grup_kod']);


                }

            }
        }
        catch(Zend_Exception $e)
        {
            $this->_error=$e;
            echo $e->getMessage();
            exit;
        }
    }

    public function sil($where,$all=0)
    {
        if(!$where and $all==0)
            return 0;

        if(is_array($where))
        {
            $arr=array();
            foreach ($where as $k=>$v)
            {
                if(stripos($k,'>')>0 or stripos($k,'<')>0)
                {
                    $arr[] = "$k'$v'";
                }
                else if(substr($k,strlen($k)-2,2)=='in')
                {
                    if(is_array($v))
                    {
                        $arr[] = "$k(".implode(',',$v).")";
                    }
                    else
                    {
                        $arr[] = "$k ($v)";
                    }
                }
                else
                {
                    $arr[] = "$k='$v'";
                }
            }
            $where=$arr;
        }
        try {
            //print_r($where);exit;
            //Zend_Debug::dump($where);exit;
            return $this->delete($where);
        }
        catch(Zend_Exception $e)
        {
            $this->_error=$e;
            $frontController = Zend_Controller_Front::getInstance();
            $request = $frontController->getRequest();

            echo $e->getMessage();
            exit;
        }
        return 0;
    }

    /**
     * array den Where kriteri oluşturur
     *
     * @param array $where
     * @param Zend_Db_Select $sql
     * @return Zend_Db_Select
     * @author Sinan Kambur
     */
    /**
     * array den Where kriteri oluşturur
     *
     * @param array $where
     * @param Zend_Db_Select $sql
     * @return Zend_Db_Select
     * @author Sinan Kambur
     */
    function getWhere($where, $sql, $between=null) {



        foreach ($where as $key => $value) {

            if (stripos($key, ' ') > -1) {
                $tmp = explode(' ', $key);
                $key = $tmp[0] . " " . $tmp[1];
            }else if(is_int($key))
            {
                if($value)
                    $sql = $sql->where($value);
                continue;
            }

            if (is_array($value)) {

                for ($i = 0; $i < sizeof($value); $i++) {
                    if ($value == '') {
                        unset($value[$i]);
                        continue;
                    }
                    $value[$i] = "'" . $value[$i] . "'";
                }

                if (sizeof($value) > 0)
                    $sql = $sql->where($key . " IN (" . implode(',', $value) . ")");
            }
            else {
                $value=addslashes($value);
                if (stripos($key, '>') > -1 or stripos($key, '<') > -1 or stripos($key, '=') > -1) {
                    if(strlen($value))
                        $sql = $sql->where($key . " ?", $value);
                } else if (stripos(strtolower($key), " in") > -1) {
                    if(strlen($value))
                        $sql = $sql->where($key . $value);
                }
                else
                {
                    if(strlen($value))
                        $sql = $sql->where($key . " like '%".$value."%'");
                }

            }
        }

        foreach ($between as $key => $value) {
            $sql = $sql->where($key . " between ? and ?", $value[0], $value[1]);
        }

        return $sql;
    }

    public function liste($where=null, $sort=null, $limit=null, $offset=null, $cols=null, $group=null, $count=null, $returnSql=0, $having=null) {
        try {
            if ($cols) {
                $keys = array_keys($cols);
                if (!$cols[0] and !eregi("\(", $cols[$keys[0]])) {
                    $cols = $keys;
                }
            }
            $db = $this->getAdapter();
            if (!$this->_select) {
                if ($cols) {
                    $this->_select = $this->select()->from($this->_name, $cols);
                } else {
                    $this->_select = $this->select()->from($this->_name);
                }
            }
            if ($count and ($group or $cols)) {
                $columns = $this->_select->getPart('columns');
                array_push($columns, array(null, new Zend_Db_Expr('count(*)'), 'adet'));
                $this->_select->setPart('columns', $columns);
                if (!$group) {
                    $this->_select = $this->_select->group($cols);
                }
            }
            //Zend_Debug::dump($sort);exit;
            $this->_select = $this->_select->limit($limit, $offset)
                ->order($sort);
            if ($group) {
                $this->_select = $this->_select->group($group);
            }
            if ($having) {
                $this->_select = $this->_select->having($having);
            }
            if ($where) {
                $between = array();
                $_where = $where;
                foreach ($where as $k => $v) {
                    if (is_array($v)) {
                        for ($i = 0; $i < sizeof($v); $i++) {
                            if ($v[$i] == '' or !$v) {
                                unset($v[$i]);
                            }
                        }
                        $where[$k] = $v;
                    }
                    if ($k{0} == '_' and $v) {
                        $between[substr($k, 3)][] = $v;
                        unset($where[$k]);
                    }
                }
                foreach ($between as $k => $v) {
                    if (sizeof($v) == 1 and $v[0] != '') {
                        $where[$k] = $v[0];
                        unset($between[$k]);
                    }
                }
                $this->_select = $this->getWhere($where, $this->_select, $between);
            }
            $fc = Zend_Controller_Front::getInstance();
            if(stripos($fc->getRequest()->getActionName(),'ajax')>-1)
            {
                $userSession = new Zend_Session_Namespace('userSession');
                $userSession->unlock();
                $ind=$fc->getRequest()->getControllerName();
                $sql=$this->_select;
                $sql->reset('limitcount');
                $userSession->yazdir[$ind]['model']=get_class($this);
                $where=array_merge($where, $between);
                $userSession->yazdir[$ind]['where']=$_where;
                $userSession->yazdir[$ind]['sort']=$sort;
                $userSession->yazdir[$ind]['cols']=$cols;
                $userSession->yazdir[$ind]['count']=$count;
                $userSession->yazdir[$ind]['group']=$group;
                $userSession->lock();
                $this->_select->limit($limit, $offset);
            }

            //echo "sql: ".$this->_select ."\n\n";
            if ($returnSql) {
                $sql = $this->_select;
                $this->_select = null;
                return $sql;
            }
            $r = new stdClass();
            $r->rows = $db->fetchAll($this->_select);
            if ($limit) {
                $this->_select->reset('order');
                $this->_select->reset('limitcount');
                $this->_select->reset('limitoffset');
                //$this->_select->reset('group');
                $this->_select->setPart('columns', array(array($this->_name, new Zend_Db_Expr('count(*) as adet'))));
                $rc = $db->fetchAll($this->_select);
                if (sizeof($rc) > 1) {
                    $r->rowcount = sizeof($rc);
                } else {
                    $r->rowcount = $rc[0]['adet'];
                }
                if (!$r->rowcount) {
                    $r->rowcount = 0;
                }
            }
            $this->_select = null;
            return $r;
        } catch (Zend_Exception $e) {
            echo $e->getMessage();
            exit;
        }
        return null;
    }

    /**
     * tabloda geçilen parametreye göre kayıt var mı
     *
     * @param array $where
     * @return bool
     * @author Sinan Kambur
     */
    public function kontrol($where)
    {
        try {
            $sql=$this->select();
            if(is_array($where))
            {
                /*foreach ($where as $key=>$value)
                {
                    //array_push($where, $this->getAdapter()->quoteInto($key .' = ?', $value));
                    $sql = $sql->where($this->getAdapter()->quoteInto($key .' = ?', $value));
                }*/
                $this->getWhere($where, $sql);
            }
            else
                $sql = $this->select()->where($where);
            $row = $this->fetchRow($sql);
            if($row){
                return true;
            }
            else
                return false;
        }
        catch (Zend_Exception $e)
        {
            echo $e->getMessage();
            exit;
        }
    }

}
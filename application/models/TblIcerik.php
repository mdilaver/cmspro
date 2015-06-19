<?php

class TblIcerik extends Site_Table
{
    protected $_name = 'tbl_icerik';
    protected $_primary = array('id');

    function getPost($id)
    {
        $post = $this->fetchRow($this->select()->where('id = ?', $id));
        return $post;
    }
}

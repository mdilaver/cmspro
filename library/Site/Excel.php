<?php
require_once ROOT_DIR."/library/PhpExcel/PHPExcel.php";
/**
 * Created by PhpStorm.
 * User: alkan
 * Date: 5/18/15
 * Time: 1:57 PM
 */

class Site_Excel {

    function __construct(){
        parent::__construct();
    }
    function read($file){
        $objReader = new PHPExcel_Reader_Excel2007();
        $objPHPExcel = $objReader->load($file);
        $data = $objPHPExcel->getActiveSheet()->toArray();
        $keys = $data[0];
        unset($data[0]);

        foreach($data as $k=> $o){
            $arr[] = array_combine($keys,$o);
        }

        return $arr;
    }
}

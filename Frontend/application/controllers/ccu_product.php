<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

/**
 *
 * CCU_product.php: This file contains CCU Product Class
 *
 **/

/**
 * CCU Product Class
 *
 *
 **/

class Ccu_product extends Base_controller {
/**
 *
 * Constructor: extends from Base controller Class
 *
 */
	public function __construct(){
		parent::__construct();
	}
	
/**
 * Index
 *
 * Default page
 *
 */
	public function index()
	{
		$this->ccu_product_information();
	}
	
/**
 * CCU Products Detail
 * NOTES:
 * _ Show những Product name có CCU tổng bị drop quá ngưỡng config trên CCU Dashboard ( màu đỏ )
 * _ Show những Product name có 50% số lượng server có CCU bị drop ( ko theo chu kì ) ( màu xanh lá cây )
 * _ Thời gian keep alert 15p sau đó sẽ nhảy xuống phần log alert
 * 
 *
 */
	public function ccu_product_information() {
		$this->loadview('ccu_product/ccu_product', array());
	}
}

?>
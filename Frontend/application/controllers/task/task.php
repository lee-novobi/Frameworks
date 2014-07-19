<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
require_once 'application/controllers/base_controller.php';

/**
 *
 * Task.php: This file contains Task Class, the controller for Tasks of Monitor_Assistant
 *
 **/

/**
 * Task Class
 *
 *
 **/

class Task extends Base_controller {
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
		$this->task_list();
	}
	
/**
 * Task List
 *
 * Show list of tasks in shifts
 * NOTES: shift chief -> show full | Members -> only show tasks followed
 *
 */
	public function task_list() {
		$this->loadview('task/task_list', array());
	}
}

?>
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdminController extends Controller
{
	
	/**
	 * Index Page for this controller.
	 */
	public function index() {
		$this->loadView('admin');
	}
}

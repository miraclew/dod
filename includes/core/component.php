<?php
abstract class Component {
	private $_controller;
	
	public function __construct($controller) {
		$this->_controller = $controller;
	}
	
	public abstract function initialize();

}
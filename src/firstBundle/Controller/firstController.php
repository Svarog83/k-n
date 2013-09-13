<?php

namespace SDClasses\firstBundle\Controller;
use SDClasses;
use SDClasses\AppConf;

class firstController extends SDClasses\Controller
{
	public function defaultAction()
	{
		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );
	}
}

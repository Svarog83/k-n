<?php
namespace SDClasses\compBundle\Controller;
use SDClasses;
use SDClasses\AppConf;
use SDClasses\NoEscapeClass;
use SDClasses\User;
use SDClasses\Controller;

class compController extends Controller
{
	public function newAction()
	{
		$this->render( array( 'module' => 'comp', 'view' => 'new' ) );
	}
}
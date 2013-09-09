<?php

namespace SDClasses\firstBundle\Controller;
use SDClasses;
use SDClasses\AppConf;

class firstController extends SDClasses\Controller
{
	public function defaultAction()
	{
		echo 'this is default action of first controller. <br> <a href="/">Exit</a>';
		AppConf::getIns()->uid = 'wrong';
		AppConf::getIns()->user = 'bad';
	}
}

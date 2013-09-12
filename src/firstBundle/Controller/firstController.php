<?php

namespace SDClasses\firstBundle\Controller;
use SDClasses;
use SDClasses\AppConf;

class firstController extends SDClasses\Controller
{
	public function defaultAction()
	{
		$menu = array (

		'items' => array (
			"Главная" => array (
				'path'      => '/',
				'class'     => 'icon icon-home',
				'active'    =>  true
			),
			"Поставка" => array (
				'path'      => '/job',
				'class'     => 'icon icon-th-list',
				'active'    =>  false,
				'submenu'   => array (
					"Список" => array (
								'path'      => '/job/list',
								'class'     => '',
						),
					"Новая" => array (
								'path'      => '/job/new',
								'class'     => '',
						),
					"Сверка" => array (
								'path'      => '/job/check',
								'class'     => '',
						),
				)
			),
			"Компания" => array (
							'path'      => '/comp',
							'class'     => 'icon icon-th-list',
							'submenu'   => array (
								"Список" => array (
											'path'      => '/comp/list',
											'class'     => '',
									),
								"Новая" => array (
											'path'      => '/comp/new',
											'class'     => '',
									),
								"Статистика" => array (
											'path'      => '/comp/stats',
											'class'     => '',
									),
							)
						),
				"Договор" => array (
							'path'      => '/comp',
							'class'     => 'icon icon-th-list',
							'submenu'   => array (
								"Список" => array (
											'path'      => '/contr/list',
											'class'     => '',
									),
								"Новый" => array (
											'path'      => '/contr/new',
											'class'     => '',
									),
							),
						),
				"Графики" => array (
								'path'      => '/chart',
								'class'     => 'icon icon-signal',
							),

			)
		);

		$this->render( array ( 'view' => 'header' ), array () );
		$this->render( array ( 'view' => 'sidebar' ), array ( 'menu' => $menu ) );
		$this->render( array ( 'module' => 'first', 'view' => 'default' ), array () );
		$this->render( array ( 'view' => 'footer' ), array () );

	}
}

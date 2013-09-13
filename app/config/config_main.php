<?php


use SDClasses\AppConf;

$AC->STD = '0';
$AC->logout_time = 1800;
$AC->IS_DST = AppConf::is_dst();
$AC->Charset = 'utf-8';
$AC->DBCharset = 'utf8';
$AC->secret_salt = 'kn_salt';

$AC->admin_emails = array(
	'sergey.vetko@gmail.com'
);

$AC->menu = array(
	'items' => array(
		"main" => array(
			'name' => 'Главная',
			'path' => '/',
			'class' => 'icon icon-home',
			'active' => true
		),
		"job" => array(
			'name' => 'Поставка',
			'path' => '/job',
			'class' => 'icon icon-th-list',
			'active' => false,
			'submenu' => array(
				"job_list" => array(
					'name' => 'Список',
					'path' => '/job/list',
					'class' => '',
				),
				"job_new" => array(
					'name' => 'Новая',
					'path' => '/job/new',
					'class' => '',
				),
				"job_check" => array(
					'name' => 'Сверка',
					'path' => '/job/check',
					'class' => '',
				),
			)
		),
		"comp" => array(
			'name' => 'Компания',
			'path' => '/comp',
			'class' => 'icon icon-th-list',
			'submenu' => array(
				"comp_list" => array(
					'name' => 'Список',
					'path' => '/comp/list',
					'class' => '',
				),
				"comp_new" => array(
					'name' => 'Новая',
					'path' => '/comp/new',
					'class' => '',
				),
				"comp_stats" => array(
					'name' => 'Статистика',
					'path' => '/comp/stats',
					'class' => '',
				),
			)
		),
		"contr" => array(
			'name' => 'Контракт',
			'path' => '/contr',
			'class' => 'icon icon-th-list',
			'submenu' => array(
				"contr_list" => array(
					'name' => 'Список',
					'path' => '/contr/list',
					'class' => '',
				),
				"contr_new" => array(
					'name' => 'Новый',
					'path' => '/contr/new',
					'class' => '',
				),
			),
		),
		"chart" => array(
			'name' => 'Графики',
			'path' => '/chart',
			'class' => 'icon icon-signal',
		),

	)
);

$AC->modules = array(
	'main' => array(
		'name' => 'Главная',
		'active' => true
	),
	'comp' => array(
		'name' => 'Компания',
		'active' => true
	),
	'contr' => array(
		'name' => 'Контракт',
		'active' => true
	),
	'chart' => array(
		'name' => 'Графики',
		'active' => true
	),
	'job' => array(
		'name' => 'Поставка',
		'active' => true
	),
	'user' => array(
		'name' => 'Пользователь',
		'active' => true
	),
);

<?php

use SDClasses\AppConf;
use \User;

/**
 * @var array $params
 * @var User $user
 * @var SDClasses\View $this
 */
$user = $params['user'];

?>

<div class="container-fluid">

	<div class="widget-box">
		<div class="widget-title">
			<ul class="nav nav-tabs">
				<li class="active"><a data-toggle="tab" href="#tab1">Общие</a></li>
				<li class=""><a data-toggle="tab" href="#tab2">Уведомления</a></li>
				<li class=""><a data-toggle="tab" href="#tab3">Интерфейс</a></li>
			</ul>
		</div>
		<div class="widget-content tab-content">
			<div id="tab1" class="tab-pane active">Общие настройки (логин, пароль, почта и т.п.)
			<br>
				<br>
				<?= $this->showButton( 'Сохранить', 'btn-success', 'icon-ok', array ( 'id' => 'btn1' ) ); ?>
				<?= $this->showButton( 'Отменить', 'btn-danger', 'icon-remove', array ( ) ); ?>
			</div>
			<div id="tab2" class="tab-pane">Настройка уведомлений (когда и что отсылать)</div>
			<div id="tab3" class="tab-pane">Настройки интерфейса (цветовая гамма, что показывать)</div>
		</div>
	</div>

</div>
<?php

use SDClasses\AppConf;
use \User;

/**
 * @var array $params
 * @var User $user
 * @var SDClasses\View $this
 */
$user = $params['user'];
$row = $user->getRow();

?>

<div class="container-fluid">

	<div class="row-fluid">

		<div class="span12">
			<div class="widget-box">
				<div class="widget-title">
										<span class="icon">
											<i class="icon-user"></i>
										</span>
					<h5>Редактирование профиля</h5>

					<div class="buttons">
						<a title="Вернуться назад" class="btn btn-mini" href="/user/profile/"><i
									class="icon-hand-left"></i>
							Назад</a>
					</div>
				</div>

				<div class="widget-content form-horizontal">
					<form action="/user/save_profile" method="get" class="form-horizontal"/>
					<?= $this->showEntry( 'ID', $row['user_id'] ); ?>
					<?= $this->showText( 'form_name', User::showUserName( $row ) ); ?>
					<?= $this->showText( 'form_login', $row['user_login'] ); ?>
					<?= $this->showText( 'form_email', $row['user_email'] ); ?>
					<?= $this->showEntry( 'Активен?', $row['user_activ'] == 'a' ? 'Да' : 'Нет' ); ?>
					<div class="form-actions">
						<?= $this->showButton( 'Сохранить', 'btn-success', 'icon-ok', array ( 'submit' => true ) ) ?>
					</div>

				</div>
			</div>

		</div>

	</div>
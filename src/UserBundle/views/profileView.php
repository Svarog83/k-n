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
					<h5><?= User::showUserName( $row ) ?></h5>

					<div class="buttons">
						<a title="Редактировать пользователя" class="btn btn-mini" href="/user/edit_profile/"><i class="icon-edit"></i>
							Редактировать</a>
						<a title="Распечатать страницу" class="btn btn-mini" href="#"><i class="icon-print"></i> Печать</a>
					</div>
				</div>

				<div class="widget-content form-horizontal">
					<?= $this->showEntry( 'ID', $row['user_id'] ); ?>
					<?=  $this->showEntry( 'Имя', User::showUserName( $row ) ); ?>
					<?=  $this->showEntry( 'Логин', $row['user_login'] ); ?>
					<?=  $this->showEntry( 'E-mail', $row['user_email'] ); ?>
					<?=  $this->showEntry( 'Активен?', $row['user_activ'] == 'a' ? 'Да' : 'Нет' ); ?>
				</div>
			</div>

		</div>

	</div>
<?php

use SDClasses\AppConf;
use SDClasses\Form;
use \User;

/**
 * @var array $params
 * @var User $user
 * @var SDClasses\View $this
 */
$form = new Form();
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
					<form action="/user/save_profile" method="get" class="form-horizontal">
						<?= $this->showEntry( 'ID', $row['user_id'] ); ?>
						<?= $form->showTextBlock( 'Имя', 'form_name', User::showUserName( $row ), array( 'validation' => 'required' ) ); ?>
						<?= $form->showTextBlock( 'Логин', 'form_login', $row['user_login'] ); ?>
						<?= $form->showTextBlock( 'E-mail', 'form_email', $row['user_email'], array( 'help_block' => 'Проверьте внимательно!' ) ); ?>

						<?= $form->showTextBlock( 'Новый пароль', 'form_pass_new', '', array( 'placeholder' => 'New placeholder' ) ); ?>

						<?= $form->showSelectBlock( "Пол", 'form_sex', '', array( 'multiple' => false, "validation" => 'required', 'show_select_title' => "Выберите пол", 'select_values' => array( 'm' => 'Male', 'f' => 'Female' ) ) ); ?>

						<?= $form->showTextAreaBlock( "О себе", 'form_about' ); ?>

						<?= $form->showRadioBlock( "Заходить автоматом?", 'form_auto_enter', 1, array( 'id' => "form_auto_id", 'select_values' => array( "0" => 'Нет', '1' => 'Да' ) ) ); ?>

						<?=
						$form->showCheckBoxesBlock( 'Доступ к модулям', 'form_modules[]', array( '1', '2' ), array(
							'no_title' => false,
							'select_values' => array(   '0' => 'Компании',
														'1' => 'Контракты',
														'2' => 'Поставки' ),
							"validation" => "" ) ); ?>

						<?= $this->showEntry( 'Активен?', $row['user_activ'] == 'a' ? 'Да' : 'Нет' ); ?>

						<div class="form-actions">
							<?= $this->showButton( 'Сохранить', 'btn-success', 'icon-ok', array( 'submit' => true ) ) ?>
						</div>
					</form>
				</div>
			</div>

		</div>

	</div>
</div>
<?php

use SDClasses\AppConf;
use SDClasses\Form;
use SDClasses\User;

/**
 * @var array $params
 * @var User $user
 * @var SDClasses\View $this
 */
$form = new Form( $this );

$options_arr = $form->SaveFormOptions( 'form_id', 'comp', 'save', array( 'need_confirm' => false,
	'upload_exist' => false
) );
$options_arr['show_close'] = false;
$form->SaveForm( $options_arr );

/*

<div class="container-fluid">

	<div class="row-fluid">

		<div class="span12">
			<div class="widget-box">
				<div class="widget-title">
										<span class="icon">
											<i class="icon-user"></i>
										</span>
					<? if ( !$user->getExist() ): ?>
						<h5>Создание пользователя</h5>
					<? else: ?>
						<h5>Редактирование пользователя</h5>
					<? endif; ?>

					<div class="buttons">
						<a title="К списку пользователей" class="btn btn-mini" href="/user/list"><i
									class="icon-user"></i>
							Список пользователей</a>
					</div>
				</div>

				<div class="widget-content form-horizontal">
					<form action="/user/save" method="POST" class="form-horizontal">
						<input type="hidden" name="form_user_id" value="<?= $row['user_id'] ?>">
						<?= $form->showTextBlock( 'Имя', 'form_name', $row['user_name_rus'], array( 'validation' => 'required' ) ); ?>
						<?= $form->showTextBlock( 'Фамилия', 'form_surname', $row['user_fam_rus'], array( 'validation' => 'required' ) ); ?>
						<?= $form->showTextBlock( 'Логин', 'form_login', $row['user_login'], array( 'validation' => 'required' ) ); ?>
						<?= $form->showTextBlock( 'E-mail', 'form_email', $row['user_email'], array( 'validation' => 'required', 'help_block' => 'Проверьте внимательно!' ) ); ?>

						<?= $form->showTextBlock( 'Пароль', 'form_pass', '', array( 'placeholder' => 'Введите пароль' ) ); ?>

						<?= $form->showSelectBlock( "Пол", 'form_sex', $row['user_sex'], array( 'multiple' => false, "validation" => 'required', 'show_select_title' => "Выберите пол", 'select_values' => array( 'm' => 'Мужской', 'f' => 'Женский' ) ) ); ?>

						<?= $form->showRadioBlock( "Заблокирован?", 'form_blocked', $row['user_blocked'], array( 'id' => "form_blocked_id", 'select_values' => array( "0" => 'Нет', '1' => 'Да' ) ) ); ?>

						<div class="form-actions">
							<?= $this->showButton( 'Сохранить', 'btn-success', 'icon-ok', array( 'submit' => true ) ) ?>
						</div>
					</form>
				</div>
			</div>

		</div>

	</div>
</div>
*/
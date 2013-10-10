<?php

use SDClasses\AppConf;
use SDClasses\Form;
use SDClasses\FormElement;
use SDClasses\User;

/**
 * @var array $params
 * @var User $user
 * @var SDClasses\View $this
 */
$form = new Form(
array( 'form_id' => 'form_id', 'module' => 'comp', 'action' => 'save', 'need_confirm' => false, 'upload_exist' => false )
);

$FormElements = array();

$FormElements[] = new FormElement( 'hidden', '', 'comp_id', '' );
$FormElements[] = new FormElement( 'text', 'Название', 'comp_name', '', array ( 'size' => '50' ) );
$FormElements[] = new FormElement( 'radio', 'Зарубежная?', 'comp_foreign', '', array( 'id' => "form_foreign_id", 'select_values' => array( "0" => 'Нет', '1' => 'Да' ) ) );
$FormElements[] = new FormElement( 'checkbox', 'Департаменты', 'comp_dep', array(), array( 'select_values' => array( "1" => 'Таможня', '2' => 'Логистика', '3' => 'Бухгалтерия' ) ) );
$FormElements[] = new FormElement( 'select', 'Тип компании', 'comp_type', '', array( 'multiple' => false, "validation" => 'required', 'show_select_title' => "Выберите тип", 'select_values' => array( 'b' => 'Брокер', 'c' => 'Заказчик', 't' => 'Перевозчик' ) ) );

$FormElements[] = new FormElement( 'radio', 'Is activated?', 'comp_blocked', '', array( 'id' => "form_foreign_id", 'select_values' => array( "0" => 'No', '1' => 'Yes' ) ) );

$FormElements[] = new FormElement( 'empty', ' ', '', '' );

$FormElements[] = new FormElement( 'textarea', 'Banking account', 'comp_account', '', array( ) );
$FormElements[] = new FormElement( 'text', 'VAT Number', 'comp_vat', '', array( ) );
$FormElements[] = new FormElement( 'textarea', 'Address', 'comp_address', '', array( ) );

$FormElements[] = new FormElement( 'empty', ' ', '', '' );

$FormElements[] = new FormElement( 'text', 'General manager name', 'comp_manager_name', '', array( ) );
$FormElements[] = new FormElement( 'text', 'Official e-mail', 'comp_email', '', array( ) );
$FormElements[] = new FormElement( 'text', 'Phone number', 'comp_phone', '', array( ) );

?>

<div class="container-fluid">

	<div class="row-fluid">

		<div class="span12">
			<div class="widget-box">
				<div class="widget-title">
										<span class="icon">
											<i class="icon-user"></i>
										</span>
					<? if ( true ): ?>
						<h5>Создание компании</h5>
					<? else: ?>
						<h5>Редактирование компании</h5>
					<? endif; ?>

					<div class="buttons">
						<a title="К списку пользователей" class="btn btn-mini" href="/comp/list"><i
									class="icon-user"></i>
							Список компаний</a>
					</div>
				</div>

			<? $form->showForm( $this, $FormElements ); ?>

			</div>

		</div>

	</div>
</div>

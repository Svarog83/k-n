<?php

use SDClasses\AppConf;
use SDClasses\User;

/**
 * @param array $UsersArr
 */
$UsersArr = $params['users'];

/**
 * @var array $params
 * @var User $user
 * @var SDClasses\View $this
 */
?>
<div class="container-fluid">

	<div class="row-fluid">

		<div class="span12">
			<div class="widget-content nopadding">
				<table class="table table-striped table-bordered data-table">
					<thead>
					<tr>
						<th>Имя</th>
						<th>Логин</th>
						<th>E-mail</th>
						<th>Пол</th>
						<th>Статус</th>
						<th>&nbsp;</th>
					</tr>
					</thead>
					<tbody>
					<?php foreach ( $UsersArr AS $row )
					{
						?>
						<tr>
							<td><?= User::showUserName( $row ) ?></td>
							<td><?= $row['user_login'] ?></td>
							<td><?= $row['user_email'] ?></td>
							<td><?= $row['user_sex'] ?></td>
							<td><?= $row['user_blocked'] ? 'Заблокирован' : 'Активен' ?></td>

							<td class="taskOptions">
								<a href="/user/edit/<?= $row['user_id']?>" class="tip-top" data-original-title="Редактировать"><i
											class="icon-edit"></i></a>
								<a href="/user/delete/<?= $row['user_id']?>" class="tip-top"
								   data-original-title="Удалить"><i class="icon-remove"></i></a>
							</td>

						</tr>
					<?
					}

					/*
					 <tr>
					 						<td class="taskDesc"><i class="icon-info-sign"></i> Code a new theme</td>
					 						<td class="taskStatus"><span class="in-progress">in progress</span></td>
					 						<td class="taskOptions"><a href="#" class="tip-top" data-original-title="Update"><i
					 										class="icon-ok"></i></a> <a href="" class="tip-top"
					 						                                            data-original-title="Delete"><i
					 										class="icon-remove"></i></a></td>
					 					</tr>
					 					<tr>
					 						<td class="taskDesc"><i class="icon-plus-sign"></i> Update a site</td>
					 						<td class="taskStatus"><span class="pending">pending</span></td>
					 						<td class="taskOptions"><a href="#" class="tip-top" data-original-title="Update"><i
					 										class="icon-ok"></i></a> <a href="" class="tip-top"
					 						                                            data-original-title="Delete"><i
					 										class="icon-remove"></i></a></td>
					 					</tr>
					 					<tr>
					 						<td class="taskDesc"><i class="icon-ok-sign"></i> Meet the customers</td>
					 						<td class="taskStatus"><span class="done">done</span></td>
					 						<td class="taskOptions"><a href="#" class="tip-top" data-original-title="Update"><i
					 										class="icon-ok"></i></a> <a href="" class="tip-top"
					 						                                            data-original-title="Delete"><i
					 										class="icon-remove"></i></a></td>
					 					</tr>*/


					?>

					</tbody>
				</table>
			</div>

		</div>

	</div>

</div>
</div>

<script type="text/javascript">
	<!--
	$(document).ready(function()
	{

		<?php if ( !empty( $params['flash_message'] ) ): ?>
			$.gritter.add({
				title:	'',
				text:	'<?= $params['flash_message'] ?>',
				sticky: true
			});
		<? endif;?>
	} );
	//-->
</script>


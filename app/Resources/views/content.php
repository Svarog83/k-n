<?php
use SDClasses\AppConf;
/**
 * @var array $params
 */
?>

<div class="container-fluid">
	<br>

	<div class="alert <?= !empty( $params['class'] ) ? $params['class'] : 'alert-info' ?>">
		<strong><?= $params['text'] ?></strong>
		<a href="#" data-dismiss="alert" class="close">×</a>
	</div

	<div id="footer" class="span12">
		&nbsp;
	</div>
</div>

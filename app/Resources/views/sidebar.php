<?php
use SDClasses\AppConf;

/**
 * @var array $params
 */
?>

<div id="sidebar">
	<ul>
		<?php
		foreach ( $params['menu']['items'] AS $abr => $item )
		{
			if ( !isset ( $item['submenu'] ) )
			{
				?>
				<li class="<?= !empty( $item['active'] ) ? 'active' : '' ?>"><a href="<?= $item['path'] ?>"><i
								class="<?= $item['class'] ?>"></i> <span><?= $item['name'] ?></span></a></li>
			<?
			}
			else
			{
				?>
				<li class="submenu <?= !empty( $item['active'] ) ? 'active' : '' ?>">
					<a href="#"><i class="<?= $item['class'] ?>"></i> <span><?= $item['name'] ?></span></a>
					<ul>
						<?php

						foreach ( $item['submenu'] AS $abr2 => $item2 )
						{
							?>
							<li class="<?= !empty( $item2['active'] ) ? 'active' : '' ?>"><a
										href="<?= $item2['path'] ?>"><i class="<?= $item2['class'] ?>"></i>
									<span><?= $item2['name'] ?></span></a></li>
						<?
						}
						?>
					</ul>
				</li>
			<?
			}
		}
		?>
	</ul>
</div>

<div id="content">
	<div id="breadcrumb">
		<a href="/" title="Главная" class="tip-bottom"><i class="icon-home"></i> Главная</a>
		<?php if ( AppConf::getIns()->route->getModule() != '' && is_array ( AppConf::getIns()->breadcrumb ) ): ?>
			<?php foreach ( AppConf::getIns()->breadcrumb AS $bread )
			{
				?>
				<a href="#" class="current"><?= $bread ?></a>
			<? } ?>
		<? endif; ?>
	</div>

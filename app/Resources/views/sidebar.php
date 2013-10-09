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
			$active = AppConf::getIns()->route->getModule() == str_replace ( '/', '', $item['path'] ) ? true : false;
			if ( !isset ( $item['submenu'] ) )
			{
				?>
				<li class="<?= $active ? 'active open' : '' ?>"><a href="<?= $item['path'] ?>"><i
								class="<?= $item['class'] ?>"></i> <span><?= $item['name'] ?></span></a></li>
			<?
			}
			else
			{
				?>
				<li class="submenu <?= $active ? 'active open' : '' ?>">
					<a href="#"><i class="<?= $item['class'] ?>"></i> <span><?= $item['name'] ?></span></a>
					<ul>
						<?php

						foreach ( $item['submenu'] AS $abr2 => $item2 )
						{
							$active2 = AppConf::getIns()->route->getAction() == explode ( '/', $item2['path'] )[2] ? true : false;
							?>
							<li class="<?= $active2 ? 'active' : '' ?>"><a
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
			<?php
			$count = count ( AppConf::getIns()->breadcrumb ) - 1;
			foreach ( AppConf::getIns()->breadcrumb AS $k => $bread )
			{
				?>
				<a href="#" <?= $k == $count ? 'class="current"' : ''?>><?= $bread ?></a>
			<? } ?>
		<? endif; ?>
	</div>

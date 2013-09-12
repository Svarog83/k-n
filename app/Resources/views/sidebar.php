<div id="sidebar">
	<ul>
<?php

foreach ( $params['menu']['items'] AS $name => $item )
{
	if ( !isset ( $item['submenu'] ) )
	{
?>
		<li class="<?= !empty( $item['active'] ) ? 'active' : '' ?>"><a href="<?= $item['path']?>"><i class="<?= $item['class']?>"></i> <span><?= $name?></span></a></li>
<?
	}
	else
	{
?>
		<li class="submenu <?= !empty( $item['active'] ) ? 'active' : '' ?>">
			<a href="#"><i class="<?= $item['class']?>"></i> <span><?= $name?></span></a>
			<ul>
				<?php

				foreach ( $item['submenu'] AS $name2 => $item2 )
				{
				?>
						<li class="<?= !empty( $item2['active'] ) ? 'active' : '' ?>"><a href="<?= $item2['path']?>"><i class="<?= $item2['class']?>"></i> <span><?= $name2?></span></a></li>
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
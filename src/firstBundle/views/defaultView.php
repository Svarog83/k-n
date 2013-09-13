<?php
use SDClasses\AppConf;
/**
 * @var array $params
 */
?>
<div class="container-fluid">
	<br>

	<div class="alert alert-info">
		<strong><?= \User::getUserName( AppConf::getIns()->user ) ?></strong>, вы успешно авторизованы в
		системе.
		<a href="#" data-dismiss="alert" class="close">×</a>
	</div
	<div class="row-fluid">
		<div class="span6">
			<div class="widget-box">
				<div class="widget-title"><span class="icon"><i class="icon-signal"></i></span><h5>Общая
						статистика</h5>

					<div class="buttons"><a href="#" class="btn btn-mini"><i class="icon-refresh"></i> Обновить</a>
					</div>
				</div>
				<div class="widget-content">
					<div class="row-fluid">
						<div class="span4">
							<ul class="site-stats">
								<li><i class="icon-briefcase"></i> <strong>1433</strong>
									<small>Поставок за месяц</small>
								</li>
								<li><i class="icon-arrow-right"></i> <strong>16</strong>
									<small>Пришло вчера</small>
								</li>
								<li><i class="icon-arrow-left"></i> <strong>16</strong>
									<small>Выпущено вчера</small>
								</li>
								<li class="divider"></li>
								<li><i class="icon-repeat"></i> <strong>29</strong>
									<small>Ожидает обработки</small>
								</li>
							</ul>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="span6">
			<div class="widget-box">
				<div class="widget-title"><span class="icon"><i class="icon-comment"></i></span><h5>Последние
						Pre-Alert</h5><span title="88 всего" class="label label-info tip-left">88</span>
				</div>
				<div class="widget-content nopadding">
					<ul class="recent-comments">
						<li>
							<div class="comments">
								<span class="user-info">От: michael@apple.co.cn</span><br>
								<span class="user-info">Копия: ron@apple.nl</span>

								<p>
									<a href="#">Поставка запчастей</a>
								</p>
								<a href="#" class="btn btn-primary btn-mini">Посмотреть</a>
								<a href="#" class="btn btn-success btn-mini">Green light</a>
								<a href="#" class="btn btn-danger btn-mini">Red light</a>
							</div>
						</li>
						<li>
							<div class="comments">
								<span class="user-info">От: michael@apple.co.cn</span><br>
								<span class="user-info">Копия: ron@apple.nl</span>

								<p>
									<a href="#">Новые iPad, один вагон</a>
								</p>
								<a href="#" class="btn btn-primary btn-mini">Посмотреть</a>
								<a href="#" class="btn btn-success btn-mini">Green light</a>
								<a href="#" class="btn btn-danger btn-mini">Red light</a>
							</div>
						</li>
						<li>
							<div class="comments">
								<span class="user-info">От: michael@apple.co.cn</span><br>
								<span class="user-info">Копия: ron@apple.nl</span>

								<p>
									<a href="#">Iphone 5s, три самолета</a>
								</p>
								<a href="#" class="btn btn-primary btn-mini">Посмотреть</a>
								<a href="#" class="btn btn-success btn-mini">Green light</a>
								<a href="#" class="btn btn-danger btn-mini">Red light</a>
							</div>
						</li>
						<li class="viewall">
							<a title="View all comments" class="tip-top" href="#"> + Посмотреть все + </a>
						</li>
					</ul>
				</div>
			</div>
		</div>

	</div>
</div>
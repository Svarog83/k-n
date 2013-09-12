<?php
use SDClasses\AppConf;

?>
<div id="content">
	<div id="content-header">
		<h1>Панель управления</h1>

		<div class="btn-group">
			<a class="btn btn-large tip-bottom" title="Завести новую поставку"><i class="icon-file"></i></a>
			<a class="btn btn-large tip-bottom" title="Новых сообщений"><i class="icon-comment"></i><span
						class="label label-important">5</span></a>
		</div>
	</div>
	<div id="breadcrumb">
		<a href="#" title="Go to Home" class="tip-bottom"><i class="icon-home"></i> Главная</a>
		<a href="#" class="current">Панель</a>
	</div>
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
									<li><i class="icon-user"></i> <strong>1433</strong>
										<small>Пользователей</small>
									</li>
									<li><i class="icon-arrow-right"></i> <strong>16</strong>
										<small>Поставок пришло</small>
									</li>
									<li><i class="icon-arrow-left"></i> <strong>16</strong>
										<small>Поставок выпущено</small>
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
					<div class="widget-title"><span class="icon"><i class="icon-comment"></i></span><h5>Recent
							Comments</h5><span title="88 total comments" class="label label-info tip-left">88</span>
					</div>
					<div class="widget-content nopadding">
						<ul class="recent-comments">
							<li>
								<div class="user-thumb">
									<img width="40" height="40" alt="User" src="img/demo/av1.jpg"/>
								</div>
								<div class="comments">
									<span class="user-info"> User: michelle on IP: 172.10.56.3 </span>

									<p>
										<a href="#">Vivamus sed auctor nibh congue, ligula vitae tempus pharetra...</a>
									</p>
									<a href="#" class="btn btn-primary btn-mini">Edit</a> <a href="#"
									                                                         class="btn btn-success btn-mini">Approve</a>
									<a href="#" class="btn btn-warning btn-mini">Mark as spam</a> <a href="#"
									                                                                 class="btn btn-danger btn-mini">Delete</a>
								</div>
							</li>
							<li>
								<div class="user-thumb">
									<img width="40" height="40" alt="User" src="img/demo/av3.jpg"/>
								</div>
								<div class="comments">
									<span class="user-info"> User: john on IP: 192.168.24.3 </span>

									<p>
										<a href="#">Vivamus sed auctor nibh congue, ligula vitae tempus pharetra...</a>
									</p>
									<a href="#" class="btn btn-primary btn-mini">Edit</a> <a href="#"
									                                                         class="btn btn-success btn-mini">Approve</a>
									<a href="#" class="btn btn-warning btn-mini">Mark as spam</a> <a href="#"
									                                                                 class="btn btn-danger btn-mini">Delete</a>
								</div>
							</li>
							<li>
								<div class="user-thumb">
									<img width="40" height="40" alt="User" src="img/demo/av2.jpg"/>
								</div>
								<div class="comments">
									<span class="user-info"> User: neytiri on IP: 186.56.45.7 </span>

									<p>
										<a href="#">Vivamus sed auctor nibh congue, ligula vitae tempus pharetra...</a>
									</p>
									<a href="#" class="btn btn-primary btn-mini">Edit</a> <a href="#"
									                                                         class="btn btn-success btn-mini">Approve</a>
									<a href="#" class="btn btn-warning btn-mini">Mark as spam</a> <a href="#"
									                                                                 class="btn btn-danger btn-mini">Delete</a>
								</div>
							</li>
							<li class="viewall">
								<a title="View all comments" class="tip-top" href="#"> + View all + </a>
							</li>
						</ul>
					</div>
				</div>
			</div>

		</div>
		<div id="footer" class="span12">
			<?php echo '2013' . ( date( "Y" ) != '2013' ? '-' . date( "Y" ) : '' ) ?> &copy; SD development</a>
		</div>
	</div>
</div>
</div>
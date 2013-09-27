<?php

//AppConf::getIns()->debug_mode = true;

class Uploader {

/** @var char модуль*/
	public $module			= '';
/** @var int ID записи в модуле( comp_id для компаний и т.п. )*/
	public $docs_link_id	= '';
/** @var array фильтр файлов ( array('Image files (*.jpg)' => 'jpg','AutoCAD files (*.dxf)' => 'dxf'.... )*/
	public $filters			= array( array ( 'title' => 'Все файлы', 'extensions' => '*' ) );
/** @var string максимальный размер в МБ*/
	public $file_max_size	= '10mb';
/** @var string перенос строки для титлов HTML*/
	public $br				= "\n";
/** @var array @options настройки для формы загрузки файлов*/
	public $options = array();
/** @var string $targetDir путь до временной папки, куда сохраняются загруженные файлы*/
	private $tmpDir = '';
/** @var string $targetDir путь до папки(в директории docs), куда сохраняются загруженные файлы*/
	private $targetDir = '';

	function  __construct( $module, $docs_link_id, $options = array() )
	{
		$this->module		= $module;
	    $this->docs_link_id	= $docs_link_id;
	    $this->_oDB         = AutoLoader::DB();
		$this->tmpDir       = AppConf::getIns()->root_path . DIRECTORY_SEPARATOR .'tmp'. DIRECTORY_SEPARATOR . 'docs' . DIRECTORY_SEPARATOR;
		$this->targetDir    = AppConf::getIns()->root_path . DIRECTORY_SEPARATOR .'app/docs'. DIRECTORY_SEPARATOR . $this->module . DIRECTORY_SEPARATOR;

	    if ( isset ( $options['file_max_size'] ) )
			$this->file_max_size = $options['file_max_size'];
		if ( isset ( $options['filters'] ) && is_array( $options['filters'] ) )
			$this->filters = $options['filters'];

	}

	public function upload()
	{

?>
<link rel="stylesheet" href="/jquery/plupload/jquery.plupload.queue/css/jquery.plupload.queue.css" type="text/css" media="screen" />

<script type="text/javascript" src="/jquery/plupload/plupload.js"></script>
<script type="text/javascript" src="/jquery/plupload/plupload.flash.js"></script>
<script type="text/javascript" src="/jquery/plupload/plupload.html4.js"></script>
<script type="text/javascript" src="/jquery/plupload/plupload.html5.js"></script>

<?
		echo set_script_link( 'js', 'jquery_plupload_queue', 'jquery_plupload_queue' );
		echo set_script_link( 'js', 'ru', 'jquery_plupload_queue' );
		echo set_script_link( 'js', 'jquery_plupload_init', 'jquery_plupload_queue' );

?>

<script type="text/javascript">
	<!--

var UploadModule			  = '<?= $this->module ?>';
var UploadDocsLinkID      = '<?= $this->docs_link_id?>';
var UploadFileMaxSize = '<?= $this->file_max_size?>';
var UploadFilters     = <?= php2js( $this->filters ); ?>;
	//-->
</script>

<div id="uploader" style="width: 600px;">__**Ваш браузер не поддерживает загрузку.**__</div>
<br>
	<a id="clear" href="#">__**Очистить очередь**__</a>

<?php
	}

	public function save_file() {
		// HTTP headers for no cache etc
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");

		// Settings
		if ( !is_dir( $this->tmpDir) )
		{
			mkdir( $this->tmpDir );
		}

		//$cleanupTargetDir = false; // Remove old files
		//$maxFileAge = 60 * 60; // Temp file age in seconds

		// 5 minutes execution time
		@set_time_limit(5 * 60);

		// Uncomment this one to fake upload time
		// usleep(5000);

		// Get parameters
		$chunk    = isset($_REQUEST["chunk"]) ? $_REQUEST["chunk"] : 0;
		$chunks   = isset($_REQUEST["chunks"]) ? $_REQUEST["chunks"] : 0;
		$fileName = isset($_REQUEST["name"]) ? $_REQUEST["name"] : '';

		// Clean the fileName for security reasons
		$fileName = preg_replace('/[^\w\._]+/', '', $fileName);

		// Make sure the fileName is unique but only if chunking is disabled
		if ($chunks < 2 && file_exists( $this->tmpDir . DIRECTORY_SEPARATOR . $fileName)) {
			$ext = strrpos($fileName, '.');
			$fileName_a = substr($fileName, 0, $ext);
			$fileName_b = substr($fileName, $ext);

			$count = 1;
			while (file_exists($this->tmpDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b))
				$count++;

			$fileName = $fileName_a . '_' . $count . $fileName_b;
		}

		// Create target dir
		if (!file_exists( $this->tmpDir ) )
			@mkdir( $this->tmpDir);

		// Remove old temp files
		/* this doesn't really work by now

		if (is_dir( $this->tmpDir ) && ($dir = opendir( $this->tmpDir ))) {
			while (($file = readdir($dir)) !== false) {
				$filePath = $this->tmpDir . DIRECTORY_SEPARATOR . $file;

				// Remove temp files if they are older than the max age
				if (preg_match('/\\.tmp$/', $file) && (filemtime($filePath) < time() - $maxFileAge))
					@unlink($filePath);
			}

			closedir($dir);
		} else
			die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		*/

		// Look for the content type header
		if (isset($_SERVER["HTTP_CONTENT_TYPE"]))
			$contentType = $_SERVER["HTTP_CONTENT_TYPE"];

		if (isset($_SERVER["CONTENT_TYPE"]))
			$contentType = $_SERVER["CONTENT_TYPE"];

		// Handle non multipart uploads older WebKit versions didn't support multipart in HTML5
		if (strpos($contentType, "multipart") !== false) {
			if (isset($_FILES['file']['tmp_name']) && is_uploaded_file($_FILES['file']['tmp_name'])) {
				// Open temp file
				$out = fopen( $this->tmpDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");

				if ($out) {
					// Read binary input stream and append it to temp file
					$in = fopen($_FILES['file']['tmp_name'], "rb");

					if ($in) {
						while ($buff = fread($in, 4096))
							fwrite($out, $buff);
					} else
						die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
					fclose($in);
					fclose($out);

					@unlink($_FILES['file']['tmp_name']);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		} else {
			// Open temp file
			$out = fopen(  $this->tmpDir . DIRECTORY_SEPARATOR . $fileName, $chunk == 0 ? "wb" : "ab");
			if ($out) {
				// Read binary input stream and append it to temp file
				$in = fopen("php://input", "rb");

				if ($in) {
					while ($buff = fread($in, 4096))
						fwrite($out, $buff);
				} else
					die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');

				fclose($in);
				fclose($out);
			} else
				die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

		// Return JSON-RPC response
		die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');

	}

	public function save_attachments()
	{
		if ( !is_dir( $this->targetDir ) )
			mkdir( $this->targetDir );

		$arr_upload_name 		= isset ( $_REQUEST['upload_name'] ) ? $_REQUEST['upload_name'] : array();
		$arr_upload_comment 	= isset ( $_REQUEST['upload_comment'] ) ? $_REQUEST['upload_comment'] : '';
		$upload_size            = isset ( $_REQUEST['upload_size'] ) ? $_REQUEST['upload_size'] : '0';

		if ( is_array( $arr_upload_name ) )
		{
			foreach ( $arr_upload_name as $k => $v )
			{
				$docs_ext = strtolower( substr( strrchr( $arr_upload_name[ $k ], '.'), 1 ) );// работает быстрее стандартной функции php!
				$file_name = $k . '.' . $docs_ext;

				if ( is_file( $this->tmpDir . $file_name ) && rename ( $this->tmpDir . $file_name, $this->targetDir . $file_name ) )
				{
			/*
					if ( in_array( $docs_ext, array('jpg','jpeg','gif','png','tif','tiff') ) )
					{
						list( $width, $height, $type, $attr ) = getimagesize( $arr_upload_name[ $k ] );
					}
			*/
					$query = "
						INSERT INTO docs
						SET
							docs_activ		  = 'a',
							docs_mod		  = '". $this->module ."',
							docs_link_id	  = '". $this->docs_link_id ."',
							docs_default_name = '". $arr_upload_name[ $k ] ."',
							docs_name		  = '". $file_name ."',
							docs_title		  = '". ( isset ( $arr_upload_comment[ $k ] ) ? $arr_upload_comment[$k] : '' ) ."',
							docs_ext		  = '". $docs_ext ."',
							docs_size		  = '". $upload_size[ $k ] ."',
							docs_date		  = '". date('Y-m-j H:i:s') ."',
							docs_creator	  = '". AppConf::getIns()->user ."',
							docs_hash		  = '". substr( md5( time() . AppConf::getIns()->user ) , 0, 10 ) ."'
					";
					$this->_oDB->query( $query, __FILE__, __LINE__ );
				}
			}
		}
	}

	public function show(  ) {

	   $DocsArr = Uploader::getDocs( $this->module, $this->docs_link_id );

?>
<div id="upload_view">
	<table style="border-collapse: collapse; table-layout: fixed; width: 60%;" border="1">
		<col width="50px" align="left">
		<col width="100px" align="left">
		<col width="300px">
		<col align="left">
	<tr  class="gray">
		<th class="myeditable_all">__**№**__</th>
		<th>__**Дата документа**__</th>
		<th>__**Документ**__</th>
		<th>__**Описание документа**__</th>
	</tr>
	<?
		$step = 0;

		if( count ( $DocsArr ) )
		{
			foreach ( $DocsArr AS $row )
			{

	?>
	<tr>
		<td class="myeditable" id="td_<?= $row['docs_id'] ?>"><?= ++$step ?></td>
		<td><?= ( $row['day'] < 10 ? '0' : '' ) . $row['day'] .'.'. ( $row['month'] < 10 ? '0' : '' ) . $row['month'] .'.'. $row['year'] ?></td>
		<td>
			<a
				href  = "../choice/?module=choice&action=file_download&docs_id=<?= $row['docs_id'] ?>&docs_hash=<?= $row['docs_hash'] ?>&flag_return_buffer=1"
				title = "__**Открыть:**__<?= $this->br ?>__**Имя файла:**__ <?= $row['docs_default_name'] ?><?= $this->br ?>__**Размер:**__ <?= $row['docs_size'] ?>Kb<?= $this->br ?>Тип: <?= $row['docs_ext'] ?>"
			>
				<img src="/icon/docs_16x16/<?= substr( $row['docs_ext'], 0, 3 ) ?>.png" alt="<?= $row['docs_ext'] .' __**файл**__' ?>" width="16" height="16" border="0" onClick="self.focus();">
				<?= $row['docs_default_name'] ?>
			</a>
		</td>
		<td><?= str_replace("\n", '<br>', $row['docs_title']) ?></td>
	</tr>
	<?
			}
		}
		else
		{
	?>
	<tr>
		<td colspan="4">
			<br>
			<div class="info">__**Нет данных для отображения**__</div>
			<br>
		</td>
	</tr>
	<?
		}
	?>
	</table>
</div>



<?
	}

	public function edit( $show_close = false )
	{

		$g_form_id = 'form_id';
		$DocsArr = Uploader::getDocs( $this->module, $this->docs_link_id );


?>

<form method="post" action="/" name="form" id="<?= $g_form_id?>" enctype="multipart/form-data" onsubmit="return false;">
	<fieldset>
	<input type="hidden" name="todo"        value="attach_edit">
	<input type="hidden" name="docs_module" value="<?= $this->module ?>">
	<input type="hidden" name="link_id"     value="<?= $this->docs_link_id ?>">

	<div id="view_upload" style="width: 480px; height: 330px; display: block">
	<?php

	$this->upload();

	?>
	</div>
	<br><br><br>
	<table style="border-collapse: collapse; table-layout: fixed; width: 100%;" border="1">
		<col width="24px" align="left">
		<col width="50px" align="left">
		<col width="100px" align="left">
		<col width="300px">
		<col align="left">
	<tr class="gray">
		<th>
			<input type="Checkbox" id="DocsDeleteAll" title="__**Выделить/Снять выделение файлов для удаления**__">
		</th>
		<th class="myeditable_all" id="header">__**№**__</th>
		<th>__**Дата документа**__</th>
		<th>__**Документ**__</th>
		<th>__**Описание документа**__</th>
	</tr>
	<?
		$step = 0;

		if( count ( $DocsArr ) )
		{
			foreach ( $DocsArr AS $row )
			{

	?>
	<tr>
		<td>
			<input type="Checkbox" class="DocsDelete" id="docs_delete[<?= $row['docs_id'] ?>]" name="docs_delete[<?= $row['docs_id'] ?>]" title="__**Отметить для удаления**__" value="<?= $row['docs_id'] ?>">
		</td>

		<td class="myeditable" id="td_<?= $row['docs_id'] ?>"><?= ++$step ?></td>
		<td><?= ( $row['day'] < 10 ? '0' : '' ) . $row['day'] .'.'. ( $row['month'] < 10 ? '0' : '' ) . $row['month'] .'.'. $row['year'] ?></td>
		<td>
			<a
				href  = "../choice/?module=choice&action=file_download&docs_id=<?= $row['docs_id'] ?>&docs_hash=<?= $row['docs_hash'] ?>&flag_return_buffer=1"
				title = "__**Открыть:**__<?= $this->br ?>__**Имя файла:**__ <?= $row['docs_default_name'] ?><?= $this->br ?>__**Размер:**__ <?= $row['docs_size'] ?>Kb<?= $this->br ?>Тип: <?= $row['docs_ext'] ?>"
			>
				<img src="/icon/docs_16x16/<?= substr( $row['docs_ext'], 0, 3 ) ?>.png" alt="<?= $row['docs_ext'] .' __**файл**__' ?>" width="16" height="16" border="0" onClick="self.focus();">
				<?= $row['docs_default_name'] ?>
			</a>
		</td>
		<td>
			<input type="text" name="docs_title[<?= $row['docs_id']?>]" style="width:50%;" value="<?= $row['docs_title']?>">
		</td>
	</tr>
	<?
			}
		}
		else
		{
	?>
	<tr>
		<td colspan="5">
			<br>
			<div class="info">__**Нет данных для отображения**__</div>
			<br>
		</td>
	</tr>
	<?
		}
	?>
	</table>
</fieldset>
</form>


<?
		$options_arr = SaveFormOptions( $g_form_id, 'choice', 'attach_edit', array ( 'need_confirm' => false ) );
		$options_arr['show_close']      = $show_close;
		$options_arr['upload_exist']    = true;
		SaveForm( $options_arr );

	}

	public function save() {

		$avatarDir = $this->targetDir .'avatar'. DIRECTORY_SEPARATOR;

		$arr_docs_title  = isset($_REQUEST["docs_title"])  ? $_REQUEST["docs_title"] : array();
		$arr_docs_delete = isset($_REQUEST["docs_delete"]) ? $_REQUEST["docs_delete"] : array();

		if ( count( $arr_docs_title ) ) {

			foreach ( $arr_docs_title as $k => $v ) {

				$query = "
					UPDATE docs
					SET
						docs_title = '". mysql_real_escape_string( $v ) ."'
					WHERE
						docs_id = '". $k ."'
				";
				$this->_oDB->query( $query, __FILE__, __LINE__ );
			}
		}

		if ( count ( $arr_docs_delete ) )
		{

			$query = "
				SELECT * FROM docs
				WHERE
					docs_id		IN('". implode("','", $arr_docs_delete ) ."')
			";
			$oResult = $this->_oDB->query( $query, __FILE__, __LINE__ );

			while( $row = $oResult->get_fetch_ass() ) {

				if( file_exists( $this->targetDir . $row['docs_name'] ) )
					unlink( $this->targetDir . $row['docs_name'] );
				if( file_exists( $avatarDir . $row['docs_name'] ) )
					unlink( $avatarDir . $row['docs_name'] );
			}

			$query = "
				UPDATE docs
				SET
					docs_activ	= 'del'
				WHERE
					docs_id		IN('". implode("','", $arr_docs_delete ) ."')
			";
			$this->_oDB->query( $query, __FILE__, __LINE__ );
		}
	}

	public static function getDocs( $module, $docs_link_id )
	{
		$DB = AutoLoader::DB();
		$DocsArr = array();

		$query = "
			SELECT
				d.*,
				DAY( d.docs_date ) as day,
				MONTH( d.docs_date ) as month,
				YEAR( d.docs_date ) as year
			FROM docs d
			WHERE
			    d.docs_activ = 'a' &&
			    d.docs_mod   = ?s &&
			    d.docs_link_id  = ?s
			ORDER BY
			    d.docs_ext,d.docs_title
		";

		$DocsArr = $DB->getAll( $query, $module, $docs_link_id );

		return $DocsArr;
	}
}
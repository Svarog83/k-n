<?php

namespace SDClasses;

class View
{

	/**
	 * @var string
	 */
	protected $_path = '';

	/**
	 * @param $path
	 * @param array $params
	 */
	public function __construct( $path, $params = array() )
	{
		if ( !is_string( $path ) )
		{
			trigger_error( 'Wrong path ' . $path . ' in View constructor' );
		}

		if ( is_array( $params ) && count( $params ) )
		{
			if ( !isset ( $params['module'] ) || $params['module'] == 'Resources' )
				$this->_path = '../app/Resources/views/' . $params['view'] . '.php';
			else if ( !empty ( $params['module'] ) && !empty ( $params['view'] ) )
			{
				$this->_path = AppConf::getIns()->root_path . '/src/' . $params['module'] . 'Bundle' . '/views/' . $params['view'] . 'View.php';
			}
		}
		else
		{
			$this->_path = $path;
		}
		$AC = AppConf::getIns();
		$AC->_view = $this;
	}

	/**
	 * @param array $params
	 */
	public function render( $params = array() )
	{
		if ( file_exists( $this->_path ) )
			require( $this->_path );
		else
			trigger_error( 'View path ' . $this->_path . ' does not exist!' );
	}

	/**
	 * @param array $params
	 * @return string
	 */
	public function renderView( $params = array() )
	{
		if ( file_exists( $this->_path ) )
		{
			ob_start();

			require( $this->_path );

			$str = ob_get_clean();
			ob_end_clean();

			return $str;
		}
		else
			trigger_error( 'View path ' . $this->_path . ' does not exist!' );
	}

	/**
	 * @param $heading
	 * @param $text
	 * @param string $status
	 * @param array $options
	 * @return string
	 */
	public function showNotif( $heading, $text, $status = 'alert-info', $options = array() )
	{
		$def_options = array(
			'id' => '',
			'onclick' => '',
			'title' => '',
		);

		$options = array_merge( $def_options, $options );

		ob_start();
		?>
		<div class="alert <?= $status ?> alert-block">
			<a class="close" data-dismiss="alert" href="#">×</a>
			<h4 class="alert-heading"><?= $heading ?>!</h4>
			<?= $text ?>
		</div>
		<?
		$str = ob_get_clean();
		return $str;
	}

	/**
	 * @param $heading
	 * @param $class
	 * @param $icon
	 * @param array $options
	 * @return string
	 */
	public function showButton( $heading, $class, $icon, $options = array() )
	{
		$def_options = array(
			'id' => '',
			'submit' => false,
			'onclick' => '',
			'title' => '',
			'disabled' => false
		);

		$options = array_merge( $def_options, $options );

		ob_start();
		?>
		<button <?= !empty ( $options['id'] ) ? 'id="' . $options['id'] . '"' : '' ?>
				class="btn <?= $class ?>">
			<i class="<?= $icon ?> icon-white"></i> <?= $heading ?>
		</button>
		<?
		$str = ob_get_clean();
		return $str;
	}

	/**
	 * @param $heading
	 * @param $text
	 * @param array $options
	 * @return string
	 */
	public function showWarnNotif( $heading, $text, $options = array() )
	{
		return $this->showNotif( $heading, $text, '', $options );
	}

	/**
	 * @param $heading
	 * @param $text
	 * @param array $options
	 * @return string
	 */
	public function showSuccessNotif( $heading, $text, $options = array() )
	{
		return $this->showNotif( $heading, $text, 'alert-success', $options );
	}

	/**
	 * @param $heading
	 * @param $text
	 * @param array $options
	 * @return string
	 */
	public function showErrorNotif( $heading, $text, $options = array() )
	{
		return $this->showNotif( $heading, $text, 'alert-error', $options );
	}

	public function showEntry( $title, $content, $options = array() )
	{
		$def_options = array(
			'id' => '',
			'onclick' => '',
			'title' => '',
		);

		$options = array_merge( $def_options, $options );

		ob_start();
		?>
		<div class="control-group">
			<label class="control-label sd_entry_label"><?= $title?></label>

			<div class="controls sd_content">
				<?= $content ?>
			</div>
		</div>
		<?
		$str = ob_get_clean();
		return $str;
	}

	/**
		 * @param string $field_name - name of the field
		 * @param string $value - default value for the field
		 * @param array $arr - options
		 * @return string
		 */
		public function showText( $field_name, $value, $arr = array( ) )
		{
			return '<input
	            type="' . ( !empty( $arr['password'] ) ? 'password' : 'text' ) . '"
	            name="' . $field_name . '"
	            size="' . ( !empty( $arr['size'] ) ? $arr['size'] : '16' ) . '"
	            value="' . $value . '"
	            ' . ( !empty( $arr['onchange'] ) ? ' onChange="' . $arr['onchange'] . '"' : '' ) .
			       ( isset ( $arr['id'] ) && $arr['id'] ? ' id="' . $arr['id'] . '"' : '' ) .
			       ( isset ( $arr['validation'] ) && $arr['validation'] ? ' class="' . $arr['validation'] . '"' : '' ) .
			       ( isset ( $arr['equalTo'] ) && $arr['equalTo'] ? ' equalTo="' . $arr['equalTo'] . '"' : '' ) .
			       ( isset ( $arr['minlength'] ) && $arr['minlength'] ? ' minlength="' . $arr['minlength'] . '"' : '' ) .
				   ( isset ( $arr['maxlength'] ) && $arr['maxlength'] ? ' maxlength="' . $arr['maxlength'] . '"' : '' ) .
				   ( isset ( $arr['min'] ) && $arr['min'] ? ' min="' . $arr['min'] . '"' : '' ) .
				   ( isset ( $arr['max'] ) && $arr['max'] ? ' max="' . $arr['max'] . '"' : '' ) .
			       ( isset ( $arr['onfocus'] ) && $arr['onfocus'] ? ' onFocus="' . $arr['onfocus'] . '"' : '' ) .
			       ( isset ( $arr['onblur'] ) && $arr['onblur'] ? ' onBlur="' . $arr['onblur'] . '"' : '' ) .
			       ( isset ( $arr['add_str'] ) && $arr['add_str'] ? $arr['add_str'] : '' ) . '>' .
			       ( isset ( $arr['add_html'] ) && $arr['add_html'] ? $arr['add_html'] : '' ) . '';
		}

}
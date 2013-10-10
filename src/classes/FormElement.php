<?php
/**
 * Created by JetBrains PhpStorm.
 * User: SVetko
 * Date: 10.10.13
 * Time: 17:49
 * To change this template use File | Settings | File Templates.
 */

namespace SDClasses;


class FormElement
{

	public $type = '';
	public $title = '';
	public $field_name = '';
	public $value;
	public $options = array();

	public function __construct( $type, $title, $field, $value, $options = array() )
	{
		$this->type = $type;
		$this->title = $title;
		$this->field_name = $field;
		$this->value = $value;
		$this->options = $options;
	}

	/**
	 * @param \SDClasses\Form $form
	 */
	public function process( $form )
	{
		switch ( $this->type )
		{
			case 'hidden':
			{
				?>
				<input type="hidden" name="<?= $this->field_name ?>" value="<?= $this->value ?>">
				<?
				break;
			}

			case 'text':
			{
				echo $form->showTextBlock( $this->title, $this->field_name, $this->value, $this->options );
				break;
			}

			case 'select':
			{
				echo $form->showSelectBlock( $this->title, $this->field_name, $this->value, $this->options );
				break;
			}
			case 'radio':
			{
				echo $form->showRadioBlock( $this->title, $this->field_name, $this->value, $this->options );
				break;
			}
			case 'checkbox':
			{
				echo $form->showCheckBoxesBlock( $this->title, $this->field_name, $this->value, $this->options );
				break;
			}
			case 'textarea':
			{
				echo $form->showTextAreaBlock( $this->title, $this->field_name, $this->value, $this->options );
				break;
			}
			case 'empty':
			{
				echo $form->showEmptyBlock( $this->title, $this->options );
				break;
			}

			default:
			{
				trigger_error( 'unknown type ' . $this->type . ' of FormElement' );
			}
		}
	}

}
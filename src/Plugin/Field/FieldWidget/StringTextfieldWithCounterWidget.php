<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextfieldWidget;

/**
 * Plugin implementation of the 'string_textfield_with_counter' widget.
 *
 * @FieldWidget(
 *   id = "string_textfield_with_counter",
 *   label = @Translation("Textfield with counter"),
 *   field_types = {
 *     "string"
 *   }
 * )
 */
class StringTextfieldWithCounterWidget extends StringTextfieldWidget
{
	use TextFieldCounterWidgetTrait;

	/**
	 * {@inheritdoc}
	 */
	public static function defaultSettings()
	{
		return [
			'counter_position' => 'after',
		] + parent::defaultSettings();
	}

	/**
	 * {@inheritdoc}
	 */
	public function settingsForm(array $form, FormStateInterface $form_state)
	{
		$form = parent::settingsForm($form, $form_state);

		$this->addCounterPositionSettingsFormElement($form, $this->getSetting('counter_position'));

		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function settingsSummary()
	{
		$summary = parent::settingsSummary();
	
		$summary[] = $this->addPositionSummary($this->getSetting('counter_position'));

		return $summary;
	}

	/**
	* {@inheritdoc}
	*/
	public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
	{
		$element = parent::formElement($items, $delta, $element, $form, $form_state);

		$entity = $items->getEntity();
		$field_defintion = $items->getFieldDefinition();
		$maxlength = $this->getFieldSetting('max_length');
		$position = $this->getSetting('counter_position');
		$this->addFieldFormElement($element['value'], $entity, $field_defintion, $delta, $maxlength, $position);

		return $element;
	}
}

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

		$form['counter_position'] = [
			'#type' => 'select',
			'#title' => $this->t('Counter position'),
			'#options' => [
				'before' => $this->translateValue('before'),
				'after' => $this->translateValue('after'),
			],
			'#default_value' => $this->getSetting('counter_position'),
		];

		return $form;
	}

	/**
	 * {@inheritdoc}
	 */
	public function settingsSummary()
	{
		$summary = parent::settingsSummary();
	
		$maxlength = $this->getSetting('maxlength');
		$summary[] = $this->t('Counter position: @position', ['@position' => $this->translateValue($this->getSetting('counter_position'))]);

		return $summary;
	}

	/**
	* {@inheritdoc}
	*/
	public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
	{
		$element = parent::formElement($items, $delta, $element, $form, $form_state);

		$entity = $items->getEntity();
		$keys = [$entity->getEntityTypeId()];
		$keys[] = $entity->id() ? $entity->id() : 0;
		$keys[] = str_replace('.', '--', $items->getFieldDefinition()->id());
		$keys[] = 'string-textfield-with-counter';
		$keys[] = $delta;

		$key = implode('-', $keys);

		$element['value']['#attributes']['class'][] = $key;
		$element['value']['#attributes']['class'][] = 'textfield-counter-element';

		$element['#attached']['library'][] = 'textfield_counter/counter';
		$field_definition_id = str_replace('.', '--', $items->getFieldDefinition()->id());
		$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['key'][] = $key;
		$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['maxlength'] = (int) $this->getFieldSetting('max_length');
		$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['counterPosition'] = $this->getSetting('counter_position');

		return $element;
	}

	/**
	 * A unified translation function to translate values provided by this
	 * module.
	 *
	 * @param string $value
	 *   The key of the item to be translated
	 *
	 * @return string
	 *   The translated value
	 */
	private function translateValue($value) {
		$values = [
			'before' => $this->t('Before'),
			'after' => $this->t('After'),
		];

		return $values[$value];
	}
}

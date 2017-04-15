<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\text\Plugin\Field\FieldWidget\TextfieldWidget;

/**
 * Plugin implementation of the 'text_textfield_with_counter' widget.
 *
 * @FieldWidget(
 *   id = "text_textfield_with_counter",
 *   label = @Translation("Text field with counter"),
 *   field_types = {
 *     "text"
 *   },
 * )
 */
class TextfieldWithCounterWidget extends TextfieldWidget
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
		$keys[] = 'text-textfield-with-counter';
		$keys[] = $delta;

		$key = implode('-', $keys);

		$element['#attributes']['class'][] = $key;
		$element['#attributes']['class'][] = 'textfield-counter-element';

		$element['#attached']['library'][] = 'textfield_counter/counter';
		$field_definition_id = str_replace('.', '--', $items->getFieldDefinition()->id());
		$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['key'][$delta] = $key;
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

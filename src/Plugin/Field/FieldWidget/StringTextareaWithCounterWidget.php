<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Field\Plugin\Field\FieldWidget\StringTextareaWidget;

/**
 * Plugin implementation of the 'string_textarea_with_counter' widget.
 *
 * @FieldWidget(
 *   id = "string_textarea_width_counter",
 *   label = @Translation("Text area (multiple rows) with counter"),
 *   field_types = {
 *     "string_long"
 *   }
 * )
 */
class StringTextareaWithCounterWidget extends StringTextareaWidget
{
	/**
	 * {@inheritdoc}
	 */
	public static function defaultSettings()
	{
		return [
			'maxlength' => 0,
			'counter_position' => 'after',
		] + parent::defaultSettings();
	}

	/**
	 * {@inheritdoc}
	 */
	public function settingsForm(array $form, FormStateInterface $form_state)
	{
		$form = parent::settingsForm($form, $form_state);

		$form['maxlength'] = [
			'#type' => 'number',
			'#title' => $this->t('Maximum number of characters'),
			'#min' => 0,
			'#default_value' => $this->getSetting('maxlength'),
			'#description' => $this->t('Setting this value to zero will disable the counter'),
		];

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
		$summary[] = $this->t('Maximum number of characters: @count', ['@count' => ($maxlength ? $maxlength : $this->t('Disabled'))]);
		if($maxlength)
		{
			$summary[] = $this->t('Counter position: @position', ['@position' => $this->translateValue($this->getSetting('counter_position'))]);
		}

		return $summary;
	}

	/**
	* {@inheritdoc}
	*/
	public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state)
	{
		$element = parent::formElement($items, $delta, $element, $form, $form_state);

		if($this->getSetting('maxlength'))
		{
			$entity = $items->getEntity();
			$keys = [$entity->getEntityTypeId()];
			$keys[] = $entity->id() ? $entity->id() : 0;
			$keys[] = str_replace('.', '--', $items->getFieldDefinition()->id());
			$keys[] = 'string-textarea-with-counter';
			$keys[] = $delta;

			$key = implode('-', $keys);

			$element['value']['#attributes']['class'][] = $key;
			$element['value']['#attributes']['class'][] = 'textfield-counter-element';
			$element['value']['#element_validate'][] = [get_class($this), 'validateElement'];
			$element['value']['#textfield-maxlength'] = $this->getSetting('maxlength');

			$element['#attached']['library'][] = 'textfield_counter/counter';
			$field_definition_id = str_replace('.', '--', $items->getFieldDefinition()->id());
			$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['key'][] = $key;
			$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['maxlength'] = (int) $this->getSetting('maxlength');
			$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['counterPosition'] = $this->getSetting('counter_position');
		}

		return $element;
	}

	public static function validateElement(array $element, FormStateInterface $form_state)
	{
		$input_exists = FALSE;
		$value = NestedArray::getValue($form_state->getValues(), $element['#parents'], $input_exists);
		if(strlen($value) > $element['#textfield-maxlength'])
		{
			$form_state->setError($element, t('@name cannot be longer than %max characters but is currently %length characters long.', ['@name' => $element['#title'], '%max' => $element['#textfield-maxlength'], '%length' => strlen($value)]));
		}		
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

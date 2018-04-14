<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

trait TextFieldCounterWidgetTrait
{
	use StringTranslationTrait;

	public function addMaxlengthSettingsFormElement(&$form, $maxlength)
	{
		$form['maxlength'] = [
			'#type' => 'number',
			'#title' => $this->t('Maximum number of characters'),
			'#min' => 0,
			'#default_value' => $maxlength,
			'#description' => $this->t('Setting this value to zero will disable the counter'),
		];
	}

	public function addCounterPositionSettingsFormElement(&$form, $position)
	{
		$form['counter_position'] = [
			'#type' => 'select',
			'#title' => $this->t('Counter position'),
			'#options' => [
				'before' => $this->translateValue('before'),
				'after' => $this->translateValue('after'),
			],
			'#default_value' => $position,
		];
	}

	public function addMaxlengthSummary($maxlength)
	{
		return $this->t('Maximum number of characters: @count', ['@count' => ($maxlength ? $maxlength : $this->t('Disabled'))]);
	}

	public function addPositionSummary($position)
	{
		return $this->t('Counter position: @position', ['@position' => $this->translateValue($position)]);
	}

	public function addFieldFormElement(&$element, $entity, $fieldDefinition, $delta, $maxlength, $position)
	{
		$keys = [$entity->getEntityTypeId()];
		$keys[] = $entity->id() ? $entity->id() : 0;
		$field_definition_id = str_replace('.', '--', $fieldDefinition->id());
		$keys[] = $field_definition_id;
		$keys[] = $delta;

		$key = implode('-', $keys);

		$element['#attributes']['class'][] = $key;
		$element['#attributes']['class'][] = 'textfield-counter-element';

		$element['#attached']['library'][] = 'textfield_counter/counter';
		$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['key'][$delta] = $key;
		$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['maxlength'] = (int) $maxlength;
		$element['#attached']['drupalSettings']['textfieldCounter'][$field_definition_id]['counterPosition'] = $position;
	}

	public static function validateFieldFormElement(array $element, FormStateInterface $form_state, $maxlength)
	{
		$input_exists = FALSE;
		$value = NestedArray::getValue($form_state->getValues(), $element['#parents'], $input_exists);
		$value = is_array($value) ? $value['value'] : $value;
		$parts = explode(PHP_EOL, $value);
		$newline_count = count($parts) - 1;
		$value_length = mb_strlen($value) - $newline_count;
		if($value_length > $element['#textfield-maxlength'])
		{
			$form_state->setError($element, t('@name cannot be longer than %max characters but is currently %length characters long.', ['@name' => $element['#title'], '%max' => $element['#textfield-maxlength'], '%length' => $value_length]));
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
	private function translateValue($value)
	{
		$values = [
			'before' => $this->t('Before'),
			'after' => $this->t('After'),
		];

		return $values[$value];
	}
}

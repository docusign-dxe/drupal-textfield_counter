<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

use Drupal\Component\Utility\NestedArray;
use Drupal\Component\Utility\Unicode;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Field\FieldDefinitionInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\StringTranslation\StringTranslationTrait;

/**
 * Textfield counter trait. Adds textfield counting functionality.
 */
trait TextFieldCounterWidgetTrait {

  use StringTranslationTrait;

  /**
   * Adds a form element to set the maximum number of characters allowed.
   *
   * @param array $form
   *   The form render array to which the element should be added.
   * @param int $defaultValue
   *   The default value for the form element.
   */
  public function addMaxlengthSettingsFormElement(array &$form, $defaultValue) {
    $form['maxlength'] = [
      '#type' => 'number',
      '#title' => $this->t('Maximum number of characters'),
      '#min' => 0,
      '#default_value' => $defaultValue,
      '#description' => $this->t('Setting this value to zero will disable the counter on textareas.'),
    ];
  }

  /**
   * Adds a form element to set the position of the text counter.
   *
   * @param array $form
   *   The form render array to which the element should be added.
   * @param string $position
   *   Where the counter should be located in relation to the textfield. Allowed
   *   values are:
   *   - above
   *   - below.
   */
  public function addCounterPositionSettingsFormElement(array &$form, $position) {
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

  /**
   * Returns the summary of the maximum number of allowed characters.
   *
   * @param int $maxlength
   *   The maximum length allowed for the field.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The max length summary, translated.
   */
  public function addMaxlengthSummary($maxlength) {
    return $this->t('Maximum number of characters: @count', ['@count' => ($maxlength ? $maxlength : $this->t('Disabled'))]);
  }

  /**
   * Returns the summary of the position of the textfield counter.
   *
   * @param string $position
   *   Where the counter should be located in relation to the textfield. Allowed
   *   values are:
   *   - above
   *   - below.
   *
   * @return \Drupal\Core\StringTranslation\TranslatableMarkup
   *   The position summary, translated.
   */
  public function addPositionSummary($position) {
    return $this->t('Counter position: @position', ['@position' => $this->translateValue($position)]);
  }

  /**
   * Sets up the form element with the textfield counter.
   *
   * @param array $element
   *   The render array for the element to which files are being attached.
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity to which the field is attached.
   * @param |Drupal\Core\Field\FieldDefinitionInterface $fieldDefinition
   *   The field definition.
   * @param int $delta
   *   The delta (index) of the current item.
   * @param int $maxlength
   *   The maximum length of the textfield.
   * @param string $position
   *   Where the counter should be located in relation to the textfield. Allowed
   *   values are:
   *   - above
   *   - below.
   */
  public function addFieldFormElement(array &$element, EntityInterface $entity, FieldDefinitionInterface $fieldDefinition, $delta, $maxlength, $position) {
    $keys = [$entity->getEntityTypeId()];
    $keys[] = $entity->id() ? $entity->id() : 0;
    if (method_exists($fieldDefinition, 'id')) {
      $field_definition_id = str_replace('.', '--', $fieldDefinition->id());
    }
    else {
      $field_definition_id = "{$entity->getEntityTypeId()}--{$entity->getType()}--{$fieldDefinition->getName()}";
    }
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

  /**
   * Validates the field for the maximum number of characters.
   *
   * @param array $element
   *   The render array for the element to which fiels are being attached.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The Drupal form state.
   * @param int $maxlength
   *   The maximum allowed text length against which the field should be
   *   validated.
   */
  public static function validateFieldFormElement(array $element, FormStateInterface $form_state, $maxlength) {
    $input_exists = FALSE;
    $value = NestedArray::getValue($form_state->getValues(), $element['#parents'], $input_exists);
    $value = is_array($value) ? $value['value'] : $value;
    $parts = explode(PHP_EOL, $value);
    $newline_count = count($parts) - 1;
    $value_length = Unicode::strlen($value) - $newline_count;
    if ($value_length > $element['#textfield-maxlength']) {
      $form_state->setError($element, t(
        '@name cannot be longer than %max characters but is currently %length characters long.',
        [
          '@name' => $element['#title'],
          '%max' => $element['#textfield-maxlength'],
          '%length' => $value_length,
        ]
      ));
    }
  }

  /**
   * A unified translation function to translate values provided by this module.
   *
   * @param string $value
   *   The key of the item to be translated.
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

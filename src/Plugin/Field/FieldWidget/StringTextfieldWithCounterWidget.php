<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

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
class StringTextfieldWithCounterWidget extends StringTextfieldWidget {

  use TextFieldCounterWidgetTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'use_field_maxlength' => 0,
      'maxlength' => 0,
      'counter_position' => 'after',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $form = parent::settingsForm($form, $form_state);

    $this->addMaxlengthSettingsFormElement($form, $this->getSetting('maxlength'), TRUE);
    $this->addCounterPositionSettingsFormElement($form, $this->getSetting('counter_position'), TRUE);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $maxlength = $this->getSetting('maxlength');
    $summary['maxlength'] = $this->addMaxlengthSummary($maxlength);
    if ($maxlength || $this->getSetting('use_field_maxlength')) {
      $summary['counter_position'] = $this->addPositionSummary($this->getSetting('counter_position'));
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $use_field_maxlength = $this->getSetting('use_field_maxlength');
    $maxlength = $use_field_maxlength ? $this->getFieldSetting('max_length') : $this->getSetting('maxlength');
    if ($maxlength) {
      $entity = $items->getEntity();
      $field_defintion = $items->getFieldDefinition();
      $position = $this->getSetting('counter_position');
      $this->fieldFormElement($element['value'], $entity, $field_defintion, $delta, $maxlength, $position);
      $element['value']['#textfield-maxlength'] = $maxlength;
      $classes = class_uses($this);
      if (count($classes)) {
        $element['#element_validate'][] = [array_pop($classes), 'validateFieldFormElement'];
      }
    }

    return $element;
  }

}

<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

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
class TextfieldWithCounterWidget extends TextfieldWidget {

  use TextFieldCounterWidgetTrait;

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'maxlength' => 0,
      'counter_position' => 'after',
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $form = parent::settingsForm($form, $form_state);

    $this->addMaxlengthSettingsFormElement($form, $this->getSetting('maxlength'));
    $this->addCounterPositionSettingsFormElement($form, $this->getSetting('counter_position'));

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary['maxlength'] = $this->addMaxlengthSummary($this->getSetting('maxlength'));
    $summary['counter_position'] = $this->addPositionSummary($this->getSetting('counter_position'));

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $entity = $items->getEntity();
    $field_defintion = $items->getFieldDefinition();
    $maxlength = $this->getFieldSetting('max_length');
    if ($this->getSetting('maxlength')) {
      $maxlength = min($maxlength, $this->getSetting('maxlength'));
    }
    $position = $this->getSetting('counter_position');
    $this->addFieldFormElement($element, $entity, $field_defintion, $delta, $maxlength, $position);
    $element['value']['#textfield-maxlength'] = $maxlength;
    $classes = class_uses($this);
    $element['value']['#element_validate'][] = [array_pop($classes), 'validateFieldFormElement'];

    return $element;
  }

}

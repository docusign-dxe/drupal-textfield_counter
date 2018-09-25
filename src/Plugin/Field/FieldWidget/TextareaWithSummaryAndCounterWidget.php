<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\text\Plugin\Field\FieldWidget\TextareaWithSummaryWidget;

/**
 * Plugin implementation of the 'text_textarea_with_summary_and_counter' widget.
 *
 * @FieldWidget(
 *   id = "text_textarea_with_summary_and_counter",
 *   label = @Translation("Textarea with a summary and counter"),
 *   field_types = {
 *     "text_with_summary"
 *   }
 * )
 */
class TextareaWithSummaryAndCounterWidget extends TextareaWithSummaryWidget {

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

    $maxlength = $this->getSetting('maxlength');
    $summary[] = $this->addMaxlengthSummary($maxlength);
    if ($maxlength) {
      $summary[] = $this->addPositionSummary($this->getSetting('counter_position'));
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    if ($this->getSetting('maxlength')) {
      $entity = $items->getEntity();
      $field_defintion = $items->getFieldDefinition();
      $maxlength = $this->getSetting('maxlength');
      $position = $this->getSetting('counter_position');
      $this->fieldFormElement($element, $entity, $field_defintion, $delta, $maxlength, $position);
      $element['#textfield-maxlength'] = $maxlength;
      $classes = class_uses($this);
      $element['#element_validate'][] = [array_pop($classes), 'validateFieldFormElement'];
    }

    return $element;
  }

}

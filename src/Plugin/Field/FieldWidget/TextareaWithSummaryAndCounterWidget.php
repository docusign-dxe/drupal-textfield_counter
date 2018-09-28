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
      'js_prevent_submit' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {

    $form = parent::settingsForm($form, $form_state);

    $this->addMaxlengthSettingsFormElement($form);
    $this->addCounterPositionSettingsFormElement($form);
    $this->addJsPreventSubmitSettingsFormElement($form);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {

    $summary = parent::settingsSummary();

    $summary['maxlength'] = $this->addMaxlengthSummary();
    if ($this->getSetting('maxlength')) {
      $summary['counter_position'] = $this->addPositionSummary();
      $summary['js_prevent_submit'] = $this->addJsSubmitPreventSummary();
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    if ($maxlength = $this->getSetting('maxlength')) {
      $entity = $items->getEntity();
      $field_defintion = $items->getFieldDefinition();
      $this->fieldFormElement($element, $entity, $field_defintion, $delta);
      if (isset($element['value'])) {
        $element['value']['#textfield-maxlength'] = $maxlength;
      }
      $element['#textfield-maxlength'] = $maxlength;
      $classes = class_uses($this);
      if (count($classes)) {
        $element['#element_validate'][] = [array_pop($classes), 'validateFieldFormElement'];
      }
    }

    return $element;
  }

}

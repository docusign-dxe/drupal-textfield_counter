<?php

namespace Drupal\textfield_counter\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\text\Plugin\Field\FieldWidget\TextareaWidget;

/**
 * Plugin implementation of the 'text_textarea_with_counter' widget.
 *
 * @FieldWidget(
 *   id = "text_textarea_with_counter",
 *   label = @Translation("Textarea (multiple rows) with counter"),
 *   field_types = {
 *     "text_long"
 *   }
 * )
 */
class TextareaWithCounterWidget extends TextareaWidget {

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

    $summary[] = $this->addMaxlengthSummary();
    if ($this->getSetting('maxlength')) {
      $summary[] = $this->addPositionSummary();
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

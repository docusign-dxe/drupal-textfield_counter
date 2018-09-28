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
 *   label = @Translation("Textfield with counter"),
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
      'use_field_maxlength' => 0,
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

    $this->addMaxlengthSettingsFormElement($form, TRUE);
    $this->addCounterPositionSettingsFormElement($form, TRUE);
    $this->addJsPreventSubmitSettingsFormElement($form, TRUE);

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = parent::settingsSummary();

    $summary['maxlength'] = $this->addMaxlengthSummary();
    if ($this->getSetting('maxlength') || $this->getSetting('use_field_maxlength')) {
      $summary['counter_position'] = $this->addPositionSummary();
    }

    if ($this->getSetting('maxlength') && !$this->getSetting('use_field_maxlength')) {
      $summary['js_prevent_submit'] = $this->addJsSubmitPreventSummary();
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {

    $element = parent::formElement($items, $delta, $element, $form, $form_state);

    $maxlength = $this->getSetting('use_field_maxlength') ? $this->getFieldSetting('max_length') : $this->getSetting('maxlength');
    if ($maxlength) {
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

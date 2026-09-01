<?php

declare(strict_types=1);

namespace Drupal\coffee_shortcut\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configures the keyboard shortcut that opens Coffee.
 */
final class CoffeeShortcutSettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId(): string {
    return 'coffee_shortcut_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames(): array {
    return ['coffee_shortcut.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state): array {
    $config = $this->config('coffee_shortcut.settings');
    $custom = $config->get('custom_shortcut') ?? [];

    $form['block_default_shortcut'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Block the default alt + D shortcut'),
      '#description' => $this->t('Coffee normally opens with alt + D, which overrides the browser shortcut that uses the same keys. Enable this to free alt + D for the browser. Coffee still opens with alt + K.'),
      '#default_value' => $config->get('block_default_shortcut'),
    ];

    $form['custom_shortcut'] = [
      '#type' => 'fieldset',
      '#title' => $this->t('Custom shortcut'),
      '#description' => $this->t('Bind an additional shortcut that opens Coffee. Leave the key blank to skip this.'),
    ];
    $form['custom_shortcut']['alt'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Alt'),
      '#default_value' => $custom['alt'] ?? FALSE,
    ];
    $form['custom_shortcut']['shift'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Shift'),
      '#default_value' => $custom['shift'] ?? FALSE,
    ];
    $form['custom_shortcut']['ctrl'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Ctrl'),
      '#default_value' => $custom['ctrl'] ?? FALSE,
    ];
    $form['custom_shortcut']['meta'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Meta (Cmd on Mac, Windows key on PC)'),
      '#default_value' => $custom['meta'] ?? FALSE,
    ];
    $form['custom_shortcut']['key'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Key'),
      '#description' => $this->t('A single letter or digit, for example D.'),
      '#default_value' => $custom['key'] ?? '',
      '#maxlength' => 1,
      '#size' => 2,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state): void {
    parent::validateForm($form, $form_state);

    $key = trim((string) $form_state->getValue('key'));
    if ($key !== '' && !preg_match('/^[a-zA-Z0-9]$/', $key)) {
      $form_state->setErrorByName('key', $this->t('The key must be a single letter or digit.'));
      return;
    }

    $is_plain_alt_d = $key !== ''
      && strtolower($key) === 'd'
      && $form_state->getValue('alt')
      && !$form_state->getValue('shift')
      && !$form_state->getValue('ctrl')
      && !$form_state->getValue('meta');

    if ($is_plain_alt_d && $form_state->getValue('block_default_shortcut')) {
      $form_state->setErrorByName('key', $this->t('Alt + D is blocked above, so it cannot also be used as the custom shortcut. Add another modifier or choose a different key.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state): void {
    $this->config('coffee_shortcut.settings')
      ->set('block_default_shortcut', (bool) $form_state->getValue('block_default_shortcut'))
      ->set('custom_shortcut', [
        'alt' => (bool) $form_state->getValue('alt'),
        'shift' => (bool) $form_state->getValue('shift'),
        'ctrl' => (bool) $form_state->getValue('ctrl'),
        'meta' => (bool) $form_state->getValue('meta'),
        'key' => strtolower(trim((string) $form_state->getValue('key'))),
      ])
      ->save();

    parent::submitForm($form, $form_state);
  }

}

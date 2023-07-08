<?php

namespace Drupal\br50_cfg\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure site settings for branch 50.
 */
class Br50SiteSettingsForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'br50_site.settings';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'br50_site_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      static::SETTINGS,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // Get the config.
    $config = $this->config(static::SETTINGS);

    // The operating hours element.
    $form['operating_hours'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Operating hour'),
      '#cols' => 60,
      '#rows' => 5,
      '#format' => $config->get('operating_hours')['format'] ?? 'full_html',
      '#default_value' => $config->get('operating_hours')['value'] ?? NULL,
    ];

    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {

    // Get the values from the form state.
    $values = $form_state->getValues();

    // Set the config.
    $this->config(static::SETTINGS)
      ->set('operating_hours', $values['operating_hours'])
      ->save();

    // Clear all caches so the new site settings up.
    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

}
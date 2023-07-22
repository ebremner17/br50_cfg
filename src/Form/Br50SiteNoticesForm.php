<?php

namespace Drupal\br50_cfg\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure site notices for branch 50.
 */
class Br50SiteNoticesForm extends ConfigFormBase {

  /**
   * Config settings.
   *
   * @var string
   */
  const SETTINGS = 'br50_site.notices';

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'br50_site_notices';
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

    // The notice type.
    $form['notice_type'] = [
      '#type' => 'select',
      '#title' => $this->t('Notice type'),
      '#options' => [
        'status' => $this->t('Status'),
        'notice' => $this->t('Notice'),
        'cancellation' => $this->t('Cancellation'),
      ],
      '#default_value' => $config->get('notice_type') ?? NULL,
    ];

    $form['notice_text'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Notice text'),
      '#cols' => 60,
      '#rows' => 5,
      '#format' => $config->get('header_text')['format'] ?? 'full_html',
      '#default_value' => $config->get('notice_text')['value'] ?? NULL,
    ];

    $form['show_notice'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Show notice'),
      '#default_value' => $config->get('show_notice') ?? NULL,
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
      ->set('notice_type', $values['notice_type'])
      ->set('notice_text', $values['notice_text'])
      ->set('show_notice', $values['show_notice'])
      ->save();

    // Clear all caches so the new site settings up.
    drupal_flush_all_caches();

    parent::submitForm($form, $form_state);
  }

}

<?php

namespace Drupal\popup_zip\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure popup_zip settings for this site.
 */
class SettingsForm extends ConfigFormBase {

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'popup_zip_settings';
    }

    /**
     * {@inheritdoc}
     */
    protected function getEditableConfigNames() {
        return ['popup_zip.settings'];
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['telegram_bot_name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Telegram bot system name'),
            '#required' => true,
            '#default_value' => $this->config('popup_zip.settings')->get('telegram_bot_name'),
        ];
        $form['telegram_pass'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Registration password'),
            '#required' => true,
            '#default_value' => $this->config('popup_zip.settings')->get('telegram_pass'),
        ];
        return parent::buildForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        parent::validateForm($form, $form_state);
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {
        $this->config('popup_zip.settings')
                ->set('telegram_bot_name', $form_state->getValue('telegram_bot_name'))
                ->save();
        $this->config('popup_zip.settings')
                ->set('telegram_pass', $form_state->getValue('telegram_pass'))
                ->save();        
        parent::submitForm($form, $form_state);
    }

}

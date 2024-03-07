<?php

namespace Drupal\popup_zip\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\popup_zip\StartCommand;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a popup_zip form.
 */
class SendProblemForm extends FormBase {

    protected $session;

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'popup_zip_send_problem';
    }

// Pass the dependency to the object constructor
    public function __construct(PrivateTempStoreFactory $temp_store_factory) {
        $request = \Drupal::request();
        $this->session = $request->getSession();
    }

    // Uses Symfony's ContainerInterface to declare dependency to be passed to constructor
    public static function create(ContainerInterface $container) {
        return new static(
                $container->get('tempstore.private')
        );
    }

    protected function loadSelectFromVoc(string $vocabularyName) {
        // Create empty options array
        $options = array();

// Taxonomy vocabulary machine name
        $taxonomy = $vocabularyName;

// Get all the taxonomy terms from the vocabulary
        $tax_items = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadTree($taxonomy);

// Inject into the options array
        foreach ($tax_items as $tax_item) {
            $options[$tax_item->tid] = $tax_item->name;
        }
        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
        $form['appliance'] = array(
            '#type' => 'select',
            '#title' => $this->t('Select the appliance you want to repair?'),
            '#options' => $this->loadSelectFromVoc('categories_on_front'),
            '#required' => TRUE,
        );

        $form['brand'] = array(
            '#type' => 'select',
            '#title' => $this->t('Select the of ther appliance'),
            '#options' => $this->loadSelectFromVoc('categories_on_front'),
            '#required' => TRUE,
        );

        $form['name'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Name'),
            '#required' => TRUE,
        ];

        $form['phone'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Phone'),
            '#required' => TRUE,
        ];

        $form['message'] = [
            '#type' => 'textarea',
            '#title' => $this->t('Describe the problem'),
            '#required' => TRUE,
        ];

        $form['actions'] = [
            '#type' => 'actions',
            '#required' => TRUE,
        ];
        $form['actions']['submit'] = [
            '#type' => 'submit',
            '#value' => $this->t('Send'),
        ];

        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function validateForm(array &$form, FormStateInterface $form_state) {
        
    }

    /**
     * {@inheritdoc}
     */
    public function submitForm(array &$form, FormStateInterface $form_state) {

        $botName = $this->config('popup_zip.settings')->get('telegram_bot_name');

        if (!$botName)
            return;

        $telegram_bot = \Drupal::entityTypeManager()->getStorage('telegram_bot')->load($botName);
        /** @var \Telegram\Bot\Api $telegram */
        $telegram = \Drupal::service('drupal_telegram_sdk.bot_api')->getTelegram($telegram_bot);
        $zip = $this->session->get('zip');

        $data = $form_state->getValues();

        $brandTerm = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($data['brand']);
        $brand = $brandTerm->name->value;

        $applianceTerm = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->load($data['appliance']);
        $appliance = $applianceTerm->name->value;

        if ($telegram) {
            $data_result = array(
                'name' => '<b>Name:</b> ' . $data['name'],
                //   'email' => '<b>Email:</b> ' . $data['email'] ,
                'phone' => '<b>Phone:</b> ' . $data['phone'],
                'zip' => '<b>ZIP:</b> ' . $zip,
                'message' => '<b>Message:</b> ' . $data['message'],
                'appliance' => '<b>Appliance:</b> ' . $appliance,
                'brand' => '<b>Brand:</b> ' . $brand
            );

            $text = join(",\n", $data_result);

            $query = \Drupal::entityQuery('tg_chat');
            $ids = $query->execute();
            $storage = \Drupal::service('entity_type.manager')->getStorage('tg_chat');
            $chats = $storage->loadMultiple($ids);

            foreach ($chats as $chat) {
                $chatId = $chat->get('chat_id')->getString();
                $telegram->sendMessage([
                    'chat_id' => $chatId,
                    'parse_mode' => 'HTML',
                    'text' => $text,
                ]);
            }
        }
        
    }

}

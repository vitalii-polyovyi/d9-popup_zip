<?php

namespace Drupal\popup_zip\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Component\Serialization\Json;
// Use for Ajax.
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\Core\Ajax\CssCommand;
use Drupal\Core\Ajax\CloseModalDialogCommand;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a Popup zip form.
 */
class ZipForm extends FormBase {

    protected $session;

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

    /**
     * {@inheritdoc}
     */
    public function getFormId() {
        return 'popup_zip_questions';
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(array $form, FormStateInterface $form_state) {
// Disable caching on this form.
        $form_state->setCached(FALSE);

        $form['zip'] = [
            '#type' => 'textfield',
            '#required' => TRUE,
            '#prefix' => '<div id="zipfield">',
            '#suffix' => '</div>',
            '#attributes' => array('placeholder' => t('Your ZIP'), 'class' => array('form-control')),
        ];

// Placeholder to put the result of Ajax call, setMessage().
        $form['err'] = [
            '#type' => 'markup',
            '#markup' => '<div class="result_message"></div>',
        ];

        $form['submit'] = [
            '#type' => 'submit',
            '#ajax' => [
                'callback' => '::ajaxValidateZip',
            ],
            '#value' => $this->t('Send'),
        ];

        return $form;
    }

    public function ajaxValidateZip(array $form, FormStateInterface $form_state) {
        $cssRed = ['border' => '1px solid red'];
        $cssGray = ['border' => '1px solid gray'];

        $zip = '';
        if ($form_state->hasValue('zip')) {
            $zip = $form_state->getValue('zip');
        }

        $response = new AjaxResponse();

        if ($this->isValidZipCode($zip) && $this->zipInList($zip)) {
            $this->session->set('zip', $zip);
            $response->addCommand(new CssCommand('#zipfield input', $cssGray));
            $response->addCommand(
                    new HtmlCommand(
                            '.result_message',
                            '<div class="my_message"></div>')
            );
            $command = new CloseModalDialogCommand();
            $response->addCommand($command);
            return $response;
        } else {
            $response->addCommand(new CssCommand('#zipfield input', $cssRed));
            //           $tmpZip = $this->tempStore->get('zip');
            $response->addCommand(
                    new HtmlCommand(
                            '.result_message',
                            '<div class="my_message">' . t('Incorrect zip') . '</div>')
            );
        }

        return $response;
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
        
    }

    public function isValidZipCode($zipCode) {
        return (preg_match('/^[0-9]{5}(-[0-9]{4})?$/', $zipCode)) ? true : false;
    }

    public function zipInList(string $zip): bool {
        $zips = ['35004' => '36925',
            '99501' => '99950',
            '85001' => '86556',
            '71601' => '72959',
            '90001' => '96162',
            '80001' => '81658',
            '06001' => '06928',
            '19701' => '19980',
            '32003' => '34997',
            '30002' => '39901',
            '96701' => '96898',
            '83201' => '83877',
            '60001' => '62999',
            '46001' => '47997',
            '50001' => '52809',
            '66002' => '67954',
            '40003' => '42788',
            '70001' => '71497',
            '03901' => '04992',
            '20588' => '21930',
            '01001' => '05544',
            '48001' => '49971',
            '55001' => '56763',
            '38601' => '39776',
            '63001' => '65899',
            '59001' => '59937',
            '68001' => '69367',
            '88901' => '89883',
            '03031' => '03897',
            '07001' => '08989',
            '87001' => '88439',
            '00501' => '14925',
            '27006' => '28909',
            '58001' => '58856',
            '43001' => '45999',
            '73001' => '74966',
            '97001' => '97920',
            '15001' => '19640',
            '02801' => '02940',
            '29001' => '29945',
            '57001' => '57799',
            '37010' => '38589',
            '73301' => '88595',
            '84001' => '84791',
            '05001' => '05907',
            '20101' => '24658',
            '98001' => '99403',
            '24701' => '26886',
            '53001' => '54990',
            '82001' => '83414',
            '96910' => '96932',
            '96898' => '96898',
            '96913' => '96931',
            '96950' => '96952',
            '96898' => '96898',
            '00601' => '00767',
            '00801' => '00851',
            '96898' => '96898'];

        foreach ($zips as $from => $to) {
            if (in_array(intval($zip), range(intval($from), intval($to)))) {
                return true;
            }
        }
        return false;
    }

}

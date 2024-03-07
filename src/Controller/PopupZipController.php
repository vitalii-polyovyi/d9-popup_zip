<?php

namespace Drupal\popup_zip\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\OpenModalDialogCommand;

/**
 * Returns responses for popup_zip routes.
 */
class PopupZipController extends ControllerBase {

    public function openZipModal() {
        $request = \Drupal::request();
        $session = $request->getSession();
        $zip = $session->get('zip');

        if (intval($zip) > 0) {
            //zip allready present, don't show popup
            $response = new AjaxResponse();
            return $response;
        }

        $response = new AjaxResponse();
        $modal_form = $this->formBuilder()->getForm('Drupal\popup_zip\Form\ZipForm');
        $options = [
            'width' => '25%',
        ];
        $response->addCommand(new OpenModalDialogCommand('', $modal_form, $options));
        return $response;
    }

}

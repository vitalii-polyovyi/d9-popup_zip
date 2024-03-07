<?php

namespace Drupal\popup_zip\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use \SplString;
use Drupal\Component\Serialization\Json;

/**
 * Provides a questionsformpopupblock block.
 *
 * @Block(
 *   id = "popup_zip_questionsformpopupblock",
 *   admin_label = @Translation("QuestionsFormPopupBlock"),
 *   category = @Translation("Unava")
 * )
 */
class QuestionsFormPopupBlock extends BlockBase implements ContainerFactoryPluginInterface {

    /**
     * Constructs a new QuestionsFormPopupBlock instance.
     *
     * @param array $configuration
     *   The plugin configuration, i.e. an array with configuration values keyed
     *   by configuration option name. The special key 'context' may be used to
     *   initialize the defined contexts by setting it to an array of context
     *   values keyed by context names.
     * @param string $plugin_id
     *   The plugin_id for the plugin instance.
     * @param mixed $plugin_definition
     *   The plugin implementation definition.
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition) {
        parent::__construct($configuration, $plugin_id, $plugin_definition);
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
        return new static(
                $configuration,
                $plugin_id,
                $plugin_definition
        );
    }

    /**
     * {@inheritdoc}
     */
    public function blockSubmit($form, FormStateInterface $form_state) {
        $this->configuration['foo'] = $form_state->getValue('foo');
    }

    /**
     * {@inheritdoc}
     */
    public function build() {
        $form = [];
        $form['markup'] = [
            '#type' => 'markup',
            '#markup' => '<div id="drupal-modal"></div>'
        ];
        $form['#attached']['library'][] = 'popup_zip/popup_zip';

        return $form;
    }

}

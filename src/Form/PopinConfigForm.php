<?php

namespace Drupal\popin\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Config\ConfigManagerInterface;

/**
 * Class PopinConfigForm.
 */
class PopinConfigForm extends ConfigFormBase {

  /**
   * Drupal\Core\Config\ConfigManagerInterface definition.
   *
   * @var \Drupal\Core\Config\ConfigManagerInterface
   */
  protected $configManager;
  /**
   * Constructs a new PopinConfigForm object.
   */
  public function __construct(
    ConfigFactoryInterface $config_factory,
      ConfigManagerInterface $config_manager
    ) {
    parent::__construct($config_factory);
        $this->configManager = $config_manager;
  }

  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('config.factory'),
            $container->get('config.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'popin.popinconfig',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'popin_config_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $config = $this->config('popin.popinconfig');
    $form['titre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Titre'),
      '#description' => $this->t('Titre de la popin'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('titre'),
    ];
    $form['sous_titre'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Sous-titre'),
      '#description' => $this->t('Sous-titre de la popin'),
      '#maxlength' => 128,
      '#size' => 64,
      '#default_value' => $config->get('sous_titre'),
    ];
    $form['description'] = [
      '#type' => 'text_format',
      '#title' => $this->t('Description'),
      '#default_value' => $config->get('description'),
    ];
    $form['texte_cta'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Texte CTA'),
      '#description' => $this->t('Sera affiché sur le bouton'),
      '#maxlength' => 64,
      '#size' => 64,
      '#default_value' => $config->get('texte_cta'),
    ];
    $form['lien_cta'] = [
      '#type' => 'url',
      '#title' => $this->t('Lien CTA'),
      '#default_value' => $config->get('lien_cta'),
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
    parent::submitForm($form, $form_state);

    $this->config('popin.popinconfig')
      ->set('titre', $form_state->getValue('titre'))
      ->set('sous_titre', $form_state->getValue('sous_titre'))
      ->set('description', $form_state->getValue('description'))
      ->set('texte_cta', $form_state->getValue('texte_cta'))
      ->set('lien_cta', $form_state->getValue('lien_cta'))
      ->save();
  }

}

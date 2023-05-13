<?php

namespace Drupal\glossary_tooltip\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;

class glossary_tooltipForm extends FormBase
{

  public function getFormid()
  {
    return 'glossary_tooltip_form';
  }

  public function buildform(array $form, FormStateInterface $form_state)
  {
    $conn = Database::getConnection();
    $record = [];
    if (isset($_GET['id'])) {
      $query = $conn->select('glossary_tooltip', 'm')->condition('id', $_GET['id'])->fields('m');
      $record = $query->execute()->fetchAssoc();
    }
    echo "Title";
    $form['title'] = ['#title' => $this->t('Title'), '#type' => 'textfield', '#required' => TRUE, '#default_value' => (isset($record['title']) && $_GET['id']) ? $record['title'] : '',];
    echo "Description";
    $form['description'] = ['#title' => $this->t('Description'), '#type' => 'textfield', '#required' => TRUE, '#default_value' => (isset($record['description']) && $_GET['id']) ? $record['description'] : '',];
    $form['action'] = ['#type' => 'action',];

    $form['action']['submit'] = ['#type' => 'submit', '#value' => t('Save'),];

    $link = Url::fromUserInput('/glossary_tooltip/');

    $form['action']['cancel'] = ['#markup' => Link::fromTextAndUrl(t('Go back'), $link, ['
  attributes' => ['class' => 'button']])->toString(),];
    return $form;
  }

  public function validateForm(array &$form, FormStateInterface $form_state)
  {
    $title = $form_state->getValue('title');

    if (preg_match('/[^A-Z[ ]a-z]/', $title)) {
      $form_state->setErrorByName('title', $this->t('title must be in Characters Only'));
    }

    parent::validateForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state)
  {
    $field = $form_state->getValues();

    $title = $field['title'];
    $description = $field['description'];

    if (isset($_GET['id'])) {
      $field = ['title' => $title, 'description' => $description,];

      $query = \Drupal::database();
      $query->update('glossary_tooltip')->fields($field)->condition('id', $_GET['id'])->execute();
      $this->messenger()->addMessage('Successfully added to glossary');
    } else {
      $field = ['title' => $title, 'description' => $description,];
      $query = \Drupal::database();
      $query->insert('glossary_tooltip')->fields($field)->execute();
      $this->messenger()->addMessage('Successfully added to glossary');
    }

    $form_state->setRedirect('glossary_tooltip.glossary_tooltip_controller_listing');
  }
}
<?php

/**
 * @file
 * Install, update and uninstall functions for the glossary_tooltip module.
 */

namespace Drupal\glossary_tooltip\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Database;
use Drupal\Core\Url;
use Drupal\Core\Messenger;
use Drupal\Core\Link;


class glossary_tooltipController extends ControllerBase
{

  public function Listing()
  {

    // table apache_request_headers
    $header_table = ['id' => t('ID'), 'title' => t('Title'), 'description' => t('Description'), 'opt' => t('Edit'), '
    opt1' => t('Delete'),];
    $row = [];
    $conn = Database::getConnection();
    $query = $conn->select('glossary_tooltip', 'm');
    $query->fields('m', ['id', 'title', 'description']);
    $result = $query->execute()->fetchAll();

    foreach ($result as $value) {
      $delete = Url::fromUserInput('/glossary_tooltip/form/delete/' . $value->id);
      $edit = Url::fromUserInput('/glossary_tooltip/form/data?id=' . $value->id);

      $row[] = ['id' => $value->id, 'title' => $value->title, 'description' => $value->description, 'opt' => Link::fromTextAndUrl('Edit', $edit)->toString(), 'opt1' => Link::fromTextAndUrl('Delete', $delete)->toString(),];
    }
    $add = Url::fromUserInput('/glossary_tooltip/form/data');
    $text = "Add to glossary";

    $data['table'] = ['#type' => 'table', '#header' => $header_table, '#rows' => $row, '#empty' => t('
    No record found'), '#caption' => Link::fromTextAndUrl($text, $add)->toString(),];

    $this->messenger()->addMessage('Glossary List');
    return $data;
  }
}
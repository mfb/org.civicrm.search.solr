<?php

require_once 'CRM/Core/Form.php';
require_once 'SolrPhpClient/Apache/Solr/Service.php';

class CRM_Solr_Form_SolrSearch extends CRM_Core_Form {
  public function buildQuickForm() {
    $this->add('text',
      'search',
      ts('Search terms')
    );
    $this->addButtons(array(
      array(
        'type' => 'submit',
        'name' => 'Search',
        'isDefault' => TRUE,
      ),
    ));
  }

  public function postProcess( ) {
    $values = $this->exportValues();
    $solr = new Apache_Solr_Service('localhost', 8080, '/solr');
    if ($values['search'] && $solr->ping()) {
      $offset = 0;
      $limit = 10;
      $params = array();
      $response = $solr->search('description:' . $solr->escape($values['search']), $offset, $limit, $params);
      foreach ($response->response->docs as $doc) {
        $id = str_replace('contact:', '', $doc->getField('id'));
        $id = $id['value'];
        $title = $doc->getField('title');
        $title = $title['value'];
        $results .= '<li><a href="/civicrm/contact/view?reset=1&cid=' . $id . '">' . $title . '</a></li>';
      }
// Some code to inject contacts into Solr:
//      $sql = 'SELECT id FROM civicrm_contact LIMIT 2';
//      $dao = CRM_Core_DAO::executeQuery($sql);
//      while ($dao->fetch()) {
//        $params = array('id' => $dao->id);
//        $contact = CRM_Contact_BAO_Contact::retrieve($params);
//        $debugMessage .= print_r($contact, TRUE);
//        $document = new Apache_Solr_Document();
//        $document->id = 'contact:' . $dao->id;
//        $document->title = $dao->display_name;
//        $document->description = "{$dao->first_name} {$dao->middle_name} {$dao->last_name} {$dao->email}";
//        $documents[] = $document;
//        if (count($documents) > 500) {
//          $solr->addDocuments($documents);
//          $solr->commit();
//          $documents = array();
//        }
//      }
//      $solr->addDocuments($documents);
//      $solr->commit();
//      $solr->optimize();
    }
    CRM_Utils_System::setTitle(ts('Solr Search'));
    $this->assign('results', $results);
  }
}

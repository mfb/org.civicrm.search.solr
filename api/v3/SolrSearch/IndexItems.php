<?php

/**
 * SolrSearch.IndexItems API specification (optional)
 * This is used for documentation and validation.
 *
 * @param array $spec description of fields supported by this API call
 * @return void
 * @see http://wiki.civicrm.org/confluence/display/CRM/API+Architecture+Standards
 */
function _civicrm_api3_solr_search_indexitems_spec(&$spec) {
  $spec['magicword']['api.required'] = 1;
}

/**
 * SolrSearch.IndexItems API
 *
 * @param array $params
 * @return array API result descriptor
 * @see civicrm_api3_create_success
 * @see civicrm_api3_create_error
 * @throws API_Exception
 */
function civicrm_api3_solr_search_indexitems($params) {
  if (array_key_exists('magicword', $params) && $params['magicword'] == 'sesame') {
    $returnValues = array( // OK, return several data rows
      12 => array('id' => 12, 'name' => 'Twelve'),
      34 => array('id' => 34, 'name' => 'Thirty four'),
      56 => array('id' => 56, 'name' => 'Fifty six'),
    );
    // ALTERNATIVE: $returnValues = array(); // OK, success
    // ALTERNATIVE: $returnValues = array("Some value"); // OK, return a single value

    // Spec: civicrm_api3_create_success($values = 1, $params = array(), $entity = NULL, $action = NULL)
    $solr = new Apache_Solr_Service('localhost', 8080, '/solr');
    $sql = 'SELECT id FROM civicrm_contact LIMIT 2';
    $dao = CRM_Core_DAO::executeQuery($sql);
    while ($dao->fetch()) {
      $params = array('id' => $dao->id);
      $contact = CRM_Contact_BAO_Contact::retrieve($params);
      $debugMessage .= print_r($contact, TRUE);
      $document = new Apache_Solr_Document();
      $document->id = 'contact:' . $dao->id;
      $document->title = $dao->display_name;
      $document->description = "{$dao->first_name} {$dao->middle_name} {$dao->last_name} {$dao->email}";
      $documents[] = $document;
      if (count($documents) > 500) {
        $solr->addDocuments($documents);
        $solr->commit();
        $documents = array();
      }
    }
    $solr->addDocuments($documents);
    $solr->commit();
    $solr->optimize();


    return civicrm_api3_create_success($returnValues, $params, 'NewEntity', 'NewAction');
  } else {
    throw new API_Exception(/*errorMessage*/ 'Everyone knows that the magicword is "sesame"', /*errorCode*/ 1234);
  }
}


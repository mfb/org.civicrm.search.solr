<?php
// This file declares a managed database record of type "Job".
// The record will be automatically inserted, updated, or deleted from the
// database as appropriate. For more details, see "hook_civicrm_managed" at:
// http://wiki.civicrm.org/confluence/display/CRMDOC42/Hook+Reference
return array (
  0 => 
  array (
    'name' => 'Cron:SolrSearch.IndexItems',
    'entity' => 'Job',
    'params' => 
    array (
      'version' => 3,
      'name' => 'Call SolrSearch.IndexItems API',
      'description' => 'Call SolrSearch.IndexItems API',
      'run_frequency' => 'Hourly',
      'api_entity' => 'SolrSearch',
      'api_action' => 'IndexItems',
      'parameters' => '',
    ),
  ),
);
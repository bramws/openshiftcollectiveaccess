<?php
/** ---------------------------------------------------------------------
 * support/utils/synchronizeData.php
 * ----------------------------------------------------------------------
 * CollectiveAccess
 * Open-source collections management software
 * ----------------------------------------------------------------------
 *
 * Software by Whirl-i-Gig (http://www.whirl-i-gig.com)
 * Copyright 2011 Whirl-i-Gig
 *
 * For more information visit http://www.CollectiveAccess.org
 *
 * This program is free software; you may redistribute it and/or modify it under
 * the terms of the provided license as published by Whirl-i-Gig
 *
 * CollectiveAccess is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTIES whatsoever, including any implied warranty of 
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  
 *
 * This source code is free and modifiable under the terms of 
 * GNU General Public License. (http://www.gnu.org/copyleft/gpl.html). See
 * the "license.txt" file for details, or visit the CollectiveAccess web site at
 * http://www.CollectiveAccess.org
 * 
 * @package CollectiveAccess
 * @subpackage models
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License version 3
 * 
 * ----------------------------------------------------------------------
 */
 
 /**
   *
   */
   require_once("../../setup.php");
   require_once(__CA_LIB_DIR__.'/core/Configuration.php');
   require_once(__CA_LIB_DIR__.'/core/Datamodel.php');
   require_once(__CA_LIB_DIR__.'/ca/Service/RestClient.php');
   require_once(__CA_MODELS_DIR__.'/ca_lists.php');
   
   
   	$va_processed_records = array();
   
	$o_config = Configuration::load(__CA_CONF_DIR__."/synchronization.conf");
	$o_dm = Datamodel::load();
	
	$va_sources = $o_config->getAssoc('sources');
	foreach($va_sources as $vn_i => $va_source) {
	
		$vs_base_url 						= $va_source['baseUrl'];
		$vs_search_expression 		= $va_source['searchExpression'];
		$vs_username 						= $va_source['username'];
		$vs_password 						= $va_source['password'];
		$vs_table 								= $va_source['table'];
		if(!$vs_base_url){
			print "ERROR: You must pass a valid CollectiveAccess\n";
			exit(-1);
		}
		
		if (!($t_instance = $o_dm->getInstanceByTableName($vs_table, true))) {
			die("Invalid table '{$vs_table}'\n");
		}
		
		
		//
		// Set up HTTP client for REST calls
		//
		$o_client = new RestClient($vs_base_url."/service.php/search/Search/rest");
		
		
		//
		// Authenticate
		//
		$o_res = $o_client->auth($vs_username, $vs_password)->get();
			if (!$o_res->isSuccess()) {
				die("Could not authenticate to service for authentication\n");
			}
		
		//
		// Get userID
		//
		$o_res = $o_client->getUserID()->get();
			if (!$o_res->isSuccess()) {
				die("Could not fetch user_id\n");
			}
			
		$o_res = $o_client->queryRest($vs_table, $vs_search_expression)->get();
		
		
		//parse results
			$vs_pk = $t_instance->primaryKey();
			$va_items = array();
			$o_xml = $o_res->CaSearchResult;
			foreach($o_xml->children() as $vn_i => $o_item) {
				$o_attributes = $o_item->attributes();
				$vn_id = (int)$o_attributes->{$vs_pk};
				
				$vs_idno = (string)$o_item->idno;
				$vs_label = (string)$o_item->ca_labels->ca_label[0];
				
				$va_items[$vs_table.'/'.$vn_id] = array(
					'table' => $vs_table,
					'id' => $vn_id,
					'idno' => $vs_idno
				);
			}
			
			//print_R($va_items);
		
			// Ok... now fetch and import each
			$o_client->setUri($vs_base_url."/service.php/iteminfo/ItemInfo/rest");
			fetchAndImport($va_items, $o_client, $va_source, array());
	}
	# ------------------------------------------------------------------------------------------
	function fetchAndImport($pa_item_queue, $po_client, $pa_config, $pa_tables) {
		if (!is_array($pa_tables)) { $pa_tables = array(); }
		global $va_processed_records;
		
		$vs_base_url = $pa_config['baseUrl'];
		$o_dm = Datamodel::load();
		$t_locale = new ca_locales();
		$t_list = new ca_lists();
		foreach($pa_item_queue as $vn_i => $va_item) {
			$vs_table = $va_item['table'];
			$vn_id = $va_item['id'];
			if (!$vn_id) { print "SKIP CAUSE NO ID\n"; continue; }
			if(isset($va_processed_records[$vs_table.'/'.$vn_id])) { continue; }
			
			$vs_idno = $va_item['idno'];
			
			$o_xml = $po_client->getItem($vs_table, $vn_id)->get();
			$o_item = $o_xml->getItem;
			
			$t_instance = $o_dm->getInstanceByTableName($vs_table, true);
			// Look for existing record
			$vb_skip = false;
			if ($t_instance->load(array('idno' => $vs_idno))) {
				print "SKIPPED $vs_idno 'Cause it already exists\n";
				$vb_skip = true;
			} else {
				$vn_type_id = $t_instance->getTypeIDForCode($o_item->type_id);
				
				if (!$vn_type_id) { print "NO TYPE FOR $vs_table/".$o_item->type_id."\n"; }
				// create new one
				$t_instance->clear();
				$t_instance->setMode(ACCESS_WRITE);
				$t_instance->set('type_id', $vn_type_id);
				$t_instance->set('idno', $vs_idno);
				
				// add intrinsics
				
				$t_instance->insert();
				
				if ($t_instance->numErrors()) {
					print "ERROR inserting record: ".join('; ', $t_instance->getErrors())."\n";
				}
				
				// add attributes
				$va_codes = $t_instance->getApplicableElementCodes();
				
				foreach($va_codes as $vs_code) {
					$t_element = $t_instance->_getElementInstance($vs_code);
					
					switch($t_element->get('datatype')) {
						case 0:		// container
							$va_elements = $t_element->getElementsInSet();
							
							$o_attr = $o_item->{'ca_attribute_'.$vs_code};
							foreach($o_attr as $va_tag => $o_tags) {
								foreach($o_tags as $vs_locale => $o_values) {
									if (!($vn_locale_id = $t_locale->localeCodeToID($vs_locale))) { $vn_locale_id = null; }
									$va_container_data = array('locale_id' => $vn_locale_id);
									foreach($o_values as $o_value) {
									
										foreach($va_elements as $vn_i => $va_element_info) {
											if ($va_element_info['datatype'] == 0) { continue; }	
									
											if ($vs_value = trim((string)$o_value->{$va_element_info['element_code']})) {
												switch($va_element_info['datatype']) {
													case 3:	//list
														$va_tmp = explode(":", $vs_value);		//<item_id>:<item_idno>
														//print "CONTAINER LIST CODE=".$va_tmp[1]."/$vs_value/".$va_element_info['list_id']."\n";
														$va_container_data[$va_element_info['element_code']] = $t_list->getItemIDFromList($va_element_info['list_id'], $va_tmp[1]);
														break;
													default:
														$va_container_data[$va_element_info['element_code']] = $vs_value;
														break;
												}
											}
										}
										
										$t_instance->addAttribute(
												$va_container_data,
												$vs_code);
									}
								}
							}
							break;
						case 3:		// list
							$o_attr = $o_item->{'ca_attribute_'.$vs_code};
							foreach($o_attr as $va_tag => $o_tags) {
								foreach($o_tags as $vs_locale => $o_values) {
									if (!($vn_locale_id = $t_locale->localeCodeToID($vs_locale))) { $vn_locale_id = null; }
									foreach($o_values as $o_value) {
										if ($vs_value = trim((string)$o_value->{$vs_code})) {
											$va_tmp = explode(":", $vs_value);		//<item_id>:<item_idno>
											
											if ($vn_item_id = $t_list->getItemIDFromList($t_element->get('list_id'), $va_tmp[1])) {
												$t_instance->addAttribute(
													array(
														$vs_code => $vn_item_id,
														'locale_id' => $vn_locale_id
													),
													$vs_code);
											}
											//print "LIST CODE=".$va_tmp[1]."\n";
										}
									}
								}
							}
							break;
						default:
							$o_attr = $o_item->{'ca_attribute_'.$vs_code};
							foreach($o_attr as $va_tag => $o_tags) {
								foreach($o_tags as $vs_locale => $o_values) {
									if (!($vn_locale_id = $t_locale->localeCodeToID($vs_locale))) { $vn_locale_id = null; }
									foreach($o_values as $o_value) {
										if ($vs_value = trim((string)$o_value->{$vs_code})) {
										$t_instance->addAttribute(
											array(
												$vs_code => $vs_value,
												'locale_id' => $vn_locale_id
											),
											$vs_code);
										}
									}
								}
							}
							
							break;
					}
				}
				$t_instance->update();
				
				if ($t_instance->numErrors()) {
					print "ERROR adding attributes to record: ".join('; ', $t_instance->getErrors())."\n";
				}
				
				// get label fields
				$va_label_data = array();
				foreach($t_instance->getLabelUIFields() as $vs_field) {
					$va_label_data[$vs_field] = $o_item->preferred_labels->en_US->{$vs_field};			
				}
				
				$t_instance->addLabel(
					$va_label_data, 1, null, true
				);
				if ($t_instance->numErrors()) {
					print "ERROR adding label: ".join('; ', $t_instance->getErrors())."\n";
				}
			}
			$va_processed_records[$va_item['table'].'/'.(int)$va_item['id']] = $t_instance->getPrimaryKey();
			
			if ($vb_skip) { continue; }
			if (!in_array($va_item['table'], $pa_config['importRelatedFor'])) { continue; }
			
			$pa_tables[$va_item['table']] = true;
			
			// Are there relationships?
			foreach(array('ca_objects', 'ca_entities', 'ca_places', 'ca_occurrences', 'ca_collections', 'ca_storage_locations',  'ca_loans', 'ca_movements') as $vs_table) {
				
				if (!$pa_tables[$vs_table]) {
					// load related records recursively
					if ($o_item->{'related_'.$vs_table}) {
						$t_rel = $o_dm->getInstanceByTableName($vs_table, true);
						foreach($o_item->{'related_'.$vs_table} as $vs_tag => $o_related_items) {
							foreach($o_related_items as $vs_i => $o_related_item) {
								$vs_pk = $t_rel->primaryKey();
								$vn_id = (int)$o_related_item->{$vs_pk};
								$va_queue = array(
									$vs_table."/".$vn_id => array(
										'table' => $vs_table,
										'id' => $vn_id,
										'idno' => $o_related_item->idno
									)
								);
								
								fetchAndImport($va_queue, $po_client, $pa_config, $pa_tables);
								
								$t_instance->addRelationship($vs_table, $va_processed_records[$vs_table.'/'.(int)$vn_id], (int)$o_related_item->relationship_type_id);
								if ($t_instance->numErrors()) {
									print "ERROR adding relationship to $vs_table for row_id ".$va_processed_records[$vs_table.'/'.(int)$vn_id].": ".join('; ', $t_instance->getErrors())."\n";
								}
							}
						}
					}
				}
			}
			unset($pa_tables[$va_item['table']]);
		}
	}
	# ------------------------------------------------------------------------------------------
?>
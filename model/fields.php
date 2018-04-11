<?php
class ModelField extends Model {
	public function getField($field_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "field` cf LEFT JOIN `" . DB_PREFIX . "field_description` cfd ON (cf.field_id = cfd.field_id) WHERE cf.status = '1' AND cf.field_id = '" . (int)$field_id . "' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCustomFields($customer_group_id = 0) {
		$field_data = array();

		if (!$customer_group_id) {
			$field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "field` cf LEFT JOIN `" . DB_PREFIX . "field_description` cfd ON (cf.field_id = cfd.field_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cf.status = '1' ORDER BY cf.sort_order ASC");
		} else {
			$field_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "field_customer_group` cfcg LEFT JOIN `" . DB_PREFIX . "field` cf ON (cfcg.field_id = cf.field_id) LEFT JOIN `" . DB_PREFIX . "field_description` cfd ON (cf.field_id = cfd.field_id) WHERE cf.status = '1' AND cfd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND cfcg.customer_group_id = '" . (int)$customer_group_id . "' ORDER BY cf.sort_order ASC");
		}

		foreach ($field_query->rows as $field) {
			$field_value_data = array();

			if ($field['type'] == 'select' || $field['type'] == 'radio' || $field['type'] == 'checkbox') {
				$field_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "field_value cfv LEFT JOIN " . DB_PREFIX . "field_value_description cfvd ON (cfv.field_value_id = cfvd.field_value_id) WHERE cfv.field_id = '" . (int)$field['field_id'] . "' AND cfvd.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY cfv.sort_order ASC");

				foreach ($field_value_query->rows as $field_value) {
					$field_value_data[] = array(
						'field_value_id' => $field_value['field_value_id'],
						'name'                  => $field_value['name']
					);
				}
			}

			$field_data[] = array(
				'field_id'    => $field['field_id'],
				'field_value' => $field_value_data,
				'name'               => $field['name'],
				'type'               => $field['type'],
				'value'              => $field['value'],
				'validation'         => $field['validation'],
				'location'           => $field['location'],
				'required'           => empty($field['required']) || $field['required'] == 0 ? false : true,
				'sort_order'         => $field['sort_order']
			);
		}

		return $field_data;
	}
}
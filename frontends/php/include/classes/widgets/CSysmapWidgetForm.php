<?php
/*
** Zabbix
** Copyright (C) 2001-2017 Zabbix SIA
**
** This program is free software; you can redistribute it and/or modify
** it under the terms of the GNU General Public License as published by
** the Free Software Foundation; either version 2 of the License, or
** (at your option) any later version.
**
** This program is distributed in the hope that it will be useful,
** but WITHOUT ANY WARRANTY; without even the implied warranty of
** MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
** GNU General Public License for more details.
**
** You should have received a copy of the GNU General Public License
** along with this program; if not, write to the Free Software
** Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
**/

class CSysmapWidgetForm extends CWidgetForm {

	public function __construct($data) {
		parent::__construct($data);

		// select source type field
		$source_types = [
			WIDGET_SYSMAP_SOURCETYPE_MAP => _('Map'),
			WIDGET_SYSMAP_SOURCETYPE_FILTER => _('Map navigation tree'),
		];
		$field_source_type = (new CWidgetFieldRadioButtonList('source_type', _('Source type'), $source_types))
			->setDefault(WIDGET_SYSMAP_SOURCETYPE_MAP)
			->setAction('updateWidgetConfigDialogue()')
			->setModern(true);
		if (array_key_exists('source_type', $data)) {
			$field_source_type->setValue((int)$data['source_type']);
		}
		$this->fields[] = $field_source_type;

		// select filter widget field
		if ($field_source_type->getValue() === WIDGET_SYSMAP_SOURCETYPE_FILTER) {
			$field_filter_widget = (new CWidgetFieldWidgetListComboBox('filter_widget_reference', _('Filter'),
				'type', 'navigationtree'))->setDefault('');

			if (array_key_exists('filter_widget_reference', $data)) {
				$field_filter_widget->setValue($data['filter_widget_reference']);
			}

			$this->fields[] = $field_filter_widget;
		}

		// select sysmap field
		$field_map = new CWidgetFieldSelectResource('sysmapid', _('Map'), WIDGET_FIELD_SELECT_RES_SYSMAP);
		if (array_key_exists('sysmapid', $data)) {
			$field_map->setValue($data['sysmapid']);
		}
		$this->fields[] = $field_map;
	}
}

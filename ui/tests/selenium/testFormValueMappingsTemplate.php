<?php
/*
** Zabbix
** Copyright (C) 2001-2021 Zabbix SIA
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

require_once dirname(__FILE__).'/common/testFormValueMappings.php';

/**
 * @backup valuemap
 * @backup hosts
 *
 * @on-before prepareTemplateValueMappings
 */
class testFormValueMappingsTemplate extends testFormValueMappings {
	/**
	 * Function creates the given Value mappings for the specified template.
	 */
	public static function prepareTemplateValueMappings() {
		CDataHelper::setSessionId(null);

		$response = CDataHelper::call('valuemap.create', [
			[
				'name' => self::UPDATE_VALUEMAP1,
				'hostid' => self::TEMPLATEID,
				'mappings' => [
					[
						'value' => '',
						'newvalue' => 'reference newvalue'
					]
				]
			],
			[
				'name' => self::UPDATE_VALUEMAP2,
				'hostid' => self::TEMPLATEID,
				'mappings' => [
					[
						'value' => '',
						'newvalue' => 'no data'
					],
					[
						'value' => '1',
						'newvalue' => 'one'
					],
					[
						'value' => '2',
						'newvalue' => 'two'
					],
					[
						'value' => '3',
						'newvalue' => 'three'
					]
				]
			],
			[
				'name' => self::DELETE_VALUEMAP,
				'hostid' => self::TEMPLATEID,
				'mappings' => [
					[
						'value' => 'oneoneoneoneoneoneoneoneoneoneone',
						'newvalue' => '11111111111'
					],
					[
						'value' => 'two',
						'newvalue' => '2'
					],
					[
						'value' => 'threethreethreethreethreethreethreethreethreethree',
						'newvalue' => '3333333333'
					],
					[
						'value' => 'four',
						'newvalue' => '4'
					]
				]
			]
		]);
	}

	public function testFormValueMappingsTemplate_Layout() {
		$this->checkLayout('template');
	}

	/**
	 * @backup-once valuemap
	 *
	 * @dataProvider getValuemapData
	 */
	public function testFormValueMappingsTemplate_Create($data) {
		$this->checkAction($data, 'template', 'create');
	}

	/**
	 * @backup-once valuemap
	 *
	 * @dataProvider getValuemapData
	 */
	public function testFormValueMappingsTemplate_Update($data) {
		$this->checkAction($data, 'template', 'update');
	}

	public function testFormValueMappingsTemplate_SimpleUpdate() {
		$this->checkSimpleUpdate('template');
	}

	public function testFormValueMappingsTemplate_Cancel() {
		$this->checkCancel('template');
	}

	/**
	 * @backup-once valuemap
	 */
	public function testFormValueMappingsTemplate_Delete() {
		$this->checkDelete('template');
	}

	/**
	 * Scenario for checking that the entered valuemap data is not lost if there is an error when saving the template.
	 */
	public function testFormValueMappingsTemplate_ErrorWhileSaving() {
		$this->checkSavingError('template');
	}

	/**
	 * Scenario for verifying that value mappings are correctly copied to the clone/ full clone of the template.
	 */
	public function testFormValueMappingsTemplate_Clone() {
		$this->checkClone('template');
	}
}

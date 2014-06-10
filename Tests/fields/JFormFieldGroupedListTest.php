<?php
/**
 * @copyright  Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

namespace Joomla\Form\Tests;

use Joomla\Test\TestHelper;
use Joomla\Form\Field\GroupedListField;
use SimpleXmlElement;

/**
 * Test class for JFormFieldGroupedList.
 *
 * @since  1.0
 */
class JFormFieldGroupedListTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * Test the getInput method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetInput()
	{
		$xml = '<field name="groupedlist" type="groupedlist" />';

		$field = new GroupedListField;

		$xml = new SimpleXmlElement($xml);
		$this->assertThat(
			$field->setup($xml, 'value'),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$this->assertThat(
			strlen($field->input),
			$this->greaterThan(0),
			'Line:' . __LINE__ . ' The getInput method should return something without error.'
		);

		// TODO: Should check all the attributes have come in properly.
	}

	public function dataGetOptions()
	{
		return array(
			array('<option value="oof" disabled="true">Foo</option>'
				. '<option value="rab" class="lorem">Bar</option>',
				array(
					0 => array(
						(object) array(
							'value' => 'oof',
							'text' => 'Foo',
							'disable' => true,
							'class' => '',
							'onclick' => ''
						),
						(object) array(
							'value' => 'rab',
							'text' => 'Bar',
							'disable' => false,
							'class' => 'lorem',
							'onclick' => ''
						),
					),
				),
			),
			array('<group label="barfoo"><option value="oof" disabled="true">Foo</option>'
				. '<option value="rab" class="lorem">Bar</option></group>',
				array(
					'barfoo' => array(
						(object) array(
							'value' => 'oof',
							'text' => 'Foo',
							'disable' => true,
							'class' => '',
							'onclick' => ''
						),
						(object) array(
							'value' => 'rab',
							'text' => 'Bar',
							'disable' => false,
							'class' => 'lorem',
							'onclick' => ''
						),
					),
				),
			),
			array('<option value="foo">Foo</option>'
				. '<group label="barfoo"><option value="oof" disabled="true">Foo</option>'
				. '<option value="rab" class="lorem">Bar</option></group>'
				. '<option value="bar">Bar</option>',
				array(
					0 => array(
						(object) array(
							'value' => 'foo',
							'text' => 'Foo',
							'disable' => false,
							'class' => '',
							'onclick' => ''
						),
					),
					'barfoo' => array(
						(object) array(
							'value' => 'oof',
							'text' => 'Foo',
							'disable' => true,
							'class' => '',
							'onclick' => ''
						),
						(object) array(
							'value' => 'rab',
							'text' => 'Bar',
							'disable' => false,
							'class' => 'lorem',
							'onclick' => ''
						),
					),
					2 => array(
						(object) array(
							'value' => 'bar',
							'text' => 'Bar',
							'disable' => false,
							'class' => '',
							'onclick' => ''
						),
					),
				),
			),
		);
	}

	/**
	 * Test the getGroups method.
	 *
	 * @dataProvider dataGetOptions
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function testGetGroups($optionTag, $expected)
	{
		$field = new GroupedListField;

		$fieldStartTag = '<field name="myName" type="groupedlist">';
		$fieldEndTag = '</field>';

		$xml = new SimpleXmlElement($fieldStartTag . $optionTag . $fieldEndTag);
		$this->assertThat(
			$field->setup($xml, 'value'),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$groups = TestHelper::invoke($field, 'getGroups');

		foreach ($expected as $i => $expectedGroup) {
			$this->assertTrue(
				in_array($expectedGroup, $groups),
				'Line:' . __LINE__ . ' The getGroups method should compute group #'
				. $i . ' correctly'
			);
		}
	}

	/**
	 * Test the getGroups method.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 * @expectedException UnexpectedValueException
	 */
	public function testGetGroupsUnknownChildException()
	{
		$field = new GroupedListField;

		$fieldStartTag = '<field name="myName" type="groupedlist">';
		$optionTag = '<item value="foo">Bar</item>';
		$fieldEndTag = '</field>';

		$xml = new SimpleXmlElement($fieldStartTag . $optionTag . $fieldEndTag);
		$this->assertThat(
			$field->setup($xml, 'value'),
			$this->isTrue(),
			'Line:' . __LINE__ . ' The setup method should return true.'
		);

		$groups = TestHelper::invoke($field, 'getGroups');
	}
}

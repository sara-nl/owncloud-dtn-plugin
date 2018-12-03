<?php

/**
 * @author Thomas Müller
 * @copyright Copyright (c) 2014 Thomas Müller deepdiver@owncloud.com
 * later.
 * See the COPYING-README file.
 */

namespace Test\App;

use OC;
use OC\App\InfoParser;
use Test\TestCase;

class InfoParserTest extends TestCase {

	/** @var InfoParser */
	private $parser;

	public function setUp() {
		$this->parser = new InfoParser();
	}

	/**
	 * @dataProvider providesInfoXml
	 */
	public function testParsingValidXml($expectedJson, $xmlFile) {
		$expectedData = null;
		if ($expectedJson !== null) {
			$expectedData = \json_decode(\file_get_contents(OC::$SERVERROOT . "/tests/data/app/$expectedJson"), true);
		}
		$data = $this->parser->parse(OC::$SERVERROOT. "/tests/data/app/$xmlFile");

		$this->assertEquals($expectedData, $data);
	}

	public function providesInfoXml() {
		return [
			['expected-info.json', 'valid-info.xml'],
			[null, 'invalid-info.xml'],
		];
	}
}

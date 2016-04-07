<?php

use \CaT\InstILIAS\YamlParser;

class YamlParserTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->parser = new YamlParser();
	}

	/**
	* @dataProvider readConfigWithValuesProvider
	*/
	public function test_readConfigWithValues($string, $class) {
		$obj = $this->parser->read_config($string, $class);
		$this->assertInstanceOf($class, $obj);
	}

	public function test_createClientConfig() {
		$json_string = "--- 
data_dir : sdasdads
name : hugo
password_encoder : md5";
		$obj = $this->parser->read_config($json_string, "\\CaT\\InstILIAS\\Config\\Client");

		$this->assertInstanceOf("\\CaT\\InstILIAS\\Config\\Client", $obj);
		$this->assertEquals($obj->dataDir(), "sdasdads");
		$this->assertInternalType("string", $obj->dataDir());
		$this->assertEquals($obj->name(), "hugo");
		$this->assertInternalType("string", $obj->name());
		$this->assertEquals($obj->passwordEncoder(), "md5");
		$this->assertInternalType("string", $obj->passwordEncoder());
	}

	public function test_createDbConfig() {
		$json_string = '---
host: 127.0.0.1
database: iliastest
user: root
password: 4z0sXAPk
engine: innodb
encoding: utf8_general_ci';
		$obj = $this->parser->read_config($json_string, "\\CaT\\InstILIAS\\Config\\DB");

		$this->assertInstanceOf("\\CaT\\InstILIAS\\Config\\DB", $obj);
		
		$this->assertEquals($obj->host(), "127.0.0.1");
		$this->assertInternalType("string", $obj->host());

		$this->assertEquals($obj->database(), "iliastest");
		$this->assertInternalType("string", $obj->database());

		$this->assertEquals($obj->user(), "root");
		$this->assertInternalType("string", $obj->user());

		$this->assertEquals($obj->password(), "4z0sXAPk");
		$this->assertInternalType("string", $obj->password());

		$this->assertEquals($obj->engine(), "innodb");
		$this->assertInternalType("string", $obj->engine());

		$this->assertEquals($obj->encoding(), "utf8_general_ci");
		$this->assertInternalType("string", $obj->encoding());
	}

	public function test_createGitConfig() {
		$json_string = '---
git_url: https://github.com/conceptsandtraining/ILIAS.git
git_branch_name: ilias';

		$obj = $this->parser->read_config($json_string, "\\CaT\\InstILIAS\\Config\\GitBranch");

		$this->assertInstanceOf("\\CaT\\InstILIAS\\Config\\GitBranch", $obj);
		
		$this->assertEquals($obj->gitUrl(), "https://github.com/conceptsandtraining/ILIAS.git");
		$this->assertInternalType("string", $obj->gitUrl());

		$this->assertEquals($obj->gitBranchName(), "ilias");
		$this->assertInternalType("string", $obj->gitBranchName());
	}

	public function test_createLanguageConfig() {
		$json_string = '---
default_lang: de
to_install_langs:
    - en
    - de';
		$obj = $this->parser->read_config($json_string, "\\CaT\\InstILIAS\\Config\\Language");

		$this->assertInstanceOf("\\CaT\\InstILIAS\\Config\\Language", $obj);
		
		$this->assertEquals($obj->defaultLang(), "de");
		$this->assertInternalType("string", $obj->defaultLang());

		$this->assertEquals($obj->toInstallLangs(), array("en","de"));
		$this->assertInternalType("array", $obj->toInstallLangs());
	}

	public function test_createServerConfig() {
		$json_string = '---
http_path: http://localhost/44generali2
absolute_path: /Library/WebServer/Documents/44generali2
timezone: Europe/Berlin';
		$obj = $this->parser->read_config($json_string, "\\CaT\\InstILIAS\\Config\\Server");

		$this->assertInstanceOf("\\CaT\\InstILIAS\\Config\\Server", $obj);
		
		$this->assertEquals($obj->httpPath(), "http://localhost/44generali2");
		$this->assertInternalType("string", $obj->httpPath());

		$this->assertEquals($obj->absolutePath(), "/Library/WebServer/Documents/44generali2");
		$this->assertInternalType("string", $obj->absolutePath());

		$this->assertEquals($obj->timezone(), "Europe/Berlin");
		$this->assertInternalType("string", $obj->timezone());
	}

	public function test_createSetupConfig() {
		$json_string = '---
passwd: KarlHeinz';
		$obj = $this->parser->read_config($json_string, "\\CaT\\InstILIAS\\Config\\Setup");

		$this->assertInstanceOf("\\CaT\\InstILIAS\\Config\\Setup", $obj);

		$this->assertEquals($obj->passwd(), "KarlHeinz");
		$this->assertInternalType("string", $obj->passwd());
	}

	public function test_createToolsConfig() {
		$json_string = '---
convert: /opt/ImageMagick
zip: /usr/bin/zip
unzip: /usr/bin/unzip
java: /usr/bin/java';
		$obj = $this->parser->read_config($json_string, "\\CaT\\InstILIAS\\Config\\Tools");

		$this->assertInstanceOf("\\CaT\\InstILIAS\\Config\\Tools", $obj);

		$this->assertEquals($obj->convert(), "/opt/ImageMagick");
		$this->assertInternalType("string", $obj->convert());

		$this->assertEquals($obj->zip(), "/usr/bin/zip");
		$this->assertInternalType("string", $obj->zip());

		$this->assertEquals($obj->unzip(), "/usr/bin/unzip");
		$this->assertInternalType("string", $obj->unzip());

		$this->assertEquals($obj->java(), "/usr/bin/java");
		$this->assertInternalType("string", $obj->java());
	}

	public function readConfigWithValuesProvider() {
		$json_string = '---
data_dir : /Users/shecken/Documents/ilias_data
name : Generali31
password_encoder : bcrypt
host: 127.0.0.1
database: iliastest31
user: root
password: 4z0sXAPk
engine: innodb
encoding: utf8_general_ci
default_lang: de
to_install_langs:
    - en 
    - de
http_path: http://localhost/iliastest31
absolute_path: /Library/WebServer/Documents/iliastest31
timezone: Europe/Berlin
passwd: KarlHeinz
convert: /opt/ImageMagick
zip: /usr/bin/zip
unzip: /usr/bin/unzip
java: /usr/bin/java
path: /Users/shecken/Documents/ilias_data/Generali31
file_name: ilias31.log
git_url: https://github.com/conceptsandtraining/ILIAS.git
git_branch_name: release_5-1';

		return array
			( array($json_string, "\\CaT\\InstILIAS\\Config\\Client")
			, array($json_string, "\\CaT\\InstILIAS\\Config\\DB")
			, array($json_string, "\\CaT\\InstILIAS\\Config\\GitBranch")
			, array($json_string, "\\CaT\\InstILIAS\\Config\\Language")
			, array($json_string, "\\CaT\\InstILIAS\\Config\\Server")
			, array($json_string, "\\CaT\\InstILIAS\\Config\\Setup")
			, array($json_string, "\\CaT\\InstILIAS\\Config\\Tools")
			);
	}

}

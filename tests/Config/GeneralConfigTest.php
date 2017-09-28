<?php

use \CaT\Ilse\Config\General;
use \CaT\Ilse\Aux\YamlConfigParser;

class GeneralConfigTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->parser = new YamlConfigParser();
		$this->yaml_string = "---
client:
    data_dir: /data_dir
    name: Ilias5
    password_encoder: bcrypt
    session_expire: 120
database:
    host: 127.0.0.1
    database: ilias
    user: user
    password: passwd
    engine: innodb
    encoding: utf8_general_ci
    create_db: 1
language:
    default: de
    available:
        - en
        - de
server:
    http_path: http://localhost/
    absolute_path: /yourpath
    timezone: Europe/Berlin
setup:
    master_password: passwd
tools:
    convert: /convert
    zip: /zip
    unzip: /unzip
    java: /java
log:
    path: /path
    file_name: ilias.log
    error_log: /path
git:
    url: https://github.com/ILIAS-eLearning/ILIAS.git
    branch: release_5-1
https_auto_detect:
    enabled: 0
    header_name: X-FORWARDED-SCHEME
    header_value: https
";
/*
category:
    categories:
        0:
            title: Eins
        1:
            title: Zwei
            children:
                10:
                    title: ZweiEins
                    children: []
                11:
                    title: ZweiZwei
                    children: []
        2:
            title: Drei
            children: []
orgunit:
    orgunits:
        0:
            title: &ORGU1 OrgEins
        1:
            title: OrgZwei
            children:
                10:
                    title: OrgZweiEins
                    children: []
                11:
                    title: OrgZweiZwei
                    children: []
        2:
            title: OrgDrei
            children: []
role:
    roles:
        0:
            title: Titel1
            description: Der darf alles sehen sonst nicht.
        1:
            title: Titel2
            description: Gruppe für alle
        2:
            title: Titel3
            description: Neue Menschen
ldap:
    name: ldap
    server: ldap://127.0.0.1:389
    basedn: cn=user,dc=dcdom,dc=local
    con_type: 1
    con_user_dn: cn=ldap,cn=user,dc=dcdom,dc=local
    con_user_pw: abcd
    sync_on_login: 1
    sync_per_cron: 0
    attr_name_user: sAMAccountName
    protocol_version: 3
    user_search_scope: 0
    register_role_name: User
    mappings:
        firstname: sn
        lastname: blaa
plugin:
    plugins:
        0:
            name: Pluginname
            git:
                url: Bernd
                branch: master
orgunit_type:
    orgunit_types:
        0:
            default_language: &ORGU1_TYPE_DEFAULT_LANGUAGE de
            type_language_settings:
                0:
                    language: de
                    title: &ORGU1_TYPE OrgunitTypeTest
                    description: Ich bin ein Test
orgunit_type_assignment:
    orgunit_type_assignments:
        0:
            orgunit_title: *ORGU1
            orgunit_type_default_language: *ORGU1_TYPE_DEFAULT_LANGUAGE
            orgunit_type_title: *ORGU1_TYPE
user:
    registration: 1
    link_lifetime: 1800
    required_fields:
        - title
        - birthday
        - gender
        - institution
        - department
        - street
        - zipcode
        - city
        - country
        - phone_office
        - phone_home
        - phone_mobile
        - fax
        - email
        - matriculation
    users:
        0:
           login: auto_test
           firstname: auto
           lastname: test
           gender: w
           email: stefan.hecken@concepts-and-training.de
           role: Administrator
password_settings:
    change_on_first_login: 1
    use_special_chars: 1
    numbers_and_chars: 1
    min_length: 8
    max_length: 0
    num_upper_chars: 1
    num_lower_chars: 1
    expire_in_days: 0
    forgot_password_aktive: 1
    max_num_login_attempts: 10
editor:
    enable_tinymce: 1
    repo_page_editor:
        enable: 1
        heavy_marked: 1
        marked: 1
        importand: 1
        superscript: 1
        subscript: 1
        comment: 1
        quote: 1
        accent: 1
        code: 1
        latex: 1
        footnote: 1
        external_link: 1
java_server:
    host: Test
    port: 8889
    index_path: /data_dir
    log_file: /path/log.file
    log_level: WARN
    num_threads: 1
    max_file_size: 500
    ini_path: /path
certificate:
    enable: 1
soap:
    enable: 1
    wdsl_path: http://files.php
    timeout: 10
learning_progress:
    enabled: 1
    anonym: 1
    time_span: 300
    extended_data: 0
    object_statistics: 1
    session_statistics: 0
    own_lp: 1
    personal_desktop: 0*/
	}

	public function test_not_enough_params() {
		try {
			$config = new General();
			$this->assertFalse("Should have raised.");
		}
		catch (\InvalidArgumentException $e) {}
	}

	public function test_createIliasConfig() {
		$config = $this->parser->read_config($this->yaml_string, "\\CaT\\Ilse\\Config\\General");
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\General", $config);
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Client", $config->client());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\DB", $config->database());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Language", $config->language());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Server", $config->server());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Setup", $config->setup());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Tools", $config->tools());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Log", $config->log());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Git", $config->git());
/*		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Categories", $config->category());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\OrgUnits", $config->orgunit());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Roles", $config->role());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\LDAP", $config->ldap());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Plugins", $config->plugin());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\HTTPSAutoDetect", $config->httpsAutoDetect());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\OrgunitTypes", $config->orgunitType());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\OrgunitTypeAssignments", $config->orgunitTypeAssignment());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Users", $config->user());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\PasswordSettings", $config->passwordSettings());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Editor", $config->editor());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\JavaServer", $config->javaServer());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Certificate", $config->certificate());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\Soap", $config->soap());
		$this->assertInstanceOf("\\CaT\\Ilse\\Config\\LearningProgress", $config->learningProgress());*/
	}
}

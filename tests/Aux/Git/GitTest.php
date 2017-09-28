<?php

/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

function tempdir() {
	$name = tempnam(sys_get_temp_dir(), "ilse");
	if (file_exists($name)) {
		unlink($name);
	}
	mkdir($name);
	assert('is_dir($name)');
	return $name;
}

/**
 * Test class for git commands
 * 
 * @author Daniel Weise <daniel.weise@concepts-and-training.de>
 */
abstract class GitTest extends PHPUnit_Framework_TestCase
{
	/**
	 * @return CaT\Ilse\Git\Git
	 */
	abstract public function getImplementation($target_dir, $remote_url);

	/**
	 * @var CaT\Ilse\Git\Git
	 */
	protected $gw;
	protected $gwe;

	/**
	 * Setup the testing environment
	 */
	public function setUp()
	{
		$this->gw 	= $this->getImplementation(tempdir(), __DIR__."/../../..", "ilias-tool-ilse");
		$this->gwe 	= $this->getImplementation("www/falscheAdresse/de", "httpsss://testbla");
	}

	/**
	 * Test the gitClone method
	 */
	public function test_gitClone()
	{
		$this->gw->gitClone();
		$this->assertFileExists($this->gw->gitGetPath() . "/run_tests.sh");
	}

	/**
	 * Test the gitFetch method
	 */
	public function test_gitFetch()
	{
		$this->gw->gitClone();
		$result = $this->gw->gitFetch();
		$this->assertEquals($result, 1);
	}

	/**
	 * Test the gitPull method
	 */
	public function test_gitPull()
	{
		$this->gw->gitClone();
		$result = $this->gw->gitPull("master");
		$this->assertEquals($result, 1);
	}

	/**
	 * Test the gitCheckout method
	 *
	 * @dataProvider checkoutProvider
	 */
	public function test_gitCheckout($branch, $new, $is_ok)
	{
		$this->gw->gitClone();
		$correct_control_flow = false;
		if ($is_ok) {
			$this->gw->gitCheckout($branch, $new);
			$correct_control_flow = true;
		}
		else {
			try {
				$this->gw->gitCheckout($branch, $new);
				$this->assertFalse("Should not get here...");
			}
			catch (GitException $e) {
				$correct_control_flow = true;
			}
		}
		$this->assertTrue($correct_control_flow);
	}

	/**
	 * Test the gitGetBranches method
	 */
	public function test_gitGetBranches()
	{
		$this->gw->gitClone();
		$this->gw->gitCheckout("master");
		$branches = $this->gw->gitGetBranches();
		$this->assertContains("master", $branches);
	}

	/**
	 * Test gitCloneException
	 */
	public function test_gitCloneException()
	{
		try
		{
			$this->gwe->gitClone();
			$this->assertFalse("Should have raised.");
		}
		catch(\Cat\Ilse\Aux\Git\GitException $e)
		{
			$this->assertTrue(true, "throws GitException");
		}
	}

	/**
	 * Test gitFetchException
	 */
	public function test_gitFetchException()
	{
		try
		{
			$this->gwe->gitFetch();
			$this->assertFalse("Should have raised.");
		}
		catch(\Cat\Ilse\Aux\Git\GitException $e)
		{
			$this->assertTrue(true, "throws GitException");
		}
	}

	/**
	 * Test gitPullException
	 */
	public function test_gitPullException()
	{
		try
		{
			$this->gwe->gitPull("master");
			$this->assertFalse("Should have raised.");
		}
		catch(\Cat\Ilse\Aux\Git\GitException $e)
		{
			$this->assertTrue(true, "throws GitException");
		}
	}

	/**
	 * Test gitCheckoutException
	 *
	 * @dataProvider checkoutProvider
	 */
	public function test_gitCheckoutException($branch, $new, $_)
	{
		try
		{
			$result = $this->gwe->gitCheckout($branch, $new);
			$this->assertFalse("Should have raised.");
		}
		catch(\Cat\Ilse\Aux\Git\GitException $e)
		{
			$this->assertTrue(true, "throws GitException");
		}
	}

	/**
	 * Provides data for the gitCheckout test
	 */
	public function checkoutProvider()
	{
		return [["master", false, true]];
	}
}

<?php
/* Copyright (c) 2017 Daniel Weise <daniel.weise@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Executer;

use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use CaT\Ilse\App;

/**
 * Run the ILIAS configuration process
 */
class ConfigurateILIAS extends BaseExecuter
{
	/**
	 * CaT\Ilse\IliasReleaseConfigurator
	 */
	protected $irc;

	/**
	 * Constructor of the class InstallILIAS
	 *
	 * @param string 									$config
	 * @param \CaT\Ilse\Interfaces\RequirementChecker 	$checker
	 * @param \CaT\Ilse\Interfaces\Git 					$git
	 * @param \CaT\Ilse\Interfaces\Pathes 		$path
	 */
	public function __construct($config,
								\CaT\Ilse\Interfaces\RequirementChecker $checker,
								\CaT\Ilse\Interfaces\Git $git,
								\CaT\Ilse\Interfaces\Pathes $path)
	{
		assert('is_string($config)');
		parent::__construct($config, $checker, $git, $path);

		$this->irc = new \CaT\Ilse\IliasReleaseConfigurator($this->absolute_path, $this->client_id);
	}

	/**
	 * Start the configuration process
	 */
	public function run()
	{
		echo "\n\nConfigure ILIAS.";

		$this->createCategories();
		$this->createOrgunits();
		$this->createRoles();
		$this->configLDAP();
		$this->installPlugins();
		$this->createOrgunitTypes();
		$this->assignOrgunitTypes();
		$this->configPasswordSettings();
		$this->configUsers();
		$this->configEditor();
		$this->configJavaServer();
		$this->configCertificates();
		$this->configSOAP();
		$this->configLearningProcess();

		echo "\n\nIlias successfull configured.";
	}

	/**
	 * Create catogories in ILIAS
	 */
	protected function createCategories()
	{
		if($this->gc->category() !== null)
		{
			echo "\nCreating categories...";
			$this->irc->getCategoriesConfigurator()->createCategories($this->gc->category());
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Create orgunits in ILIAS
	 */
	protected function createOrgunits()
	{
		if($this->gc->orgunit() !== null)
		{
			echo "\nCreating orgunits...";
			$this->irc->getOrgUnitsConfigurator()->createOrgUnits($this->gc->orgunit());
			echo "\t\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Create global roles in ILIAS
	 */
	protected function createRoles()
	{
		if($this->gc->role() !== null)
		{
			echo "\nCreating global roles...";
			$this->irc->getRolesConfigurator()->createRoles($this->gc->role());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Configure LDAP server settings
	 */
	protected function configLDAP()
	{
		if($this->gc->ldap() !== null)
		{
			echo "\nConfiguring LDAP server settings...";
			$this->irc->getLDAPConfigurator()->configureLDAPServer($this->gc->ldap());
			$this->irc->getLDAPConfigurator()->mapLDAPValues($this->gc->ldap());
			echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Install plugins
	 */
	protected function installPlugins()
	{
		if($this->gc->plugin() !== null)
		{
			echo "\nInstalling plugins...";
			$this->irc->getPluginsConfigurator()->installPlugins($this->gc->plugin());
			$this->irc->getPluginsConfigurator()->activatePlugins($this->gc->plugin());
			echo "\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Crate orgunit types
	 */
	protected function createOrgunitTypes()
	{
		if($this->gc->orgunitType() !== null)
		{
			echo "\nCreating orgunit types...";
			$this->irc->getOrgUnitsConfigurator()->createOrgunitTypes($this->gc->orgunitType());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Assign orgunit types to orgunit
	 */
	protected function assignOrgunitTypes()
	{
		if($this->gc->orgunitTypeAssignment() !== null)
		{
			echo "\nAssigning orgunit types to orgunit...";
			$this->irc->getOrgUnitsConfigurator()->assignOrgunitTypesToOrgunits($this->gc->orgunitTypeAssignment());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Configure password settings
	 */
	protected function configPasswordSettings()
	{
		if($this->gc->passwordSettings() !== null)
		{
			echo "\nConfiguring password settings...";
			$this->irc->getUserConfigurator()->passwordSettings($this->gc->passwordSettings());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Configure users
	 */
	protected function configUsers()
	{
		if($this->gc->user() !== null)
		{
			$user_configurator = $this->irc->getUserConfigurator();
			echo "\nConfiguring self registration mode...";
			$user_configurator->registration($this->gc->user());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";

			echo "\nChanging requirement settings for basic fields...";
			$user_configurator->changeRequirementSettings($this->gc->user());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";

			if($this->gc->user()->users())
			{
				echo "\nCreating user accounts...";
				$user_configurator->createUserAccounts($this->gc->user());
				echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
			}
		}
	}

	/**
	 * Configure editor
	 */
	protected function configEditor()
	{
		if($this->gc->editor() !== null)
		{
			echo "\nSetting usage of TinyMCE...";
			$this->irc->getEditorConfigurator()->tinyMCE($this->gc->editor());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";

			echo "\nSetting usage of repo page editor...";
			$this->irc->getEditorConfigurator()->repoPageEditor($this->gc->editor());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Configure java server
	 */
	protected function configJavaServer()
	{
		if($this->gc->javaServer() !== null)
		{
			echo "\nConfiguring java server...";
			$this->irc->getJavaServerConfigurator()->javaServer($this->gc->javaServer());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Configure certificates
	 */
	protected function configCertificates()
	{
		if($this->gc->certificate() !== null)
		{
			echo "\nConfiguring certificate...";
			$this->irc->getCertificatesConfigurator()->certificate($this->gc->certificate());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Configure soap
	 */
	protected function configSOAP()
	{
		if($this->gc->soap() !== null)
		{
			echo "\nConfiguring soap...";
			$this->irc->getSoapConfigurator()->soap($this->gc->soap());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}

	/**
	 * Configure learning progress
	 */
	protected function configLearningProcess()
	{
		if($this->gc->learningProgress() !== null)
		{
			echo "\nConfiguring LP...";
			$this->irc->getLearningProgressConfigurator()->learningProgress($this->gc->learningProgress());
			echo "\t\t\t\t\t\t\t\t\t\t\t\tDone!\n";
		}
	}
}
<?php
/* Copyright (c) 2016 Stefan Hecken <stefan.hecken@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\InstILIAS\interfaces;

/**
 * Interface for ILIAS Configurator.
 *
 * @author Stefan Hecken <stefan.hecken@concepts-and-training.de>
 */
interface Configurator {
	/**
	 * initialize ilias for further configuration
	 */
	public function initIlias();

	/**
	 * Get the configurator for user actions
	 *
	 * @return CaT\InstILIAS\Configurators\Users
	 */
	public function getUserConfigurator();

	/**
	 * Get the configurator for role actions
	 *
	 * @return CaT\InstILIAS\Configurators\Roles
	 */
	public function getRolesConfigurator();

	/**
	 * Get the configurator for org unit actions
	 *
	 * @return CaT\InstILIAS\Configurators\OrgUnits
	 */
	public function getOrgUnitsConfigurator();

	/**
	 * Get the configurator for categorie actions
	 *
	 * @return CaT\InstILIAS\Configurators\Categories
	 */
	public function getCategoriesConfigurator();

	/**
	 * Get the configurator for ldap actions
	 *
	 * @return CaT\InstILIAS\Configurators\LDAP
	 */
	public function getLDAPConfigurator();

	/**
	 * Get the configurator for plugins actions
	 *
	 * @return CaT\InstILIAS\Configurators\Plugins
	 */
	public function getPluginsConfigurator();

	/**
	 * Get the configurator for editor actions
	 *
	 * @return CaT\InstILIAS\Configurators\Editor
	 */
	public function getEditorConfigurator();

	/**
	 * Get the configurator for java server actions
	 *
	 * @return CaT\InstILIAS\Configurators\JavaServer
	 */
	public function getJavaServerConfigurator();

	/**
	 * Get the configurator for soap actions
	 *
	 * @return CaT\InstILIAS\Configurators\Soap
	 */
	public function getSoapConfigurator();

	/**
	 * Get the configurator for java learning progress actions
	 *
	 * @return CaT\InstILIAS\Configurators\LearningProgress
	 */
	public function getLearningProgressConfigurator();
	
}
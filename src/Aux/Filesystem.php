<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Aux;

/**
 * Some filesystem functions.
 */
interface Filesystem {
	/**
	 * Remove file or directory.
	 *
	 * @param	string	$path
	 * @return	void
	 */
	public function remove($path);

	/**
	 * Check if file or directory exists.
	 *
	 * @param	string	$path
	 * @return 	bool
	 */
	public function exists($path);

	/**
	 * Check if given path is a directory.
	 *
	 * @param 	string	$path
	 * @return	bool
	 */
	public function isDirectory($path);

	/**
	 * Check if given path is writeable for the current user.
	 *
	 * @param 	string	$path
	 * @return	bool
	 */
	public function isWriteable($path);

	/**
	 * Check if directory at given path is empty.
	 *
	 * @param	string	$path
	 * @return	bool
	 */
	public function isEmpty($path);

	/**
	 * Create a directory.
	 *
	 * @param	string	$path
	 * @return	void
	 */
	public function makeDirectory($path);

	/**
	 * Purge files in directory.
	 *
	 * @param	string	$path
	 * @return	void
	 */
	public function purgeDirectory($path);

	/**
	 * Get subdirectories of a directory
	 *
	 * @param 	string 	$path
	 * @return 	string[]
	 */
	public function getSubdirectories($path);

	/**
	 * Change access permissions.
	 *
	 * ATTENTION: second parameter is an integer, i.e. use 0755 e.g.
	 *
	 * @param	string	$path
	 * @param	int		$perms
	 * @return	void
	 */
	public function chmod($path, $perms);

	/**
	 * Get the home directory of the current user.
	 *
	 * @return string
	 */
	public function homeDirectory();

	/**
	 * Read a file.
	 *
	 * @param	string	$path
	 * @return	string
	 */
	public function read($path);

	/**
	 * Write a file.
	 *
	 * @param	string	$path
	 * @param	string 	$content
	 * @return	void
	 */
	public function write($path, $content);

	/**
	 * Link a file or folder
	 *
	 * @param 	string 	$target 	existing file or folder
	 * @param 	string 	$link 		paht + name of the link
	 * @return 	bool
	 */
	public function symlink($target, $link);

	/**
	 * Check for symlink
	 *
	 * @param 	string 	$filename
	 * @return 	bool
	 */
	public function isLink($filename);
}

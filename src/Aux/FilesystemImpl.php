<?php

/* Copyright (c) 2017 Richard Klees <richard.klees@concepts-and-training.de>, Extended GPL, see LICENSE */

namespace CaT\Ilse\Aux;

/**
 * Some filesystem functions.
 *
 * TODO: make this expand ~ to home
 */
class FilesystemImpl implements Filesystem {
	/**
	 * @inheritdoc
	 */
	public function remove($path) {
		assert('is_string($path)');

		if (is_file($path)) {
			unlink($path);
		}
		else {
			assert('is_dir($path)');
			$this->purgeDirectory($path);
			rmdir($path);
		}
	}

	/**
	 * @inheritdoc
	 */
	public function exists($path) {
		assert('is_string($path)');
		return file_exists($path);
	}

	/**
	 * @inheritdoc
	 */
	public function isDirectory($path) {
		assert('is_string($path)');
		return is_dir($path);
	}

	/**
	 * @inheritdoc
	 */
	public function isWriteable($path) {
		assert('is_string($path)');
		return is_writeable($path);
	}

	/**
	 * @inheritdoc
	 */
	public function isEmpty($path) {
		assert('is_string($path)');
		assert('is_dir($path)');
		return scandir($path) == [".", ".."];
	}

	/**
	 * @inheritdoc
	 */
	public function makeDirectory($path) {
		assert('is_string($path)');
		mkdir($path, 0755, true);
	}

	/**
	 * @inheritdoc
	 */
	public function purgeDirectory($path) {
		$files = array_diff(scandir($path), array('.','..'));
		foreach ($files as $file) {
			if (is_dir("$path/$file")) {
				$this->purgeDirectory("$path/$file");
				rmdir("$path/$file");
			}
			else {
				unlink("$path/$file");
			}
		}
	}

	/**
	 * @inheritdoc
	 */
	public function getSubdirectories($path)
	{
		$subdirs = array();
		$entries = array_diff(scandir($path), array('.', '..'));
		foreach ($entries as $entry)
		{
			if($this->isDirectory($path."/".$entry))
			{
				$subdirs[] = $entry;
			}
		}
		return $subdirs;
	}

	/**
	 * @inheritdoc
	 */
	public function chmod($path, $perms) {
		assert('is_string($path)');
		assert('is_int($perms)');
		chmod($path, $perms);
	}

	/**
	 * @inheritdoc
	 */
	public function homeDirectory() {
		return getenv("HOME");
	}

	/**
	 * @inheritdoc
	 */
	public function read($path) {
		assert('is_string($path)');
		return file_get_contents($path);
	}

	/**
	 * @inheritdoc
	 */
	public function write($path, $content) {
		assert('is_string($path)');
		assert('is_string($content)');
		file_put_contents($path, $content);
	}

	/**
	 * @inheritdoc
	 */
	public function symlink($target, $link) {
		assert('is_string($target)');
		assert('is_string($link)');
		return symlink($target, $link);
	}
}

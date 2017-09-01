[![Software License](https://img.shields.io/aur/license/yaourt.svg?style=round-square)](LICENSE.md)

# ilse
**A Command Line Installation Script for [ILIAS](https://github.com/ILIAS-eLearning/ILIAS)**

**Contact:** [Stefan Hecken](https://github.com/shecken)

## Usage
### Software requirements
```
* PHP 5.4 or higher (PHP 7 works since release_5-2)
* MySQL 5.0.x or higher
* Zip and Unzip
* ImageMagick
* Composer
* git 2.1.4 or higher
```
### Installation
```
$ cd DESTINATION_FOLDER
$ git clone https://github.com/conceptsandtraining/ilias-tool-ilse.git ilse
$ cd ilse
$ composer install
```

### Configuration
The Ilias-installer relies on a github-repository as the source of configuration files.  
The corresponding repo-paths should be specified in `$HOMEDIR/.ilse/config_repos.yaml`.  
Please refer to `config_repos_default.yaml` for the required layout.
   
The name of the config file is always `ilse_config.yaml`.  
Each config file is inside a directory that represents the customomer name.  

* Edit the file src/default.yaml
* Save the file as ilse_config.yaml
* Push the file into the destination folder of the repo named above.


### Required configuration entries
For new installation of ILIAS you need these configuration entries.
```
* client
* database
* language
* server
* setup
* tools
* log
* git_branch
```

### Re- / Installation of ILIAS
With ilse it´s possible to install a new ILIAS or drop your old an install in one step.
For both it is possible to run the installation in a non interactiv mode.
If you would use this, just add the parameter -i in your command.

##### Installation
```
$ ./ilse.php install $REPO_FOLDER_NAMES [-i]
```
##### Reinstallation
```
$ ./ilse.php reinstall $REPO_FOLDER_NAMES [-i][-a]
```
With the option -a ilse will delete all log files and the whole data folder before installing ILIAS again. 
##### Delete installation
```
$ ./ilse.php delete $REPO_FOLDER_NAMES [-a]
```
With the option -a ilse will also delete all log files and the whole data folder. 
##### Update installation
```
$ ./ilse.php update $REPO_FOLDER_NAMES
```
##### Update plugins
```
$ ./ilse.php updateplugins $REPO_FOLDER_NAMES
```
##### Update config
```
$ ./ilse.php config $REPO_FOLDER_NAMES
```
<?php
/**
 *
 * (c) Ruben Dorado <ruben.dorados@google.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SiteAnalyzer;

use Exception;

/**
 * class SiteAnalyzer
 *
 * @package   SiteAnalyzer		
 * @author    Ruben Dorado <ruben.dorados@gmail.com>
 * @copyright 2018 Ruben Dorado
 * @license   http://www.opensource.org/licenses/MIT The MIT License
 */
final class Configuration
{

    /**
     * @var string
     */
    protected $dsn;

    /**
     * @var string
     */
    protected $user;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var string
     */
    protected $hitTableName;

    /**
     * @var string
     */
    protected $optionsTableName;
    
    /**
     * @var string
     */
    protected $fromTableName;
    
    /**
     * @var string
     */
    protected $urlTableName;
    
    /**
     * @var boolean
     */
    protected $storeTime;

    /**
     * @var boolean
     */
    protected $storeUser;
    
    /**
     * @var boolean
     */
    protected $storeFromInfo;
    
    /**
     * @var boolean
     */
    protected $removeQueryString;

    /**
     * @var boolean
     */
    protected $useOnMemoryDB;
    
    
    /*
     * @return string
     */
    public function getDsn()
    {
        return $this->dsn;
    }

    /*
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /*
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /*
     * @return boolean
     */
    public function getStoreTime()
    {
        return $this->storeTime;
    }

    /*
     * @return string
     */
    public function getHitTableName()
    {
        return $this->hitTableName;
    }

    
    /*
     * @return boolean
     */
    public function getRemoveQueryString()
    {
        
        return $this->removeQueryString;
    }
    

    /*
     * @return string
     */
    public function getOptionsTableName()
    {
        return $this->optionsTableName;
    }
    
    /*
     * @return string
     */
    public function getFromTableName()
    {
        return $this->fromTableName;
    }

    /*
     * @return string
     */
    public function getUrlTableName()
    {
        return $this->urlTableName;
    }
    
    /*
     * @return boolean
     */
    public function getUseOnMemoryDB()
    {
        return $this->useOnMemoryDB;
    }    
    
    /*
     * @param configFileName string
     * @param pdoProvided boolean
     */
    public function __construct($configFileName, $pdoProvided = FALSE)
    {   
        if (!file_exists($configFileName)) {
            throw new Exception("File ".getcwd()."/".$configFileName." not found.");
        }
        $config = parse_ini_file($configFileName, TRUE); 
        if (!$pdoProvided) {
            $this->dsn = $this->loadMandatoryVariable($config, "database", "dsn");
        }
        
        $this->hitTableName = $this->loadMandatoryVariable($config, "database", "db_hit_table");
        $this->fromTableName = $this->loadMandatoryVariable($config, "database", "db_from_table");        
        $this->optionsTableName = $this->loadMandatoryVariable($config, "database", "db_options_table");
        $this->urlTableName = $this->loadMandatoryVariable($config, "database", "db_url_table");
        $this->useOnMemoryDB = $this->getBooleanParameter($config, "database", "use_onmemorydb");
                
        $this->storeTime = $this->getBooleanParameter($config, "options", "store_time");
        $this->storeUser = $this->getBooleanParameter($config, "options", "store_user"); 
        $this->storeFromInfo = $this->getBooleanParameter($config, "options", "store_from_info"); 
        $this->removeQueryString = $this->getBooleanParameter($config, "options", "remove_query_string");  
        
        $this->user = $this->getStringParameter($config, "database", "user"); 
        $this->password = $this->getStringParameter($config, "database", "password");  
    }

    /*
     * @param name string
     */
    private static function getStringParameter($config, $section, $name)
    {    
        return isset($config[$section][$name]) ? $config[$section][$name] : NULL;
    }
    
    /*
     * @param name string
     */
    private static function getBooleanParameter($config, $section, $name)
    {   
        return isset($config[$section][$name]) ? $config[$section][$name]!==0 : false;
    }

    /*
     * @param configFileName string
     * @param section string
     * @param varname string
     *
     * @return string
     */
    private function loadMandatoryVariable($configFile, $section, $varname)
    {
        try {
            return $configFile[$section][$varname];
        } catch (Exception $e) {
            throw new Exception("Error loading config file. Variable $varname in section [$section] not found. Check the configuration file.");
        }
    }

    /*
     * @param configFileName string
     */
    public static function loadConfig($configFileName)
    {
        $config = new Configuration($configFileName);
        return $config;
    }

}

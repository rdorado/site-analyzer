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
    protected $storeTime;

    /**
     * @var string
     */
    protected $storeUser;
    
    /**
     * @var string
     */
    protected $storeFromInfo;
    
    /**
     * @var string
     */
    protected $removeQueryString;

    /**
     * @var string
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
     * @return string
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
     * @return string
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
     * @return string
     */
    public function getUseOnMemoryDB()
    {
        return $this->useOnMemoryDB;
    }
    
    /*
     * @param configFileName
     */
    public function __construct($configFileName, $pdoProvided=FALSE)
    {    
        $config = parse_ini_file($configFileName, TRUE); 
        if(!$pdoProvided){
            $this->dsn = $this->loadMandatoryVariable($config,"database","dsn");
        }
        
        $this->hitTableName = $this->loadMandatoryVariable($config,"database","db_hit_table");
        $this->fromTableName = $this->loadMandatoryVariable($config,"database","db_from_table");        
        $this->optionsTableName = $this->loadMandatoryVariable($config,"database","db_options_table");
        $this->urlTableName = $this->loadMandatoryVariable($config,"database","db_url_table");
        $this->useOnMemoryDB = $this->loadMandatoryVariable($config,"database","use_onmemorydb");
        
        $this->storeTime = isset($config['options']['store_time']) ? strtolower($config['options']['store_time'])=="yes" : false;
        $this->storeUser = isset($config['options']['store_user']) ? strtolower($config['options']['store_user'])=="yes" : false;
        $this->storeFromInfo = isset($config['options']['store_from_info']) ? strtolower($config['options']['store_from_info'])=="yes|" : false;
        $this->removeQueryString = isset($config['options']['remove_query_string']) ? strtolower($config['options']['store_from_info'])=="yes" : false;
        
        $this->user = isset($config['database']['user']) ? $config['database']['user'] : NULL;
        $this->password = isset($config['database']['password']) ? $config['database']['password'] : NULL;
    }


    /*
     * @param configFileName string
     * @param section string
     * @param varname string
     *
     * @return string
     */
    private function loadMandatoryVariable($configFile,$section,$varname)
    {
        try{
            return $configFile[$section][$varname];
        }
        catch(Exception $e){
            throw new Exception( "Error loading config file. Variable $varname in section [$section] not found. Check the configuration file.");
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

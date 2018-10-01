<?php
/**
 *
 * (c) Ruben Dorado <ruben.dorados@google.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

//namespace SiteAnalyzer;


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
    protected $countTableName;

    /**
     * @var string
     */
    protected $storeTime;

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
    public function getCountTableName()
    {
        return $this->countTableName;
    }

    /*
     * @param configFileName
     */
    public function __construct($configFileName,$pdoProvided)
    {    
        $config = parse_ini_file($configFileName, TRUE); 
        try{
            if(!$pdoProvided){
                $this->dsn = $this->loadMandatoryVariable($config,"database","dsn");
            }

            $this->countTableName = $this->loadMandatoryVariable($config,"database","count_table_name");
            $this->storeTime = $this->loadMandatoryVariable($config,"counter","store_time"); 
         }
         catch(Exception $e){
            throw new ConfigurationException("Error loading ");
         }

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
            throw new ConfigurationException("Error loading config file. Variable $varname in section $section not found. Check the configuration file.");
        }
    }


    /*
     * @param
     */
    public static function loadConfig($configFileName)
    {
        $config = new Configuration($configFileName);
    }


}

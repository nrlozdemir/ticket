<?php

class Application_Model_Pdo
{
    /**
     * @var object
     */
    protected $connection = null;


    function __construct()
    {
        $this->connect();
    }

    public function connect()
    {
        $configuration = Zend_Controller_Front::getInstance()->getParam('bootstrap');
        $keys = $configuration->getOption('database');


        $hostname  = $keys['params']['hostname'];
        $database  = $keys['params']['database'];
        $username  = $keys['params']['username'];
        $password  = $keys['params']['password'];

        try
        {
            $this->connection = new PDO(
                "mysql:host=$hostname;dbname=$database",
                $username,
                $password,
                array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
                )
            );
        }

        // Catch errors if exist
        catch(PDOException $e)
        {
            $this->error = $e->getMessage();
        }

        return $this->connection;
    }
}


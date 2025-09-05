<?php

class DatabaseUtils
{
    private $connection;

    function __construct($nomeContexto)
    {
        $dns = getenv($nomeContexto."_DSN");
        $user = getenv($nomeContexto."_DATABASE_USER");
        $password = getenv($nomeContexto."_DATABASE_PASSWORD");
        $this->connection = new PDO($dns, $user, $password);

        // $this->connection = new PDO("mysql:host=mysql;port=3306;dbname=sip", "sip_user","sip_user");
    }


	public function execute($sql, $params = array()){
		$statement = $this->connection->prepare($sql);
        $result = $statement->execute($params);
        return $result;
	}


	public function query($sql, $params = array()){
		$statement = $this->connection->prepare($sql);
		$statement->execute($params);
		return $statement->fetchAll();
	}   

    
    public function getBdType(){
		return $this->connection->getAttribute(PDO::ATTR_DRIVER_NAME);
	}
}

<?php

class UserDAO {

    public static $instance;

    private function __construct() {
        //
    }

    public static function getInstance() {
        if(!isset(self::$instance))
            self::$instance = new UserDAO();
        
        return self::$instance;
    }

    public function getNextId() {
        try {
            $sql = 'SELECT Auto_increment FROM information_schema.tables
                    WHERE table_name=\'user\'';

            $query = Connection::getInstance()->query($sql);
            $result = $query->fetch(PDO::FETCH_ASSOC);
            
            return $result['Auto_increment'];
        } catch (Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function insert(User $user) {
        try {
            $sql = 'INSERT INTO user (
                name, 
                email,
                password,
                active,
                profileId
                ) 
                VALUES (
                :name, 
                :email,
                :password,
                :active,
                :profileId
            )';

            $p_sql = Connection::getInstance()->prepare($sql);

            $p_sql->bindValue(':name', $user->getName());
            $p_sql->bindValue(':email', $user->getEmail());
            $p_sql->bindValue(':password', $user->getPassword());
            $p_sql->bindValue(':active', $user->getActive());
            $p_sql->bindValue(':profileId', $user->getProfile()->getId());

            return $p_sql->execute();
        } catch (Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }

    }
}
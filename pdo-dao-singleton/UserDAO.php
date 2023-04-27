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

    public function add(User $user) {
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

    public function update(User $user) {
        try {
            $sql = 'UPDATE user SET 
                    name = :name, 
                    email = :email,
                    active = :active,
                    profileId = :profileId 
                    WHERE id = :id';
            
            $p_sql = Connection::getInstance()->prepare($sql);

            $p_sql->bindValue(':name', $user->getName());
            $p_sql->bindValue(':email', $user->getEmail());
            $p_sql->bindValue(':active', $user->getActive());
            $p_sql->bindValue(':profileId', $user->getProfile()->getId());
            $p_sql->bindValue(':id', $user->getId());

            return $p_sql->execute();
        } catch (Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function updateWithPassword(User $user) {
        try {
            $sql = 'UPDATE user SET 
                    name = :name, 
                    email = :email,
                    password = :password, 
                    active = :active,
                    profileId = :profileId 
                    WHERE id = :id';
            
            $p_sql = Connection::getInstance()->prepare($sql);

            $p_sql->bindValue(':name', $user->getName());
            $p_sql->bindValue(':email', $user->getEmail());
            $p_sql->bindValue(':password', $user->getPassword());
            $p_sql->bindValue(':active', $user->getActive());
            $p_sql->bindValue(':profileId', $user->getProfile()->getId());
            $p_sql->bindValue(':id', $user->getId());

            return $p_sql->execute();
        } catch (Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function updatePasswordAlreadyEncrypted($userId, $newPassword) {
        try {
            $sql = 'UPDATE user SET 
                    password = :newPassword 
                    WHERE id = :userId';
            
            $p_sql = Connection::getInstance()->prepare($sql);

            $p_sql->bindValue(':newPassword', $newPassword);
            $p_sql->bindValue(':id', $userId);

            return $p_sql->execute();
        } catch (Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function updatePassword($userId, $password, $newPassword) {
        try {
            $user = $this->findById($userId);
            if ($user->getPassword() == md5(trim(strtolower($password)))) {
                $sql = 'UPDATE user SET 
                        password = :newPassword 
                        WHERE id = :userId AND password = :password';
                
                $p_sql = Connection::getInstance()->prepare($sql);

                $p_sql->bindValue(':newPassword', md5(trim(strtolower($newPassword))));
                $p_sql->bindValue(':password', md5(trim(strtolower($password))));
                $p_sql->bindValue(':userId', $userId);

                return $p_sql->execute();
            }
            else
                return false;
        } catch(Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $sql = 'DELETE FROM user WHERE id = :id';
            $p_sql = Connection::getInstance()->prepare($sql);
            $p_sql->bindValue(':id', $id);

            return $p_sql->execute();
        } catch(Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function findById($id) {
        try {
            $sql = 'SELECT * FROM user WHERE id = :id';
            $p_sql = Connection::getInstance()->prepare($sql);
            $p_sql->bindValue(':id', $id);

            if ($p_sql->execute())
                return $this->bindUser($p_sql->fetch(PDO::FETCH_OBJ));
            else
                return false;
        } catch(Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function findByEmail($email) {
        try {
            $sql = 'SELECT * FROM user WHERE email = :email';
            $p_sql = Connection::getInstance()->prepare($sql);
            $p_sql->bindValue(':email', $email);

            if ($p_sql->execute())
                return $this->bindUser($p_sql->fetch(PDO::FETCH_OBJ));
            else
                return false;
        } catch(Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function findByEmailAndPassword($email, $password) {
        try {
            $sql = 'SELECT * FROM user WHERE email = :email AND password = :password';
            $p_sql = Connection::getInstance()->prepare($sql);
            $p_sql->bindValue(':email', $email);
            $p_sql->bindValue(':password', $password);

            if ($p_sql->execute())
                return $this->bindUser($p_sql->fetch(PDO::FETCH_OBJ));
            else
                return false;
        } catch(Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function findAll() {
        try {
            $sql = 'SELECT * FROM user ORDER BY name';
            $query = Connection::getInstance()->query($sql);
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            $list = array();

            foreach($result as $r)
                $list[] = $this->bindUser($r);
            
            return $list;
        } catch(Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    public function findAllActive() {
        try {
            $sql = 'SELECT * FROM user WHERE active = 1 ORDER BY name';
            $query = Connection::getInstance()->query($sql);
            $result = $query->fetchAll(PDO::FETCH_OBJ);
            $list = array();

            foreach($result as $r)
                $list[] = $this->bindUser($r);
            
            return $list;
        } catch(Exception $e) {
            print 'An error occurred when trying to perform this action, 
            an error Log was generated, please try again later.';

            LogGenerator::getInstance()->insertLog('<<Error>> Code: ' . $e->getCode() 
                                                    . ' Message: ' . $e->getMessage());
        }
    }

    private function bindUser($data) {
        $user = new User;
        $user->setId($data->id);
        $user->setName($data->name);
        $user->setEmail($data->email);
        $user->setPassword($data->password);
        $user->setActive($data->active);
        $user->setProfile(ProfileController::getInstance()->findById($data->profileId));

        return $user;
    }
}
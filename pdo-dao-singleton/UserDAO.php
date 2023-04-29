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

            $stmt = Connection::getInstance()->prepare($sql);

            $stmt->bindValue(':name', $user->getName());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':password', password_hash($user->getPassword(), PASSWORD_DEFAULT));
            $stmt->bindValue(':active', $user->getActive());
            $stmt->bindValue(':profileId', $user->getProfile()->getId());

            return $stmt->execute();
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
            
            $stmt = Connection::getInstance()->prepare($sql);

            $stmt->bindValue(':name', $user->getName());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':active', $user->getActive());
            $stmt->bindValue(':profileId', $user->getProfile()->getId());
            $stmt->bindValue(':id', $user->getId());

            return $stmt->execute();
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
            
            $stmt = Connection::getInstance()->prepare($sql);

            $stmt->bindValue(':name', $user->getName());
            $stmt->bindValue(':email', $user->getEmail());
            $stmt->bindValue(':password', $user->getPassword());
            $stmt->bindValue(':active', $user->getActive());
            $stmt->bindValue(':profileId', $user->getProfile()->getId());
            $stmt->bindValue(':id', $user->getId());

            return $stmt->execute();
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
            
            $stmt = Connection::getInstance()->prepare($sql);

            $stmt->bindValue(':newPassword', $newPassword);
            $stmt->bindValue(':id', $userId);

            return $stmt->execute();
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
            if (password_verify($password, $user->getPassword())) {
                $sql = 'UPDATE user SET 
                        password = :newPassword 
                        WHERE id = :userId';
                
                $stmt = Connection::getInstance()->prepare($sql);

                $stmt->bindValue(':newPassword', password_hash($newPassword, PASSWORD_DEFAULT));
                $stmt->bindValue(':userId', $userId);

                return $stmt->execute();
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
            $stmt = Connection::getInstance()->prepare($sql);
            $stmt->bindValue(':id', $id);

            return $stmt->execute();
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
            $stmt = Connection::getInstance()->prepare($sql);
            $stmt->bindValue(':id', $id);

            if ($stmt->execute())
                return $this->bindUser($stmt->fetch(PDO::FETCH_OBJ));
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
            $stmt = Connection::getInstance()->prepare($sql);
            $stmt->bindValue(':email', $email);

            if ($stmt->execute())
                return $this->bindUser($stmt->fetch(PDO::FETCH_OBJ));
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
            $stmt = Connection::getInstance()->prepare($sql);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':password', $password);

            if ($stmt->execute())
                return $this->bindUser($stmt->fetch(PDO::FETCH_OBJ));
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
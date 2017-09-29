<?php

namespace StudySilex\Service;


class Member
{
    protected $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function register($data)
    {
        try{
            $this->db->beginTransaction();
            $id = $this->db->lastInsertId();
            $sql = "INSERT INTO member SET email = :email, password = :password, created_at = now(), updated_at = now()";
            $statement = $this->db->prepare($sql);
            $statement->bindParam(':email', $data['email']);
            $statement->bindParam(':password', $this->hashPassword($id, $data['password']));

            $statement->execute();

        }
        catch (Exception $e)
        {
            $this->db->rollback();
            throw $e;
        }

        $this->db->commit();
    }

    /**
     * @param $id
     * @return string
     */
    private function getSalt($id)
    {
        return md5($id);
    }

    /**
     * @param $id
     * @param $password
     * @return string
     */
    private function hashPassword($id, $password)
    {
        $salt = $this->getSalt($id);
        $hash = '';
        for($i = 0; $i < 1024; $i++)
        {
            $hash = hash('sha256', $hash . $password . $salt);
        }

        return $hash;
    }
}
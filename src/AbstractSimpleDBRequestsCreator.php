<?php

/**
 * Class AbstractSimpleDB
 */
abstract class AbstractSimpleDBRequestsCreator
{

    /**
     * AbstractAdmin constructor.
     */
    public function __construct()
    {
        global $bd;
        $this->bd = $bd;
    }

    /**
     * Find row by field name and value, $params = ["field" => value], one param
     *
     * @param array $params
     * @param string $table
     * @return mixed
     * @throws Exception
     */
    protected function findOneBy(array $params, string $table)
    {
        $field = array_keys($params);
        $value = array_values($params);

        if(!$this->checkField($field[0], $table)){
            throw new Exception("Le champs ".$field[0]." n'existe pas dans la table de destination! ");
        }
        $sql = $this->bd->prepare("SELECT * FROM " .$table. " WHERE ".$field[0]." = ? LIMIT 1");
        $sql->execute([$value[0]]);
        $result = $sql->fetch();

        return $result;
    }

    /**
     * Find all elements of a $table
     *
     * @param string $table
     * @param int $limit
     * @return array
     */
    protected function findAll(string $table, int $limit = 10000)
    {
        $sql = $this->bd->prepare("SELECT * FROM " .$table. " WHERE id  > 0 LIMIT ".$limit);
        $sql->execute();
        $results = $sql->fetchAll();

        return $results;
    }

    /**
     * Find multipls row by one param or more
     *
     * @param array $params
     * @param int|null $limit
     * @param string $table
     * @return array
     * @throws Exception
     */
    protected function findBy(array $params,int $limit ,string $table)
    {
        $field = array_keys($params);
        $value = array_values($params);
        foreach ($field as $item){
            if(!$this->checkField($item, $table)){
                throw new Exception("Le champs ".$item." n'existe pas dans la table de destination! ");
            }
        }

        $len = count($field);
        $sql = "SELECT * FROM " .$table. " WHERE ".$field[0]." = ?";
        for ($i = 1; $i < $len; $i++){
            $sql .= " AND ".$field[$i]." = ? ";
        }
        $sql .= " LIMIT ".$limit;
        $req = $this->bd->prepare($sql);
        $req->execute($value);

        $results = $req->fetchAll();
        return $results;
    }

    /**
     * Find multipls row by one param or more + order by
     *
     * @param array $params
     * @param int $limit
     * @param string $table
     * @param array|null $order
     * @return array
     * @throws Exception
     */
    protected function findByWithOrder(array $params,int $limit ,string $table, array $order = null)
    {
        $field = array_keys($params);
        $value = array_values($params);
        foreach ($field as $item){
            if(!$this->checkField($item, $table)){
                throw new Exception("Le champs ".$item." n'existe pas dans la table de destination! ");
            }
        }

        $len = count($field);
        $sql = "SELECT * FROM " .$table. " WHERE ".$field[0]." = ?";
        for ($i = 1; $i < $len; $i++){
            $sql .= " AND ".$field[$i]." = ? ";
        }
        if($order != null){
            $sql .= " ORDER BY ".$order['champ']." ".$order['sens'];
        }
        $sql .= " LIMIT ".$limit;
        $req = $this->bd->prepare($sql);
        $req->execute($value);

        $results = $req->fetchAll();
        return $results;
    }


    /**
     * Update row by field name and value, $params = ["field" => $value , ...,"id" => $id], id should be the last key in params []
     *
     * @param array $params
     * @param string $table
     * @throws Exception
     */
    public function updateById(array $params, string $table)
    {
        $sql = "UPDATE ".$table." SET";
        $fields = array_keys($params);
        array_pop($fields);

        foreach ($fields as $field) {
            $sql .= " $field = ? ,";
        }
        $sql = rtrim($sql, ',');
        $sql .= "WHERE id = ? LIMIT 1";
        try {
            $rq = $this->bd->prepare($sql);
            $rq->execute(array_values($params));
        } catch (Exception $e) {
            die('#17 Erreur lors du transfert des données : ' . $e);
        }
    }

    /**
     * Insert data in db , $params = ["field" => $value]
     *
     * @param array $params
     * @param string $table
     * @return string
     */
    protected function set(array $params, string $table)
    {
        $fields = implode(",",array_keys($params));
        $firstValues = array_keys($params);

        $values = [];
        foreach ($firstValues as $value){
            $newValue = ':'.$value;
            array_push($values, $newValue);
        }
        $values = implode(",",$values);

        $sql = "REPLACE INTO $table (".$fields.") VALUES (".$values.")";

        try {
            $rq = $this->bd->prepare($sql);
            $rq->execute($params);
            return $this->bd->lastInsertId();
        } catch (Exception $e) {
            die('#17 Erreur lors du transfert des données : ' . $e);
        }
    }

    /**
     * Delete one element by id
     *
     * @param array $params
     * @param string $table
     * @return string
     */
    public function deleteById(array $params, string $table)
    {
        $sql = "DELETE FROM $table WHERE id = ?";

        try {
            $rq = $this->bd->prepare($sql);
            $rq->execute(array($params['id']));
            return $this->bd->lastInsertId();
        } catch (Exception $e) {
            die('#17 Erreur lors du transfert des données : ' . $e);
        }
    }

    /**
     * To check if a field exist in  $table
     *
     * @param string $field
     * @param string $table
     * @return bool
     */
    private function checkField(string $field, string $table)
    {
        $rows = $this->bd->query("SHOW COLUMNS FROM".$table);
        $results = $rows->fetchAll();
        foreach ($results as $result){
            if(in_array($field, (array) $result)){
                return true;
            }
        }

        return false;
    }

}
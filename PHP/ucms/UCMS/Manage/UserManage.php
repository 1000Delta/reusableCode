<?php
/**
 * 用户管理模块
 * 用户数据表
 *   CREATE TABLE ucms_user {
 *   id INT(20) PRIMARY_KEY AUTO_INCREMENT,
 *   uid VARCHAR(100) NOT NULL,
 *   passwd VARCHAR(255) NOT NULL,
 *   nickname VARCHAR(100)
 *   INDEX user uid(100);
 * }
 */

namespace MyClass\UCMS\Manage;

use CodeLib\a\aContentManage;
use MyClass\Database\DBC;

class UserManage extends aContentManage {
    
    const tableName = 'ucms_user';
    
    public function __construct(string $address, string $user, string $pass, string $dbName) {
        parent::__construct($address, $user, $pass, $dbName);
    }
    
    protected function paramCheck(&$data) {
        
        return settype($data, 'array') && isset($data['id']) && isset($data['uid']) && isset($data['passwd']) && isset($data['nickname']);
    }
    
    public function create($data) {

        if ($this->paramCheck($data)) {
    
            settype($data['uid'], 'string');
            settype($data['passwd'], 'string');
            settype($data['nickname'], 'string');
            $result = $this->database->insert(UserManage::tableName, [
                'uid' => $data['uid'],
                'passwd' => $data['passwd'],
                'nickname' => $data['nickname']
            ]);
            if ($result->rowCount()) {
                
                return 0;
            }
        }
        return 1;
    }
    
    public function update($data) {

        if ($this->paramCheck($data)) {
    
            settype($data['uid'], 'string');
            settype($data['passwd'], 'string');
            settype($data['nickname'], 'string');
            $result = $this->database->update(UserManage::tableName, [
                'uid' => $data['uid'],
                'passwd' => $data['passwd'],
                'nickname' => $data['nickname']
            ], 'WHERE id='.intval($data['id']));
            if ($result->rowCount()) {
                
                return 0;
            }
        }
        return 1;
    }
    
    public function read($index) {

        $result = $this->database->select(UserManage::tableName, ['*'], 'WHERE id='.intval($index));
        return $essay = $result->fetch(DBC::FETCH_ASSOC_DBC);
    }
    
    public function delete($index) {

        $result = $this->database->delete(UserManage::tableName, 'WHERE id='.intval($index));
        if ($result->rowCount()) {
            
            return 0;
        } else {
            
            return -1;
        }
    }
}
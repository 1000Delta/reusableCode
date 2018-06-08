<?php
/**
 * 消息管理模块
 * 数据表结构
 * CREATE TABLE ucms_msg {
 *   id INT(20) PRIMARY KEY AUTO INCREMENT,
 *   uid VARCHAR(100) NOT NULL,
 *   type VARCHAR(100) NOT NULL,
 *   time TIMESTAMP NOT NULL,
 *   content TEXT,
 *   FOREIGN KEY (uid) REFERENCES ucms_user (uid);
 * }
 */

namespace MyClass\UCMS\Manage;


use CodeLib\a\aContentManage;
use MyClass\Database\DBC;

class MsgManage extends aContentManage {

    const TABLE_NAME = 'ums_msg';
    
    public function __construct(string $address, string $user, string $pass, string $dbName) {
        parent::__construct($address, $user, $pass, $dbName);
    }
    
    protected function paramCheck(&$data) {
        
        return settype($data, 'array') && isset($data['id']) && isset($data['uid']) && isset($data['type']) && isset($data['time']) && isset($data['content']);
    }
    
    public function create($data) {
    
        settype($data['uid'], 'string');
        settype($data['content'], 'string');
        $result = $this->database->insert(MsgManage::TABLE_NAME, [
            'uid' => $data['uid'],
            'type' => strtolower($data['type']),
            'content' => $data['content']
        ]);
    
        if ($result->rowCount()) {
        
            return 0;
        } else {
        
            return -1;
        }
    }
    
    public function update($data) {
    
        if ($this->paramCheck($data)) {
    
            settype($data['content'], 'string');
            $result = $this->database->update(MsgManage::TABLE_NAME, [
                'content' => $data['content']
            ], 'WHERE id=' . intval($data['id']));
            if ($result->rowCount()) {
        
                return 0;
            }
        }
        return -1;
    }
    
    public function read($index) {

        $result = $this->database->select(MsgManage::TABLE_NAME, ['*'], 'WHERE id='.intval($index));
        return $essay = $result->fetch(DBC::FETCH_ASSOC_DBC);
    }
    
    public function delete($index) {

        $result = $this->database->delete(MsgManage::TABLE_NAME, 'WHERE id='.intval($index));
        if ($result->rowCount()) {
        
            return 0;
        } else {
        
            return -1;
        }
    }
}
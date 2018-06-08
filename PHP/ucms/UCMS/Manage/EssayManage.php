<?php
/**
 * 文章管理模块
 * 连接数据库进进行文章管理
 *
 * 数据库表结构
 * CREATE TABLE ucms_essay {
 *   id INT PRIMARY_KEY AUTO_INCREMENT,
 *   uid INT(10),
 *   time TIMESTAMP,
 *   title VARCHAR(255),
 *   content TEXT
 * } FOREIGN KEY (uid) REFERENCES ucms_user (uid);
 */

namespace MyClass\UCMS\Manage;
use CodeLib\a\aContentManage;

use  MyClass\Database\DBC;

class EssayManage extends aContentManage {

    const ESSAY_TABLE = 'umcs_essay';
    
    public function __construct(string $address, string $user, string $pass, string $dbName) {
        
        return parent::__construct($address, $user, $pass, $dbName);
    }
    
    private function securityString(string $str) {
    
        return addslashes(htmlspecialchars($str));
    }

    protected function paramCheck(&$data) {
    
        return settype($data, 'array') && isset($data['id']) && isset($data['title']) && isset($data['content']);
    }
    
    /*
     *
     * 传入数组结构
     * [
     *   'uid' => '',
     *   'title' => '',
     *   'content' => ''
     * ]
     */
    public function create($data) {

        if (settype($data, 'array') && isset($data['id']) && isset($data['uid']) && isset($data['title']) && isset($data['content'])) {
        
            $content = $this->securityString($data['content']);
            $title = $this->securityString($data['title']);
            $result = $this->database->insert(EssayManage::ESSAY_TABLE, [
                'title' => $title,
                'uid' => $data['uid'],
                'content' => $content
            ]);
            if ($result->rowCount()) {
        
                return 0;
            }
        }
        return -1;
    }

    public function update($data) {

        if ($this->paramCheck($data)) {
            
            $title = $this->securityString($data['title']);
            $content = $this->securityString($data['content']);
            $result = $this->database->update(EssayManage::ESSAY_TABLE, [
                'title' => $title,
                'content' => $content
            ], 'id='.$data['id']);
            if ($result->rowCount()) {
        
                return 0;
            }
        }
        return -1;
    }

    public function read($index) {

        $result = $this->database->select(EssayManage::ESSAY_TABLE, ['uid', 'title', 'content'], 'WHERE id='.intval($index));
        return $essay = $result->fetch(DBC::FETCH_ASSOC_DBC);
    }

    public function delete($index) {
        
        $result = $this->database->delete(EssayManage::ESSAY_TABLE, 'WHERE id='.intval($index));
        if ($result->rowCount() !== 0) {
            
            return 0;
        } else {
            
            return -1;
        }
    }
    
    public function getList(int $start, int $end) {
    
        $result = $this->database->select(EssayManage::ESSAY_TABLE, ['id', 'uid', 'title'], 'ORDER BY time ASC LIMIT '.$start.','.$end);
        $data = [];
        $i = 0;
        if ($result->rowCount()) {
    
            while ($item = $result->fetch(DBC::FETCH_ASSOC_DBC)) {
        
                $data[$i]['id'] = $item['id'];
                $data[$i]['uid'] = $item['uid'];
                $data[$i]['title'] = $item['title'];
                $i++;
            }
            return 0;
        } else {
            
            return -1;
        }
    }
}
<?php

class SqlHelper{
    private $conn;
    private $host = '127.0.0.1';
    private $user = 'root';
    private $password = 'root';
    private $db = 'server3';
    
    public function __construct(){
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->db);
        
        if($this->conn->connect_errno){
            die('数据库连接出错'.$this->conn->error);
        }
        $this->conn->set_charset('utf8');
    }
        
    /*
     * 执行单条 dql 语句，返回一个关联数组
     * 如果语句有错，将返回空值
     */
    public function execute_dql($sql){
        if($result = $this->conn->query($sql)){
            $row = $result->fetch_array(MYSQLI_ASSOC);
            //释放结果集
            $result->free();
            return $row;
        }
    }
    
    //执行单条dql语句,返回一个二维关联数组
    public function execute_dql_arr($sql){
        $res = $this->conn->query($sql);
        $arr=array();
        while($row=$res->fetch_array(MYSQLI_ASSOC)){
            $arr[]=$row;
        }
        $res->free();
        return $arr;
    }
    
    /*
     * 执行多条查询语句
     * 
     */
    public function execute_multy_dql($sql){
        $i = 0;
        if($this->conn->multi_query($sql)){
            do{
                if($result = $this->conn->store_result()){
                    while ($row = $result->fetch_row()){
                        $r[$i++] = $row; 
                    }
                    $result->close();
                }
            }while($this->conn->more_results() && $this->conn->next_result());
        }
        return $r;
    }
    
    /*
     * 修改指定字段的值
     */
    public function execute_dml($sql){
        if($this->conn->query($sql) === true){
            return 1;
        }else {
            return 0;
        }
    }
    
    /*
     * 关闭数据库连接
     */
    public function close(){
        $this->conn->close();
    }
}
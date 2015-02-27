<?php 
class db
{
	public $conn;
	public function __construct($host = '192.168.1.24',$user = 'aifang',$pwd = '123456')
	{
        $this->magic_quotes = get_magic_quotes_gpc();
		$this->conn = @mysql_connect($host,$user,$pwd);
		mysql_select_db('anjuke_meteor_db');
	}

	public function sel_wish()
	{
		$sql = 'SELECT * FROM wish';
		$result = mysql_query($sql,$this->conn);
		return mysql_fetch_array($result);
	}

	public function insert_wish($data)
	{
	    $sql = $this->getInsertString("wish",$data);
        mysql_query("set names utf8");
        mysql_query($sql);
	}
    public function update_wish($data,$con){
        $sql = $this->getUpdateString("wish",$data,$con);
       // $this->email_send();
        mysql_query("set names utf8");
        mysql_query($sql);
    }
    /*发送邮件方法*/
    public  function email_send(){
        $to = "sushazhang@anjuke.com";
        $subject = "Test mail";
        $message = "Hello! This is a simple email message.";
        $from = "sushazhang@anjuke.com";
        $headers = "From: $from";
        mail($to,$subject,$message,$headers);
        echo "Mail Sent.";
    }
    public function get_Rows($sql){
        mysql_query("set names utf8");
       $data=  mysql_query($sql);
        $ret = array();
        while ($row = @mysql_fetch_assoc($data))
        {
            $ret[] = $row;
        }
        return $ret;
    }
    public  function getInsertString($table, $data)
    {
        $n_str = '';
        $v_str = '';
        foreach ($data as $k => $v)
        {
            $n_str .= $k.',';
            $v_str .= "'".$v."',";
        }
        $n_str = preg_replace( "/,$/", "", $n_str );
        $v_str = preg_replace( "/,$/", "", $v_str );
        $str = 'INSERT INTO '.$table.' ('.$n_str.') VALUES('.$v_str.')';
        return $str;
    }
    public function getUpdateString($table, $data, $condtion)
    {
        $n_str = '';
        $str='';
        foreach ($data as $k => $v)
        {
            $n_str .= $k.'="'.$v.'",';
        }
        $new = 'UPDATE '.$table.' SET '.$n_str;
        $sql = substr($new,0,strlen($new)-1);
        foreach ($condtion as $kk => $vv)
        {
            $str .= $kk.'='.$vv;
        }
        $sql .= ' WHERE '.$str;
        return $sql;
    }
}
?>
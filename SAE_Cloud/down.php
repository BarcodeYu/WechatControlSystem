<?php  

    if ( ($_GET['token'] == "barcode")&&$_GET['data'] )
    {// bar0423,这相当于密码，在Arduino端改成相应的值即可
            $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
            $data = $_GET['data'];
            mysql_select_db("app_barcode", $con);//要改成相应的数据库名

            $result = mysql_query("SELECT * FROM switch");
            while($arr = mysql_fetch_array($result))
            {//找到需要的数据的记录，并读出状态值
                    if ($arr['ID'] == 1) 
                    {
                            $state = $arr['state'];
                    }
            }
            $dati = date("h:i:sa");//获取时间
            $sql ="UPDATE sensor SET timestamp='$dati',data = '$data'
            WHERE ID = '1'";//更新相应的传感器的值
            if(!mysql_query($sql,$con))
            {
                die('Error: ' . mysql_error());//如果出错，显示错误
            }
            mysql_close($con);

            if($state == "0")
            {          //关灯  
                echo "{";  
            }
            else if($state == "1")
            {    //开灯  
                echo "}";  
            } 

         //   echo "{".$state."}";//返回状态值，加“{”是为了帮助Arduino确定数据的位置
    }

    else
    {
            echo "Permission Denied";//请求中没有type或data或bar0423或bar0423错误时，显示Permission Denied
    }
 
?>
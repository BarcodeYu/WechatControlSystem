<?php  

    if ( ($_GET['token'] == "barcode")&&$_GET['data'] )
    {// bar0423,���൱�����룬��Arduino�˸ĳ���Ӧ��ֵ����
            $con = mysql_connect(SAE_MYSQL_HOST_M.':'.SAE_MYSQL_PORT,SAE_MYSQL_USER,SAE_MYSQL_PASS); 
            $data = $_GET['data'];
            mysql_select_db("app_barcode", $con);//Ҫ�ĳ���Ӧ�����ݿ���

            $result = mysql_query("SELECT * FROM switch");
            while($arr = mysql_fetch_array($result))
            {//�ҵ���Ҫ�����ݵļ�¼��������״ֵ̬
                    if ($arr['ID'] == 1) 
                    {
                            $state = $arr['state'];
                    }
            }
            $dati = date("h:i:sa");//��ȡʱ��
            $sql ="UPDATE sensor SET timestamp='$dati',data = '$data'
            WHERE ID = '1'";//������Ӧ�Ĵ�������ֵ
            if(!mysql_query($sql,$con))
            {
                die('Error: ' . mysql_error());//���������ʾ����
            }
            mysql_close($con);

            if($state == "0")
            {          //�ص�  
                echo "{";  
            }
            else if($state == "1")
            {    //����  
                echo "}";  
            } 

         //   echo "{".$state."}";//����״ֵ̬���ӡ�{����Ϊ�˰���Arduinoȷ�����ݵ�λ��
    }

    else
    {
            echo "Permission Denied";//������û��type��data��bar0423��bar0423����ʱ����ʾPermission Denied
    }
 
?>
<?php
function postmail_test($to,$name, $subject = "",$body = ""){
    //Author:Jiucool WebSite: http://www.jiucool.com 
    //$to ��ʾ�ռ��˵�ַ $subject ��ʾ�ʼ����� $body��ʾ�ʼ�����
    //error_reporting(E_ALL);
    error_reporting(E_STRICT);
    date_default_timezone_set("Asia/Shanghai");//�趨ʱ��������
    require_once('class.phpmailer.php');
    include("class.smtp.php"); 
    $mail             = new PHPMailer(); //newһ��PHPMailer�������
    $body             = eregi_replace("[\]",'',$body); //���ʼ����ݽ��б�Ҫ�Ĺ���
    $mail->CharSet ="UTF-8";//�趨�ʼ����룬Ĭ��ISO-8859-1����������Ĵ���������ã���������
    $mail->IsSMTP(); // �趨ʹ��SMTP����
    $mail->SMTPDebug  = 1;                     // ����SMTP���Թ���
                                           // 1 = errors and messages
                                           // 2 = messages only
    $mail->SMTPAuth   = true;                  // ���� SMTP ��֤����
    //$mail->SMTPSecure = "ssl";                 // ��ȫЭ��
    $mail->Host       = "mail.imu.edu.cn";      // SMTP ������
    $mail->Port       = 25;                   // SMTP�������Ķ˿ں�
    $mail->Username   = "";  // SMTP�������û���
    $mail->Password   = "";            // SMTP����������
    $mail->SetFrom('kewaike@imu.edu.cn', 'kewaike');
    $mail->AddReplyTo("kewaike@imu.edu.cn","kewaike");
    $mail->Subject    = $subject;
    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer! - From abc"; // optional, comment out and test
    $mail->MsgHTML($body);
    $address = $to;
    $mail->AddAddress($address, $name);
    //$mail->AddAttachment("images/phpmailer.gif");      // attachment 
    //$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment
    if(!$mail->Send()) {
        echo "Mailer Error: " . $mail->ErrorInfo;
    } else {
		$start = strpos($to, "@") + 1;
		$subEmail = substr($email, $start);
        echo "<p>ע��ɹ���<br />һ�⼤���ʼ��ѷ���������䣬�����ʼ��е���������������˻���<a href=\"http://mail.$subEmail\">ǰ������ ? </a></p>";
    }
}

?>
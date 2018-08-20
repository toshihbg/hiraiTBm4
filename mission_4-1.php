<?php

 header('Content-Type: text/html; charset=UTF-8');     //文字コードを指定

 //DBに接続
 $dsn = 'データベース名;';
 $user = 'ユーザー名';
 $password = 'パスワード';
 $pdo = new PDO($dsn,$user,$password);
 $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //エラーレポートと例外を投げる

//$pdo -> query("drop table m4table");

 //テーブル作成
 /*
 $sql = "CREATE TABLE m4table"
 	."("
	."id INT AUTO_INCREMENT PRIMARY KEY,"
	."name char(32),"
	."comment TEXT,"
	."date DATETIME,"
	."pass char(8)"
	.");";
 $stmt = $pdo -> query($sql);
 */


 try
  {
   //データ挿入
   //通常のデータ挿入
   if(!empty($_POST['na']) && !empty($_POST['msg']) && !empty($_POST['pw']) && empty($_POST['editnum']))     //編集対象番号以外の入力欄が空ではないとき
    {
     $sql = $pdo -> prepare("INSERT INTO m4table(id,name,comment,date,pass) VALUES(:id,:name,:comment,:date,:pass)");     //データ挿入の命令文
     $sql -> bindParam(':id',$id, PDO::PARAM_INT);     //idを数値でバインド
     $sql -> bindParam(':name',$name, PDO::PARAM_STR);     //「名前」を文字列でバインド
     $sql -> bindParam(':comment',$comment, PDO::PARAM_STR);     //「コメント」を文字列でバインド
     $sql -> bindParam(':date',$date, PDO::PARAM_STR);     //送信された日付を文字列でバインド
     $sql -> bindParam(':pass',$pass, PDO::PARAM_STR);     //パスワードを文字列でバインド
     $name = $_POST['na'];     //入力フォームから受け取った「名前」を変数に代入
     $comment = $_POST['msg'];     //入力フォームから受け取った「コメント」を変数に代入
     $date = date("Y/m/d H:i:s");     //送信された日付を変数に代入
     $pass = $_POST['pw'];     //入力フォームから受け取った「パスワード」を変数に代入
     $sql -> execute();     //INSERTを実行
    }
   //データを編集して挿入する場合
   elseif(!empty($_POST['na']) && !empty($_POST['msg']) && !empty($_POST['pw']) && !empty($_POST['editnum']))     //全ての入力フォームが空ではないとき
    {
       $id_edit = $_POST['editnum'];
       $name_edit = $_POST['na'];
       $comment_edit = $_POST['msg'];     //編集後のデータを入力フォームから受け取り変数に代入
       $date_edit = date("Y/m/d H:i:s");
       $pass_edit = $_POST['pw'];
       $sql = "update m4table set name='$name_edit',comment='$comment_edit',date='$date_edit',pass='$pass_edit' where id=$id_edit";     //データを編集
       $result = $pdo ->query($sql);

    }

   //データ削除
   if(!empty($_POST['del']) && !empty($_POST['pw']))     //削除対象番号とパスワードが空ではない場合
    {
     $sql = 'SELECT*FROM m4table';     //テーブル内のデータを取得
     $results = $pdo -> query($sql);
	    foreach($results as $row)
	     {
	      if($_POST['del'] == $row['id'] && $_POST['pw'] == $row['pass'])     //idとpwが一致した場合
	       {
     		$id_del = $_POST['del'];
     		$name_del = '';
    	 	$comment_del = '削除されました。';
     		$date_del = date("Y/m/d H:i:s");
		$pass_del = '';
     		$sql = "update m4table set name='$name_del',comment='$comment_del',date='$date_del',pass='$pass_del' where id=$id_del";
     		$result = $pdo ->query($sql);
	       }
	     }
    }

   //データ編集。対象データを入力フォームに再表示するための処理
   if(!empty($_POST['edit']))     //編集対象番号に値があった場合
    {
     $sql = 'SELECT*FROM m4table';     //テーブル内のデータを取得
     $results = $pdo -> query($sql);
	    foreach($results as $row)
	     {
	      if($_POST['edit'] == $row['id'] && $_POST['pw'] == $row['pass'])     //idとpwが一致した場合
	       {
		//入力フォームに再表示させるために対象の行の各データを変数に代入
		$row_id = $row['id'];
		$row_name = $row['name'];
		$row_comment = $row['comment'];
	        $row_pass = $row['pass'];
	       }
	      }
    }

  } 
 catch(PDOException $er)
  {
   print "Error:" . $er -> getmessage();
  }

?>

<html>

 <head>
  <meta charset="utf-8">
  <title>
   ミッション4-1
  </title>
  <font size="5" color="navy">簡易掲示板</font>
 </head>

 <body>

  <p>データ入力フォーム</p>
  <form method="post" action="mission_4-1.php">     <!--入力フォームを。post送信で「名前」「コメント」・パスワードを送る-->
   <!--名前入力欄。編集対象番号と投稿番号が一致した場合、名前を再表示-->
    <input type="text" name="na" placeholder="名前"
     value="<?php     echo  $row_name;     ?>" />
      <br/>
   <!--コメント入力欄。編集対象番号と投稿番号が一致した場合、コメントを再表示-->
    <input type="text" name="msg" placeholder="コメント"
     value="<?php     echo  $row_comment;     ?>" />
      <br/>
   <!--パスワード入力欄。-->
    <input type="text" name="pw" placeholder="パスワード（8文字以内）"
     value="<?php     echo  $row_pass;     ?>" />
   <!--編集対象番号表示欄-->
    <input type="hidden" name="editnum" value="<?php     echo  $row_id;     ?>" />
    <input type="submit" value="送信"/>     <!--送信ボタン-->
  </form>

  <p>削除フォーム</p>
  <form method="post" action="mission_4-1.php">     <!--削除フォーム。post送信で削除対象番号・パスワードを送る-->
   <input type="text" name="del" placeholder="削除対象番号"/>     <!--削除対象番号入力欄-->
     <br/>
   <input type="text" name="pw" placeholder="パスワード（8文字以内）"/>     <!--パスワード入力欄。-->
   <input type="submit" value="削除"/>     <!--削除ボタン-->
  </form>

  <p>編集フォーム</p>
  <form method="post" action="mission_4-1.php">   <!--編集対象番号入力フォーム。post送信で編集対象番号・パスワードを送る-->
   <input type="text" name="edit" placeholder="編集対象番号"/>     <!--編集対象番号入力欄-->
     <br/>
   <input type="text" name="pw" placeholder="パスワード（8文字以内）"/>     <!--パスワード入力欄。-->
   <input type="submit" value="編集"/>     <!--編集ボタン-->
  </form>


 </body>

</html>

<?php

 header('Content-Type: text/html; charset=UTF-8');     //文字コードを指定

 //DBに接続
 $dsn = 'データベース名;';
 $user = 'ユーザー名';
 $password = 'パスワード';
 $pdo = new PDO($dsn,$user,$password);
 $pdo -> setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);     //エラーレポートと例外を投げる

 try
  {
   //データ表示
   $sql = 'SELECT*FROM m4table';
   $results = $pdo -> query($sql);
 	  foreach($results as $row)
	   {
	    echo $row['id'].',';
	    echo $row['name'].',';
	    echo $row['comment'].',';
	    echo $row['date'].'<br>';
	   }
  } 
 catch(PDOException $er)
  {
   print "Error:" . $er -> getmessage();
  }

?>

<html>
  <head lang="ja">
    <meta http-equiv="Content-Type" content="text/html; charset=utf8">

    <title>練習(テキサスポーカー)</title>
  </head>
  
  <body>
  
    <form action="game.php" method="GET"> 
      開始人数の指定
      <br>
      <input type="radio" name="num" value="3" checked="checked">3人対戦　<br>
      <input type="radio" name="num" value="5">5人対戦 <br>
      <input type="radio" name="num" value="7">7人対戦 <br>
      <input type="submit" value="開始">
      <input type="hidden" name="game" value=1>
      <input type="hidden" name="bet" value=10>

    </form>
  </body>
</html>

<html>

  <head>
      <title>練習(テキサスポーカー)</title>
  </head>


  <body>
      <!-- スートの強さは無し -->
      
      <!-- 付ける機能
      check機能　-> 全員がcheckなら進行
      bet機能　-> 最初の賭け金を提示する
      raise機能　-> その進行ターンのみ継続
    　　最初のレイズはコール・ベット以上
      　　(10なら$10のレイズで$20が最低のレイズ)
    　次　以降はレイズ金額以上(
      　　($ポットで$10のレイズなら$30が最低のレイズ)
    　つまり　ポット+レイズがレイズの最低
  
      -in機能　-> した時点で手番を飛ばす　役判定あり
  
       インされて尚コール・レイズできる人がいれば、その
       達は手番あり
  
      d機能　-> した時点で手番を飛ばす　役判定なし
  
      敗判定　-> 基本役の強さで判定(評価点/)100)
     被っ  ているならカードの強さ(ペアの強さ)
 
      だ人の手番を飛ばす機能　-> moneyで判定？
 
      イヤーがfold -> foldを判定して『次へ』)し)か押せないようにする
      プレイヤーが飛ぶ　-> ゲーム終了

      ターン数設定　-> 満たし次第、ゲーム終了。最終のmoneyでランキング

      NPCの行動アルゴリズム　-> 評価点を流用予定(評価点/10)
      NPCの難易度と表情　-> 行動アルゴリズムに補正
       表情が読めなくなるor騙しに来る
      --> 
      
      <!------------------------------------------------------------>
      <?php /* 主な宣言 */
        if ( isset($_GET['game']) && $_GET['game']==1 ) $_COOKIE['PHPSESSID'] = "";

        session_start();

        /* 定数 */
        define( "HAND",  2 );
        define( "FIELD", 5 );
        define( "BLIND", 10 );
        define( "ACE", 14 );    /* Judでの仕様として14 */
        define( "END", 1);      /* ACEに対応する1 */

        $NEXT = false;

        $bet = $_GET['bet'];

        if ( isset($_GET['pot']) ) {
          $pot = $_GET['pot'];
        } else {
          $pot = 0;
        }


        if ( !isset($_SESSION['system']) || $_SESSION['system']==0 ) {
          ProgressReset();
          $_SESSION['num'] = $_GET['num'];
          $_SESSION['game'] = $_GET['game']; 
          $_SESSION['system'] = 1;
          $_SESSION['RAISE'] = false;
          $_SESSION['RESULT'] = false;
					$_SESSION['CHECK'] = false;

          $_SESSION['clist'] = array();
          $_SESSION['dlist'] = array();
          for ( $i=0; $i<$_SESSION['num']; $i++ ) {
            array_push( $_SESSION['clist'], false );
            array_push( $_SESSION['dlist'], false );
          }

          $pot = 0;

          /* suit : number 配列方向y:x */
          $DeckCards;
          $HandCards;
          $FeildCards;
          

          /* デッキ */
          $PokerCards = array( 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 1 );
          $DeckCards  = array( $PokerCards, $PokerCards, $PokerCards, $PokerCards );
          
          
          /* ハンド */
          $PokerHands = array( -1, -1 );
          $HandCards  = array($PokerHands, $PokerHands);
          

          /* フィールド */
          $PokerField = array( -1, -1 );
          $FieldCards = array( $PokerField, $PokerField, $PokerField, $PokerField, $PokerField );
        }
      ?>
      <!------------------------------------------------------------>


      <!------------------------------------------------------------>
      <?php /* 主な関数・クラス */
        function CreateMoney() {
           $_SESSION['moneylist'] = array();        
          for ( $i=0; $i<$_SESSION['num']; $i++ ) {
            array_push( $_SESSION['moneylist'], 1000 );
          }
        }

        function DrawCards() {
          if ( $_SESSION['system']==1 ) { /* 初回 */
            global $DeckCards;
            global $HandCards;
            $_SESSION['DeckCards'] = $DeckCards;
            $_SESSION['HandCards'] = $HandCards;
          }

          for ( $i=0; $i<HAND; $i++ ) {
            $suit = rand(0, 3);
            $number = rand(0, 12);

            if ( $_SESSION['DeckCards'][$suit][$number] != -1 ) {
              $_SESSION['HandCards'][$i][0] = $suit;
              $_SESSION['HandCards'][$i][1] = $_SESSION['DeckCards'][$suit][$number];
              $_SESSION['DeckCards'][$suit][$number] = -1;
            } else {
              $i--;
            }
          }

          return $_SESSION['HandCards'];
        }

        function SetField() {
          global $FieldCards;
          global $DeckCards;

          for ( $i=0; $i<3; $i++ ) {
            $suit = rand(0, 3);
            $number = rand(0, 12);

            if ( $DeckCards[$suit][$number] != -1 ) {
              $FieldCards[$i][0] = $suit;
              $FieldCards[$i][1] = $DeckCards[$suit][$number];
              $DeckCards[$suit][$number] = -1;
            } else {
              $i--;
            }
          }
        }

        function OpenField() {
          $num = 0;
          $start = 0;
          switch( $_SESSION['system'] ) {
            case 1:
              global $FieldCards;
              $_SESSION['FieldCards'] = $FieldCards;
              break;
            case 2: { $num = 3; $start = 0; break; }
            case 3: { $num = 4; $start = 3; break; }
            case 4: { $num = 5; $start = 4; break; }
          }

          if ( $_SESSION['system'] != 1 )
            echo "場にカードが公開されました<br>";

          for ( $i=$start; $i<$num; $i++) {
            $suit = rand(0, 3);
            $number = rand(0, 12);

            if ( $_SESSION['DeckCards'][$suit][$number] != -1 ) {
              $_SESSION['FieldCards'][$i][0] = $suit;
              $_SESSION['FieldCards'][$i][1] = $_SESSION['DeckCards'][$suit][$number];
              $_SESSION['DeckCards'][$suit][$number] = -1;
            } else {
              $i--;
            }
          }
        }

        function CallCheck() {
          for ( $i=0; $i<$_SESSION['num']; $i++ ) 
            if ( !(bool)$_SESSION['clist'][$i] ) return false;
          return true;
        }

        function CallReset() {
          for ( $i=0; $i<$_SESSION['num']; $i++ )
            $_SESSION['clist'][$i] = false;
        }

        function Raise($num) {
          for ( $i=0; $i<$_SESSION['num']; $i++ ) {
            if ( $i!=$num ) {
              $_SESSION['clist'][$i] = false;
            }
          }
        }

        function Progress($i) {
          for ( $count=0; $i!=0 && !CallCheck(); $count++, $i++ ) {
            if ( $i>=$_SESSION['num'] ) $i=0;
            
            if ( $_SESSION['player'][$i]->flag ) {
              echo "プレイヤー"."<br>";
              break;
            } else {
              // コンピューター
              echo "コンピュータ".$i."は";
              $_SESSION['player'][$i]->move();
              $_SESSION['clist'][$i] = true;
            }
          }
        }

        function TurnEnd() {
          $_SESSION['system']++;
          OpenField();
          CallReset();

          if ( $_SESSION['system'] >= 5 ){
            Result();
            echo "進行終了";
          } else {
            echo "進行を確認";
            global $NEXT;
            echo "start:".$_SESSION['start'];
            if ( $_SESSION['start']!=0 ) $NEXT = true;
          }
        }

        function ProgressReset() {
          global $bet;
          $bet = 10;
          $_SESSION['DeckCards'] = array();
          $_SESSION['HandCards'] = array();
          $_SESSION['FieldCards'] = array();
          $_SESSION['player'] = array();       
        }

        function output($limit, $cards) {
          echo "<br>";
          for ( $i=0; $i<$limit; $i++ ) {
            if ( $cards[$i][0]==-1 ) break;
            switch ( $cards[$i][0]) {
              case 0: { echo "♤"; break; }
              case 1: { echo '<span style="color:red">♡</span>'; break; }
              case 2: { echo "♧"; break; }
              case 3: { echo '<span style="color:red">♢</span>'; break; }
              default: echo'?';
            }
            echo "-";
            switch ( $cards[$i][1] ) {
              case 1:  { echo "A"; break; }
              case 11: { echo "J"; break; }
              case 12: { echo "Q"; break; }
              case 13: { echo "K"; break; }
              default: echo $cards[$i][1]; 
            }
            echo " ";
          }
        }

        function Result() {
          $_SESSION['RESULT'] = true;
          $_SESSION['system'] = 0;
          for ( $i=0; $i<$_SESSION['num']; $i++ ) {
            $_SESSION['moneylist'][$i] = $_SESSION['player'][$i]->money;
          }
        }
        
        class Player {
                  
          function __construct($money) {
            $this->hand = DrawCards();
            $this->hand = Jud_sort($this->hand);
            $this->flag = true;

            /* 
              Hi-card、Straight、Flash、Straight-Flash同士の勝敗用
              ハイカードを強いカードから降順に
              (役があるカードを優先)
            */
            $this->hi_card = array(0, 0, 0, 0, 0);
            
            /* 役に使用されるカードを記録 */
            /*
              pair系同士の勝敗用
              [0] -> 1pairの数位
              [1] -> 2pairのうち、1つ目の数位(高いほう)
              [2] -> 2pairのうち、2つ目の数位(低いほう)
              [3] -> 3cardの数位
              [4] -> FullHouseの3cardに使われる数位
              [5] -> 4cardの数位
            */
            $this->rank = array(0, 0, 0, 0, 0, 0);
            $this->money = $money;
          }

        }

        class Enemy extends Player {
          
          function __construct($serial, $money) {
            parent::__construct($money);
            $this->flag = false;
            $this->serial = $serial;
          }

          function move() {
            global $bet, $pot;

            $move = rand(1, 10);
            if ( $move>8 &&  $bet<1000 ) {
              Raise($this->serial);
              $bet += 10;
              $_SESSION['start'] = $this->serial;
              echo "レイズしました";
              echo "<br>";
            } else {
              echo "コールしました";
              echo "<br>";
            }

            $this->money -= $bet;
            $pot += $bet;
          }

        }
      ?>
      <!------------------------------------------------------------>


      <!------------------------------------------------------------>
      <?php /*　判定機構 */
        function Jud_sort($cards) {
          $tmp_num = 0;
          $tmp_suit = 0;
          $ace1 = 0;
          $ace2 = 0;

          for ( $m=0; $m<count($cards); $m++ ) {
            for ( $n=count($cards)-1; $n>$m; $n-- ) {
              if ( $cards[$n][1] == 1 ) $ace1 = 13;
              else $ace1 = 0;
              if ( $cards[$n-1][1] == 1 ) $ace2 = 13;
              else $ace2 = 0;
              
              if ( $cards[$n][1]+$ace1 > $cards[$n-1][1]+$ace2 ) {
                $tmp_suit = $cards[$n][0];
                $tmp_num = $cards[$n][1];
                $cards[$n][0] = $cards[$n-1][0];
                $cards[$n][1] = $cards[$n-1][1];
                $cards[$n-1][0] = $tmp_suit;
                $cards[$n-1][1] = $tmp_num;
              }
            }
          }

          return $cards;
        }

        function Jud_Royal($cards, $hist, $player_number) {  

          if ( Jud_StraightFlash($cards, $hist, $player_number) &&  $cards[0][1]==1 ) {
            return true;
          }

          return false;
        }

         function Jud_StraightFlash($cards, $hist, $player_number) {

          /*
            FlashとStraightが成立しているか確認する
            (ただし、FlashとStraightが同時に成立しても
            Straight-Flashが成立しない可能性はある)
            
            例)
            手札: ♤-A ♤-K
            場　: ♤-Q ♤-J ♡-10 ♤-5 ♢-5
            ここから5枚で役を作るため、Aを最高数位とするStraightは
            Straight-Flashではない
          */
          if ( Jud_Flash($cards, $player_number) && Jud_Straight($hist, $player_number) ) {
            
            /* Jud_Straightのプログラムと同様 */
            for ( $i=ACE; $i>(END+4); $i-- ) {
              if ( $hist[$i] == 0 )   continue;
              if ( $hist[$i-1] == 0 ) continue;
              if ( $hist[$i-2] == 0 ) continue;
              if ( $hist[$i-3] == 0 ) continue;
              if ( $hist[$i-4] > 0 ) {

                /* Straightのカード達がFlashか判定 */
                $sf_array = array();
                $hist_num = 0;

                /*
                  スートの情報を持つcardsはJud_sortによりソートされている
                  そのため、Straightの成立が確認されたすべてのhistから
                  これにあたるカードの枚数を取り出す必要がある

                  例)
                  cards: ♤-A ♡-A ♤-K ♤-Q ♤-J ♤-10 ♤-9
                  このとき、Straightの成立に必要な5枚を取り出すには
                  6枚目のカードまで確認しなくてはならない
                */
                for ( $j=0; $j<5; $j++ ) $hist_num += $hist[$i-$j];

                /*
                  Straightの最高数位($i)がcardsのどこに当たるかを調べる必要がある
                  始点が判明したら、そこから$hist_num分を配列に格納
                  ($iがACEである14のときは例外としてcardsを1と比べる)
                */
                for ( $k=0; $k<count($cards); $k++ ) {
                  if ( ($i==ACE && $cards[$k][1]==1) || $cards[$k][1]==$i ) {
                    for ( $l=0; $l<$hist_num; $l++ ) {
                      array_push( $sf_array, array($cards[$k+$l][0], $cards[$k+$l][1]) );
                    }
                    break;
                  }
                }

                /* 成立時、Hi-cardはFlashの判定で入力されているので省略 */
                if ( Jud_Flash($sf_array, $player_number) ) {
                  return true;
                }
              }
            }
          }

          return false;
        }

        function Jud_4Card($hist) {
          for ( $i=ACE; $i>END; $i-- )  {
            if ( $hist[$i]==4 ) {
              $_SESSION['player'][$player_number]->rank[5] = $i;
              return true;
            }
          }

          return false;
        }

        function Jud_FullHouse($hist, $player_number) {
          for ( $m=ACE; $m>END; $m-- ) {
            if ( $hist[$m]==3 ) {
              for ( $n=ACE; $n>END; $n-- ) {
                /*
                  3Cardが2つある場合にもFullHouseは成立する
                  3Cardと4CardでもFullHouseは作れるが、4Cardの方が役が高いため無視
                */
                if ( $m != $n && ($hist[$n]==2 || $hist[$n]==3) ){
                  $_SESSION['player'][$player_number]->rank[4] = $m;
                  return true;
                }
              }
            }
          }

          return false;
        }

        function Jud_Flash($cards, $player_number) {
          /* 各スートの枚数を数える */
          $suit = array(0, 0, 0, 0);
          for ( $i=0; $i<count($cards); $i++ ) {
            $suit[$cards[$i][0]]++;
          }

          for ( $i=0; $i<count($suit); $i++ ) {
            if ( $suit[$i]>=5 ) {
              /* 同じスートが5枚以上で当該の数位をarrayに格納 */
              $fl_array = array();
              for ( $j=0; $j<count($cards); $j++ ) {
                if ( $cards[$j][0]==$i ) {
                  array_push( $fl_array, array($cards[$j][0], $cards[$j][1]) );
                }
              }

              /*
                同じスートのカードが5枚以上ある可能性があるため
                ソートしたうえで末尾(数位の低い)カードは削除
              */
              $fl_array = Jud_sort($fl_array);
              for ( $k=5; $k<=count($fl_array); $k++ ) {
                unset($fl_array[$k]);
              }

              /* 配列同士で書き換え スートの情報は削除 */
              $fl_array_marge = array();
              for ( $l=0; $l<count($fl_array); $l++ ) {
                array_push($fl_array_marge, $fl_array[$l][1] );
              }
              array_merge($_SESSION['player'][$player_number]->hi_card, $fl_array_marge);
              return true;
            }
          }

          return false;
        }

        function Jud_Straight($hist, $player_number) {
          /* 
            Aと2は連続しない 
            5が最高数位のストレートは成立しない (5-4-3-2-X)
            ->ループの終わりをEND+4に設定する (5よりおおきいならループ)
          */
          for ( $i=ACE; $i>(END+4); $i-- ) {
            /* i番目の数位のカードを持っていないならコンティニュー */
            if ( $hist[$i] == 0 )   continue;
            if ( $hist[$i-1] == 0 ) continue;
            if ( $hist[$i-2] == 0 ) continue;
            if ( $hist[$i-3] == 0 ) continue;

            /* 数位の連続するカードをもっていたら */
            if ( $hist[$i-4] > 0 ) {
              $st_array = array( $i, $i-1, $i-2, $i-3, $i-4 );
              return true;
            }
          }
        }

        function Jud_3Card($hist, $player_number) {
          for ( $i=ACE; $i>END; $i-- ) {
            if ( $hist[$i]==3 ) {
              $_SESSION['player'][$player_number]->rank[3] = $i;
              return true;
            }
          }

          return false;
        }

        function Jud_2Pair($hist, $player_number) {
          /*
            hist[$m]==3や4の場合もPairは存在するが
            これより高い役で判定されるので無視する
          */
          for ( $m=ACE; $m>END; $m-- ) {
            if ( $hist[$m]==2 ) {
              for ( $n=$m-1; $n>END; $n-- ) {
                if ( $hist[$n]==2 ) {
                  $tp_array = array();

                  /* 1枚所持のうち、最高数位を探す */
                  for ( $i=ACE; $i>END; $i-- ) {
                    if ( $hist[$i]==1 ) {
                      $tp_marge = $i;
                      break;
                    }
                  }

                  /* 2Pairの成立に必要なカードと2p_margeを格納 */
                  array_push( $tp_array, $m );
                  array_push( $tp_array, $m );
                  array_push( $tp_array, $n );
                  array_push( $tp_array, $n );
                  array_push( $tp_array, $tp_marge );
                  
                  $_SESSION['player'][$player_number]->rank[2] = $m;
                  $_SESSION['player'][$player_number]->rank[1] = $n;
                  array_merge( $_SESSION['player'][$player_number]->hi_card, $tp_array );
                  return true;
                }
              }
            }
          }

          return false;
        }

        function Jud_1Pair($hist, $player_number) {
          /*
            hist[$m]==3や4の場合もPairは存在するが
            これより高い役で判定されるので無視する
          */
          for ( $i=ACE; $i>END; $i-- ) {
            if ( $hist[$i]==2 ) {
              $op_array = array();
              $op_marge = array();
              
              /*
                1枚所持のうち、最高数位から3枚目までを探す
                (余分なカードも格納してしまうが、使用しないため無視する)
              */
              for ( $j=ACE; $j>END; $j-- ) {
                if ( $hist[$j]==1 ) {
                  array_push( $op_marge, $j );
                }
              }

              /* 2Pairの成立に必要なカードと2p_margeを格納 */
              array_push( $op_array, $i );
              array_push( $op_array, $i );
              array_push( $op_array, $op_marge[0] );
              array_push( $op_array, $op_marge[1] );
              array_push( $op_array, $op_marge[2] );
              
              $_SESSION['player'][$player_number]->rank[0] = $i;
              array_merge( $_SESSION['player'][$player_number]->hi_card, $op_array );
              return true;
            }
          }

          return false;
        }

        function Jud($player_number) {
          $cards = array();   /* 手札+場の札 */
          $point = 0;         /* $cardsの評価点 */

          $hand = $_SESSION['player'][$player_number]->hand;
          $field = $_SESSION['FieldCards'];

          /* 手札を格納 */
          array_push( $cards, $hand[0] );
          array_push( $cards, $hand[1] );

          /* 現時点で場に出ている札を格納 */
          for ( $i=0; $i<FIELD && $field[$i][0]!=-1; $i++ ) {
            array_push( $cards, $field[$i] );
          }

          /* 数位の強いほうから降順にソート */
          $cards = Jud_sort($cards);

          /*
            0~13の配列を用いて各カードを何枚持っているかを各数位($hist[数位])に格納
            histは15(0~14)の要素を持つが、[0][1]は使用しない (一応[1]は、Aのカウントの入力がある)
            [2]=2 [12]=Q のように枚数と対応 ただし、Aは[14]([ACE])と対応させる
          */
          $hist = array( 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0 );
          for ( $i=0; $i<count($cards); $i++ )
            $hist[$cards[$i][1]]++;
          $hist[ACE] = $hist[1];

          // print_r($hist);


          /* 役が成り立っていれば$pointに評価点を加算*/
          /*
            Royal-Straight-Flash  ...  900 ok
            Straight-Flash  .........  800 ok
            4 Card  .................  700 ok
            Full-house  .............  600 ok
            Flash  ..................  500 ok
            Straight  ...............  400 ok
            3 Card  .................  300 ok
            2 Pair  .................  200 ok
            1 Pair  .................  100 ok
            Hi-card  ................  2~14 (2~Aに対応)
          
            $cards内の5枚まで使い、評価点の高い役を採用する 
            (上から順に成り立っているか見ていくだけ)
          */
          if ( $player_number==0 ) echo "プレイヤーは";
          else echo "コンピューター".$player_number."は";

          if ( Jud_Royal($cards, $hist, $player_number) ){
            echo "Royal".'<br>';

          } else if ( Jud_StraightFlash($cards, $hist, $player_number) ) {
            echo "StraightFlash".'<br>';

          } else if ( Jud_4Card($hist, $player_number) ) {
             echo "4Card".'<br>';

          } else if ( Jud_FullHouse($hist, $player_number) ) {
            echo "FullHouse".'<br>';

          } else if ( Jud_Flash($cards, $player_number) ) {
            echo "Flash".'<br>';

          } else if ( Jud_Straight($hist, $player_number) ) {
            echo "Straight".'<br>';

          } else if ( Jud_3Card($hist, $player_number) ) {
            echo "3Card".'<br>';

          } else if ( Jud_2Pair($hist, $player_number) ) {
            echo "2Pair".'<br>';

          } else if ( Jud_1Pair($hist, $player_number) ) {
            echo "1Pair".'<br>';

          } else {
            echo "ブタ".'<br>';
          }
        }
      ?>
      <!------------------------------------------------------------>


      <!------------------------------------------------------------>
      <?php /* メイン */
        if ( $_SESSION['game'] > 0 ) {
           if ( $_SESSION['game']==1 ) CreateMoney();
          $_SESSION['game'] = 0;

          $_SESSION['player'] = array();
          for ( $i=0; $i<$_SESSION['num']; $i++ ) {
            if ( $i==0 ) {
              array_push( $_SESSION['player'], new Player($_SESSION['moneylist'][$i]) );
            } else {
              array_push( $_SESSION['player'], new Enemy($i, $_SESSION['moneylist'][$i]) );
            }
          }

          OpenField();

          if ( isset($_SESSION['start']) ) {
            $_SESSION['start'] = ($_SESSION['start']+1) % $_SESSION['num'];
          } else {
            $_SESSION['start'] = rand(0, $_SESSION['num']-1);
          }
          echo "start:".$_SESSION['start']."<br>";

          $_SESSION['player'][$_SESSION['start']]->money -= $bet;
          $pot += $bet;
          $_SESSION['clist'][$_SESSION['start']] = true;

          Progress( $_SESSION['start']+1 );

          if ( CallCheck() ) {
            TurnEnd();
          }

        } else {
          $select = $_GET['select'];
          echo $select;

          switch ( $select ) {
            case 1:
              $_SESSION['player'][0]->money -= $bet;
              $pot += $bet;
              $_SESSION['clist'][0] = true;
              Progress(1);
              break;
            case 5:
              Progress($_SESSION['start']);
              break;
          }

          
          if ( CallCheck() ) TurnEnd();
        }


        /* Debug */

        for ( $i=0; $i<$_SESSION['num']; $i++ ) {
          output(HAND, $_SESSION['player'][$i]->hand);
          Jud($i);
          echo $_SESSION['player'][$i]->money;
        }
        if ( $_SESSION['system']!=1 )
          output(FIELD, $_SESSION['FieldCards']);

        echo "<br><br>現在のシーン".$_SESSION['system'];
        echo "<br>";
        var_dump($_SESSION['clist'][0]);
        var_dump($_SESSION['clist'][1]);
        var_dump($_SESSION['clist'][2]);
        
        // var_dump($_SESSION['FieldCards']);


        session_write_close();
      ?>
      <!------------------------------------------------------------>


      <!------------------------------------------------------------>
      <!-- /* HTML */ -->
      <form action="game.php" method="GET">
      
        <br>

        <?php 
          if ( $_SESSION['RAISE'] ) {
            echo '<input type="radio" value=0 name="select" id="check">';
            echo '<label for="check">チェック</label>';
          }
        ?>

        <?php 
        function dis() {
          global $NEXT;
          if ( $NEXT ) echo 'disabled="disabled"';
        }
        ?>

        <p>レイズ金額<input type="number" name="bet" min=<?php echo $bet; ?> max=1000 step=10 value=<?php echo $bet; ?>></p>
					<?php //CHECKに対応してコールとチェックを変更?>
					<input type="radio" value=1 name="select" id="call" checked="checked" <?php dis(); ?>>
        <label for="call">コール</label>
        <input type="radio" value=2 name="select" id="raise" <?php dis(); ?>>
        <label for="raise">レイズ</label>
        <input type="radio" value=3 name="select" id="drop" <?php dis(); ?>>
        <label for="drop">ドロップ</label>
        <input type="radio" value=4 name="select" id="all" <?php dis(); ?>>
        <label for="all">オールイン</label>
        <br>
        <br>
        <?php
          if ( !$_SESSION['RESULT'] ) {
            if ( $NEXT ) {
              echo '<input type="submit" value="次へ">';
              echo '<input type="hidden" name="select" value=5>';
            } else {
              echo '<input type="submit" value="決定">';
            }
            echo '<input type="hidden" name="pot" value='.$pot.'>';
          } else {
            echo '<input type="submit" value="次のゲームへ">';
            echo '<input type="hidden" name="game" value=2>';
            echo '<input type="hidden" name="num" value='.$_SESSION['num'].'>';
          }
        ?>
      </form>

      <form action="index.php">
      <input type="submit" value="タイトルに戻る">
      </form>
      <!------------------------------------------------------------>

  </body>
    
</html>

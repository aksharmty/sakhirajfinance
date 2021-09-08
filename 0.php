<meta http-equiv="refresh" content="10000; url=#" />
<body style="background-color:#000000; color:white;">
<?php 
$botid = $_GET['id']; echo " bot id " . $botid;
$cand = "candle".$botid;  echo " candle " . $cand;
include "connect.php";
define('TIMEZONE', 'Asia/kolkata');
date_default_timezone_set(TIMEZONE);
  $date = DATE("Y-m-d H:i:s"); 
///
$coinsql = "SELECT * FROM coin where id = '$botid'";
$coinsqlresult = $connection->query($coinsql);
if ($coinsqlresult->num_rows > 0) {
  // output data of each row
  while($row0 = $coinsqlresult->fetch_assoc()) {
    $coin = $row0["coin"]; $preq = $row0["quantity"]; $market = $row0["market"]; $coin1ch = $row0["ch"]; $assetc = $row0['assetc']; $assetm = $row0['assetm']; $preq = $row0['quantity']; $preq = $row0['qu'];
    echo "id: " . $row0["id"]. " - Coin1: " . $row0["coin"]. " " . $row0["ch"]. "<br>";
 
}
}  
$pair = $coin.$market;
echo "pair : ",$pair; 
echo "<br>preq : ",$preq; 

$sql = mysqli_fetch_array(mysqli_query($connection,"select * from trade where currency ='$coin' order by id desc limit 1")); 
$sqlwait = mysqli_fetch_array(mysqli_query($connection,"select * from trade where currency ='$coin' AND type = '0' order by price asc limit 1"));
$sqls = mysqli_fetch_array(mysqli_query($connection,"select * from trade where currency ='$coin' AND sa != '1' AND type = '3' order by id asc limit 1")); 

$orderbookurl = 'https://api3.binance.com/api/v3/depth?symbol=$pair';
//$orderurl = 'https://api3.binance.com/api/v3/order';
$tickerurl = "https://api3.binance.com/api/v3/ticker/bookTicker?symbol=$pair";
$urlcan = "https://api3.binance.com/api/v3/klines?symbol=$pair&interval=1h&limit=20";

$coinstatus0 = mysqli_fetch_assoc(mysqli_query($connection,"select * from coin where coin = '$coin'"));
$coinstatus = $coinstatus0["status"]; echo "status " . $coinstatus;
if($coinstatus == "enable"){
$sql123 = mysqli_query($connection,"TRUNCATE TABLE $cand");
$curlcan = curl_init();
curl_setopt($curlcan, CURLOPT_URL, $urlcan);
curl_setopt($curlcan, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlcan, CURLOPT_HEADER, false);
$datacan = curl_exec($curlcan);
curl_close($curlcan);
//print_r($datacan);
$arraycan=json_decode($datacan, true);
 foreach($arraycan as $row) 
          { 

      $query = mysqli_query($connection,"INSERT INTO $cand(open,close,min,max,timestamp,volume) VALUES ('".$row["1"]."','".$row["4"]."','".$row["3"]."','".$row["2"]."','".$row["6"]."','1'); "); 
    //1 = open, 2=high,3=low,4=close,6=endTime 
          }
?>
<?php 
//market bal
$sqla = mysqli_fetch_array(mysqli_query($connection,"select * from admin where status ='1' order by id desc limit 1"));
 $api_key = $sqla["api"];
$secret = $sqla["secret"];
$mp = $sqla["firstorder"];
$wp = $sqla["wp"];

$opt = [
    "http" => [
        "method" => "GET",
        "header" => "User-Agent: Mozilla/4.0 (compatible; PHP Binance API)\r\nX-MBX-APIKEY: {$api_key}\r\n"
    ]
];
$context = stream_context_create($opt);
$params['timestamp'] = number_format(microtime(true)*1000,0,'.','');
$query = http_build_query($params, '', '&');
$signature = hash_hmac('sha256', $query, $secret); 
//$endpoint = "https://api.binance.com/wapi/v3/accountStatus.html?{$query}&signature={$signature}";
$endpoint = "https://api.binance.com/api/v3/account?{$query}&signature={$signature}";
$res = json_decode(file_get_contents($endpoint, false, $context), true);
//print_r($res);
//print_r($endpoint);
//echo " query ".$query;
$marketasset = $res["balances"]["$assetm"]["asset"];
$marketbalance = $res["balances"]["$assetm"]["free"];
$coinasset = $res["balances"]["$assetc"]["asset"];
$coinbalance =   $res["balances"]["$assetc"]["free"]; //doge 179, trx 22 ,bnb 4 ,usdt 11
//echo "<br> a ".$res["balances"]["11"]["free"];
echo "<br> market asset : ", $market . ':' . $marketasset;
echo "<br> market bal : ", $market . ':' . $marketbalance;
 //coin bal 
echo "<br> coin asset : ", $coin . ':' . $coinasset;
echo " coin bal : ",$coin . ':' . $coinbalance;
?>
<?php
//ask - bid check
$urlask = ($tickerurl);
$curlask = curl_init();
curl_setopt($curlask, CURLOPT_URL, $urlask);
curl_setopt($curlask, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlask, CURLOPT_HEADER, false);
$dataask = curl_exec($curlask);
curl_close($curlask);
//print_r($dataask);
$ass=json_decode($dataask, true);
$ask=$ass['askPrice'];
$bid=$ass['bidPrice'];
echo "<br>ASK : ", $ask . " BID " . $bid ."<br>";
?>
<br>---------------------------------------------------<br>
<?php
$last4 = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM $cand where id ='17'"));
echo "<br> last 4th bar ";
$id4 = $last4['id']; echo "<br> id4 ". $id4;
$close4 = $last4['close'];     echo "<br> close4 ". $close4;
$open4 = $last4['open'];     echo "<br> open4 ". $open4;

$last3 = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM $cand where id ='18'"));
echo "<br> last 3rd bar ";
$id3 = $last3['id']; echo "<br> id3 ". $id3;
$close3 = $last3['close'];     echo "<br> close3 ". $close3;
$open3 = $last3['open'];     echo "<br> open3 ". $open3;
$lastcan = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM $cand where id ='19'"));
echo "<br> lastbar bar ";
if($lastcan['open'] < $lastcan['close']){ $colorcu0="green"; $can2 = $lastcan['close'];}else{$colorcu0="red";}
echo "<font color ='$colorcu0'>";
$lastid = $lastcan['id']; echo "<br> id ". $lastid;
$lastclose = $lastcan['close']; echo "<br> close ". $lastclose;
$lastopen = $lastcan['open'];   echo "<br> open ".$lastopen;
$lastmin = $lastcan['min'];     echo "<br> min ". $lastmin;
$lastmax = $lastcan['max'];     echo "<br> max ". $lastmax;
echo "</font>";
echo "<br> current bar ";
$cucan = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM $cand where id='20'"));
if($cucan['open'] < $cucan['close']){ $colorcu="green";}else{$colorcu="red";}
echo "<font color ='$colorcu'>";
$cuid = $cucan['id']; echo "<br>cu id ". $cuid;
$cuopen = $cucan['open']; echo "<br>cu open ". $cuopen;
$cuclose = $cucan['close']; echo "<br>cu close ". $cuclose;
$cumax = $cucan['max']; echo "<br>cu max ". $cumax;
//$pivet = number_format(($lastclose+$lastmin+$lastmax+$cuopen)/4,11,".", "");
//echo "<br> pivet ". $pivet;
echo "</font>";
?>
<?php
//vol and quantity
$fcount0 = mysqli_fetch_assoc(mysqli_query($connection,"select count(status) as fcount from coin where status = 'enable' AND market='$market'"));
$fcount = $fcount0['fcount']; echo " <br>fcount ".$fcount; 
$firstfund = ($marketbalance/$fcount)/3; echo "<br> first fund " . $firstfund;
// risk management
if($firstfund > $mp){ $wf = $firstfund;} else { $wf = $mp;}
echo "<br> wf" .$wf;
echo "<br> fund for this trade use ".$market ." = ".$wf;
$qq = $wf/$ask/1; //echo "qq " .$qq;
$quantity = floor($qq/$preq)*$preq;
echo "<br>Buy Qq ", $quantity; 
?>
<?php
echo "<br>Buy price : ". $ask;
$buy = $ask;
$sell0 = $buy+$buy*1/100;
$sellprice = number_format($sell0,8, ".", ""); echo "<br>sellprice :",$sellprice;
$btcl = $quantity*$buy; $btclow = number_format($btcl,11);
echo "<br>$market fund required :",$btclow;
?>
<br>---------------------------<br>
<?php
$idu = $sql['id']; echo "<br>idu :" , $idu;
$typw = $sqlwait['type']; echo "<br>typw :" , $typw;
$typ = $sql['type']; echo "<br>typ :" , $typ;
$sell123 = $sql['sell']; echo "<br>sell123 :" , $sell123;
$psell = $sql['psell']; echo "<br>psell ". $psell;
$dlastbal = $sql['dlastbal']; echo "<br>dlastbal :" , $dlastbal;
$lastbal = $sql['lastbal']; echo "<br>lastbal :" , $lastbal;
$lastprice = $sql['price']; echo "<br>lastprice :" , $lastprice;
if($typw == '0'){ $levelprice = $sqlwait['stoploss']; echo " sql wait";} else { $levelprice = $sql['stoploss']; echo " wait idu";}
 echo "<br>levelprice :" , $levelprice;
$waitprice=$levelprice-$levelprice*$wp/100;echo "<br>waitprice ". $waitprice;
$hmas = $sql['hmas'];
$buycid=$sql['clientOrderId']; echo "<br>buycid :" , $buycid;
$sellcid=$sqls['sellcid']; echo "<br>sellcid :" , $sellcid;
$sellprice0=$sql['sellprice']; echo "<br>sellprice0 :" , $sellprice0;
$ba=$sql['ba']; echo "<br>ba :" , $ba;
$sa=$sqls['sa']; echo "<br>sa :" , $sa;
$said=$sqls['id']; echo "<br>said :" , $said;
?>
<br>---------------------------<br>
<?php
if($psell == "1" && $sellprice0 < $ask){ $psellup = mysqli_query($connection,"update trade set psell = '2' , sellprice ='$ask' where id = '$idu'"); }else{  echo " psell ready";  }
?>
<?php
// buy average cal
if($idu < 1){ $typ = 1;}
if($idu > 0){
if($ba != 1){ $avgcid = $buycid; $basa = "ON";}
if($sa == 2){
if($sellcid != "0" && $sa != 1 && $ba == 1){$avgcid = $sellcid; $basa = "ON";
echo " <br>basa sell";}}
if($basa == "ON"){ echo " onnnn";
// av code start

$orderId=$avgcid; echo "<br> orderid ".$orderId;
$chav = curl_init();
$timestamp = round(microtime(true) * 1000);
$querystringav = "symbol=$pair&orderId=$orderId&recvWindow=50000&timestamp=".$timestamp;
$signature0av = hash_hmac('SHA256',$querystringav ,$secret);
curl_setopt($chav, CURLOPT_URL, "https://api.binance.com/api/v3/order?symbol=$pair&orderId=$orderId&recvWindow=50000&timestamp=$timestamp&signature=$signature0av");
//curl_setopt($chav, CURLOPT_URL, "https://api.binance.com/api/v3/order?symbol=$pair&origClientOrderId=$orderId&recvWindow=50000&timestamp=$timestamp&signature=$signature0av");
curl_setopt($chav, CURLOPT_RETURNTRANSFER, 1);

$headers = array();
$headers[] = "X-Mbx-Apikey: $api_key";
curl_setopt($chav, CURLOPT_HTTPHEADER, $headers);
$resultav = curl_exec($chav);
print_r($resultav);
if (curl_errno($chav)) {
    echo 'Error:' . curl_error($chav);
}
curl_close ($chav);
$ravg=json_decode($resultav, true);
$fcost=$ravg['price'];
$quantity123=$ravg['origQty'];
$bid123=$ravg['clientOrderId'];
 
//insert order details
          echo ".$ravg[orderId].";
          echo ".$ravg[executedQty].";
          echo ".$ravg[clientOrderId].";
          echo ".$ravg[side].";
          echo "avg price :", $fcost;
          $fcost1=$ravg['cummulativeQuoteQty']; echo "avg price1 :", $fcost1;
          $fstatus=$ravg['status']; echo "fstatus :", $fstatus;
           
$fsellprice = number_format($fcost+$fcost*1/100,8,".",""); echo "<br> fsellcost ".$fsellprice . "<br>";
// av code end

//avgprice end sellprice ='$fsellprice',
if($ba != 1){ echo " open ba ";
  $fcostup = mysqli_query($connection,"update trade set price = '$fcost' , ba ='1' , psell = '1' , sellprice = '$fsellprice'  where id = '$idu'"); 
    
}else{  echo " price no need update"; }
if($sellcid != "0" && $sa != 1 && $fstatus == "FILLED"){ echo " open sa "; 
 $fcostup0 = mysqli_query($connection,"update trade set sell = '$fcost' , selldate = '$date' , sa = '1', type = '1' where sellcid = '$sellcid' AND id = '$said'");  
 }else{  echo " *** sell price no need update"; }
}}
?>
<?php 
echo "<br>Buy SIDE<br>";
$count0 = mysqli_fetch_assoc(mysqli_query($connection,"select count(type) as count from trade where pair = '$pair' AND type != '1'"));
$count = $count0['count']; echo " <br>count ".$count; 
$btcl = $quantity*$buy; $btclow = number_format($btcl,11);
echo "<br>btc low :",$btclow;

if($open4 > $close4 && $open3 > $close3 && $lastopen < $lastclose && $lastmax < $ask){ $buyon = "ON";}

//buy code
if($count < 3){ echo " new order "; echo " buyon " .$buyon; 
if($lastclose < $lastopen && $typ == "1"){
$hmasup = mysqli_query($connection,"update trade set hmas = '1' where pair ='$pair' AND type = '1' AND id = '$idu'");}
if($buyon == "ON"){ echo "<br>Up Buy Trand start";
if($marketbalance > $btclow){ echo "buy fund enough";
if($waitprice > $ask OR $hmas == 1){ echo "hmas 1 ok";
if($marketasset == $market && $coin == $coinasset){ echo "buy pair ok";   
// buy code start
$ch = curl_init();
$timestamp = round(microtime(true) * 1000);
$querystring = "symbol=$pair&side=BUY&type=LIMIT&timeInForce=GTC&quantity=$quantity&price=$ask&recvWindow=50000&timestamp=".$timestamp;
//$querystring = "symbol=$pair&side=BUY&type=MARKET&quantity=$quantity&recvWindow=50000&timestamp=".$timestamp;
$signature0 = hash_hmac('SHA256',$querystring ,$secret);
curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/order?symbol=$pair&side=BUY&type=LIMIT&timeInForce=GTC&quantity=$quantity&price=$ask&recvWindow=50000&timestamp=$timestamp&signature=$signature0");

//curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/order?symbol=$pair&side=BUY&type=MARKET&quantity=$quantity&recvWindow=50000&timestamp=$timestamp&signature=$signature0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$headers = array();
$headers[] = "X-Mbx-Apikey: $api_key";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
print_r($result);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);
//$result=json_decode($result);
//echo"<pre>";
//print_r($result);
//order buy end
$sita=json_decode($result, true);

$ids=$sita['orderId'];
$sideask=$sita['side'];
$priceask=$sita['price'];
$quantity123=$sita['origQty'];
echo "sita"; echo "$ids"; 
//insert order details

if($quantity123 > "0") { echo "buy quantity :", $quantity123 . " - ". $ids;
$query = mysqli_query($connection,"INSERT INTO trade(price,sellprice,quantity,date,type,lastbal,currency,pair,dlastbal,hmas,assetc,assetm,mrk,qu,clientOrderId,sellcid,ba,sa,sell,psell,stoploss) VALUES ('$ask','$sellprice','$quantity123','$date','0','$btclow','$coin','$pair','$marketbalance','0','$assetc','$assetm','$market','$preq','$ids','0','0','2','0','$stop1','$lastmin')"); 
    
//buy end 

}else {echo "<br>Someting wrong , order not store in database ";}
} else {echo "<br>buy pair not ok";}
} else {echo "<br>waiting for new buy trand";}
} else {echo "<br>Buy Balance low";}
} else {echo "<br>Buy trand off";}
} else { echo "last 3 order not done";}

?>  
//
<?php
//sell fibo
echo "<br> SELL FIBO CAL";
if($lastmax > $cumax){ $fibmax = $lastmax;} else { $fibmax = $cumax;} 
$topp = $fibmax;
echo "<br> top price " . $topp;
$botp = $lastprice; //echo "<br> bottom price " . $botp;
$tbp = number_format($topp-$botp,8); //echo "<br> tbp : ". $tbp;
$fb1 = number_format($tbp/100,8); //echo "<br> fb0  : ". $fb1;
$fb100 = number_format($botp+$fb1*100.0,8); //echo "<br> fb100 : ". $fb100;
$fb236 = number_format($topp-$fb1*23.6,8); echo "<br> fb23.6 : ". $fb236;
?>
<?PHP
$ls0 = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM trade where pair='$pair' AND type = '0' order by id desc limit 1"));
$idls0 = $ls0['id']; echo "<br> idls0 ". $idls0;
$pricels0 = $ls0["price"]; echo "<br> price ls0 ". $pricels0;
$quantityls0 = $ls0["quantity"]; echo "<br> quantity ls0 ". $quantityls0;
$tc0 = $pricels0*$quantityls0; echo "<br> tc0 ". $tc0;

$ls1 = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM trade where pair='$pair' AND type = '0' AND id < '$idls0' order by id desc limit 1"));
$idls1 = $ls1['id']; echo "<br> idls1 ". $idls1;
$pricels1 = $ls1["price"]; echo "<br> price ls1 ". $pricels1;
$quantityls1 = $ls1["quantity"]; echo "<br> quantity ls1 ". $quantityls1;
$tc1 = $pricels1*$quantityls1; echo "<br> tc1 ". $tc1;

$ls2 = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM trade where pair='$pair' AND type = '0' AND id < '$idls1' order by id desc limit 1"));
$idls2 = $ls2['id']; echo "<br> idls2 ". $idls2;
$pricels2 = $ls2["price"]; echo "<br> price ls2 ". $pricels2;
$quantityls2 = $ls2["quantity"]; echo "<br> quantity ls2 ". $quantityls2;
$tc2 = $pricels2*$quantityls2; echo "<br> tc2 ". $tc2;

$tc = $tc0+$tc1+$tc2; echo " tc ". $tc;
$tq = $quantityls0+$quantityls1+$quantityls2; echo " total quantity ". $tq;
$cusell = $ls0["sellprice"];
if($tq > 0){
 $avprice = $tc/$tq; echo " total avg price ". $avprice;}
 $nsell = $avprice+$avprice*1/100; echo "<br> new sell price ". $nsell;

if($cusell < $nsell){ $presell = $cusell; $prequantity = $quantityls0; } else { $presell = $nsell; $prequantity = $tq;}

echo "<br> presell " . $presell . " prequantity " . $prequantity;
?>
<?PHP
//sell trand
echo "<br>******<br> sell Trand <br>";
if($bid > $presell) {$sell = $bid;}
$symbol = "$pair";
$type = "limit";
$price1 = "$sell";
$quantitys= "$prequantity";
    echo " pair: " . $pair. "sell price " . $sell. "sell quantity " . $quantitys. "<br>";
$count1 = mysqli_fetch_assoc(mysqli_query($connection,"select count(type) as count from trade where pair = '$pair' AND type = '3'"));
$typ1 = $count1['count']; echo " <br>typ1 ".$typ1; 
//if($cuclose < $lastmin OR $cuclose < $min3){ $exiton = "ON";}
if($count > 0){ echo " sell new order "; 
if($typ1 == "0" && $sell123 == "0"){ echo "<br>Up sell Trand Start";
echo "sell :<br>";

if($price1 > "0.00000001"  && $fb236 > $prisell && $sell > $presell){ echo "sell price check<br>";
if($coinbalance >= $quantitys){ echo "sell fund enough";
if($marketasset == $market && $coin == $coinasset){ echo "sell pair ok"; 
$ch = curl_init();
$timestamp = round(microtime(true) * 1000);
$querystring = "symbol=$pair&side=SELL&type=LIMIT&timeInForce=GTC&quantity=$quantitys&price=$price1&recvWindow=50000&timestamp=".$timestamp;
$signature0 = hash_hmac('SHA256',$querystring ,$secret);
curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/order?symbol=$pair&side=SELL&type=LIMIT&timeInForce=GTC&quantity=$quantitys&price=$price1&recvWindow=50000&timestamp=$timestamp&signature=$signature0");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
$headers = array();
$headers[] = "X-Mbx-Apikey: $api_key";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
$result = curl_exec($ch);
print_r($result);
if (curl_errno($ch)) {
    echo 'Error:' . curl_error($ch);
}
curl_close ($ch);
//$result=json_decode($result);
//echo"<pre>";
//print_r($result);

//order end
   $kali=json_decode($result, true);

$prices=$kali['price'];
$quantitys1=$kali['origQty'];
//$bid124=$kali['clientOrderId'];
$bid124=$kali['orderId'];
 echo " sell on";
//insert order details
if($quantitys1 > 0){ 
echo "sell order", $bid124;
if($cusell < $nsell){
    $querysellup = mysqli_query($connection,"update trade set type ='3' , sell ='$prices', selldate='$date', sellcid = '$bid124' where pair ='$pair' AND type = '0' AND id = '$idls0'");}
    else { $querysellup = mysqli_query($connection,"update trade set type ='3' , sell ='$prices', selldate='$date', sellcid = '$bid124' where pair ='$pair' AND type != '1'");}
}
//sell end
} else {echo "<br>sell pair not ok";}
} else {echo "<br>sell balance low";}
} else {echo "<br>wrong price";}
} else {echo "<br>sell data not found";}
} else {echo "<br>typ1=3";}
} else { echo "<h1>Trading status disable set by you for this pair</h1>";}
?>

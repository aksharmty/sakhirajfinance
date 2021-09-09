<meta http-equiv="refresh" content="10000; url=#" />
<body style="background-color:#000000; color:white;">
<?php 
//include "connect.php";
define('TIMEZONE', 'Asia/kolkata');
date_default_timezone_set(TIMEZONE);
  $date = DATE("Y-m-d H:i:s"); 
///
$sqlpair = "SELECT * FROM trade where type !='1' order by id desc limit 1";
$resultpair = $connection->query($sqlpair);
if ($resultpair->num_rows > 0) {
 while($r0=$resultpair->fetch_assoc()) 
 { $typo = $r0['type']; $coin = $r0['currency']; $market = $r0['mrk']; $assetc = $r0['assetc']; $assetm = $r0['assetm']; $preq = $r0['qu']; echo "type $typo found " .$coin . $market ."<br>" . $assetc . $assetm . $preq;}
} else {     
$coinsql = "SELECT * FROM coin where ch = '1' AND id = 6";
$coinsqlresult = $connection->query($coinsql);
if ($coinsqlresult->num_rows > 0) {
  // output data of each row
  while($row0 = $coinsqlresult->fetch_assoc()) {
    $coin = $row0["coin"]; $preq = $row0["quantity"]; $market = $row0["market"]; $coin1ch = $row0["ch"]; $assetc = $row0['assetc']; $assetm = $row0['assetm']; $preq = $row0['quantity']; $typo = "NA"; $preq = $row0['qu'];
    echo "id: " . $row0["id"]. " - Coin1: " . $row0["coin"]. " " . $row0["ch"]. "<br>";
 //   $coinsqlup0 = mysqli_query($connection,"update coin set ch ='0' where coin ='$coin'");
}
} else {  echo "0 results"; $coinsqlup = mysqli_query($connection,"update coin set ch ='1' where ch ='0'"); } 
}
///  
//$coin = "ETC";
//$market = "USDT";
$pair = $coin.$market;
echo "pair : ",$pair; 
echo "<br>preq : ",$preq; 
//$coinsql = mysqli_fetch_array(mysqli_query($connection,"SELECT * FROM coin where coin = '$coin'"));
//$assetc = $coinsql['assetc'];
//$assetm = $coinsql['assetm'];
//$preq = $coinsql['quantity'];
//$preq = $coinsql['qu'];

$sql = mysqli_fetch_array(mysqli_query($connection,"select * from trade where currency ='$coin' order by id desc limit 1")); 
$sqls = mysqli_fetch_array(mysqli_query($connection,"select * from trade where currency ='$coin' AND sa != '1' AND type = '3' order by id asc limit 1")); 

$orderbookurl = 'https://api3.binance.com/api/v3/depth?symbol=$pair';
//$orderurl = 'https://api3.binance.com/api/v3/order';
$tickerurl = "https://api3.binance.com/api/v3/ticker/price?symbol=$pair";
$urlcan = "https://api3.binance.com/api/v3/klines?symbol=$pair&interval=1h&limit=20";

$coinstatus0 = mysqli_fetch_assoc(mysqli_query($connection,"select * from coin where coin = '$coin'"));
$coinstatus = $coinstatus0["status"]; echo "status " . $coinstatus;
if($coinstatus == "enable"){
$sql123 = mysqli_query($connection,"TRUNCATE TABLE candle");
$curlcan = curl_init();
curl_setopt($curlcan, CURLOPT_URL, $urlcan);
curl_setopt($curlcan, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curlcan, CURLOPT_HEADER, false);
$datacan = curl_exec($curlcan);
curl_close($curlcan);
//print_r($datacan);
$arraycan=json_decode($datacan, true);
 foreach($arraycan as $row) //Extract the Array Values by using Foreach Loop
          { 

      $query = mysqli_query($connection,"INSERT INTO candle(open,close,min,max,timestamp,volume) VALUES ('".$row["1"]."','".$row["4"]."','".$row["3"]."','".$row["2"]."','".$row["6"]."','1'); "); 
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
$urltick = ("https://api3.binance.com/api/v3/exchangeInfo?symbol=$pair");
$curltick = curl_init();
curl_setopt($curltick, CURLOPT_URL, $urltick);
curl_setopt($curltick, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curltick, CURLOPT_HEADER, false);
$datatick = curl_exec($curltick);
curl_close($curltick);
//print_r($datatick);
$lotsize0=json_decode($datatick, true);
$lotsize=$lotsize0['LOT_SIZE']['stepSize']; echo " stepSize " . $stepSize;
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
$mprice=$ass['price'];
echo "<br>Market Price : ", $mprice ."<br>";

?>
<br>---------------------------------------------------<br>
<?php
$last4 = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM candle where id ='17'"));
echo "<br> last 4th bar ";
$id4 = $last4['id']; echo "<br> id4 ". $id4;
$close4 = $last4['close'];     echo "<br> close4 ". $close4;
$open4 = $last4['open'];     echo "<br> open4 ". $open4;

$last3 = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM candle where id ='18'"));
echo "<br> last 3rd bar ";
$id3 = $last3['id']; echo "<br> id3 ". $id3;
$close3 = $last3['close'];     echo "<br> close3 ". $close3;
$open3 = $last3['open'];     echo "<br> open3 ". $open3;
$lastcan = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM candle where id ='19'"));
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
$cucan = mysqli_fetch_assoc(mysqli_query($connection,"SELECT * FROM candle where id='20'"));
if($cucan['open'] < $cucan['close']){ $colorcu="green";}else{$colorcu="red";}
echo "<font color ='$colorcu'>";
$cuid = $cucan['id']; echo "<br>cu id ". $cuid;
$cuopen = $cucan['open']; echo "<br>cu open ". $cuopen;
$cuclose = $cucan['close']; echo "<br>cu close ". $cuclose;
$pivet = number_format(($lastclose+$lastmin+$lastmax+$cuopen)/4,11,".", "");
//echo "<br> pivet ". $pivet;
echo "</font>";
?>
<?php
//vol and quantity
// risk management
echo "<br> fund for this trade use ".$market ." = ".$mp;
$qq = $mp/$mprice/1; //echo "qq " .$qq;
$quantity = floor($qq/$preq)*$preq;
echo "<br>Buy Qq ", $quantity; 

?>
<?php
echo "<br>Buy price : ". $mprice;
$buy = $mprice;
$sell0 = $buy+$buy*1/100;
$sellprice = number_format($sell0,8, ".", ""); echo "<br>sellprice :",$sellprice;
$btcl = $quantity*$buy; $btclow = number_format($btcl,11);
echo "<br>$market fund required :",$btclow;
?>
<br>---------------------------<br>
<?php
$idu = $sql['id']; echo "<br>idu :" , $idu;
$typ = $sql['type']; echo "<br>typ :" , $typ;
$sell123 = $sql['sell']; echo "<br>sell123 :" , $sell123;
$psell = $sql['psell']; echo "<br>psell ". $psell;
$dlastbal = $sql['dlastbal']; echo "<br>dlastbal :" , $dlastbal;
$lastbal = $sql['lastbal']; echo "<br>lastbal :" , $lastbal;
$lastprice = $sql['price']; echo "<br>lastprice :" , $lastprice;
$levelprice = $sql['stoploss']; echo "<br>levelprice :" , $levelprice;
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
if($psell == "1" && $sellprice0 < $mprice){ $psellup = mysqli_query($connection,"update trade set psell = '2' , sellprice ='$mprice' where id = '$idu'"); }else{  echo " psell ready";  }
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

//avgprice end 
if($ba != 1){ echo " open ba ";
  $fcostup = mysqli_query($connection,"update trade set price = '$fcost' , sellprice ='$fsellprice', ba ='1' , psell = '1'  where id = '$idu'"); 
    
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
if($lastclose < $lastopen){
$hmasup = mysqli_query($connection,"update trade set hmas = '1' where pair ='$pair' AND type = '1'");}
if($open4 > $close4 && $open3 > $close3 && $lastopen < $lastclose && $lastmax < $mprice){ $buyon = "ON";}

//buy code
if($count < 3){ echo " new order "; echo " buyon " .$buyon; 

if($buyon == "ON"){ echo "<br>Up Buy Trand start";
if($marketbalance > $btclow){ echo "buy fund enough";
if($waitprice > $mprice OR $hmas == 1){ echo "type 1 ok";
if($marketasset == $market && $coin == $coinasset){ echo "buy pair ok";   
// buy code start
$ch = curl_init();
$timestamp = round(microtime(true) * 1000);
$querystring = "symbol=$pair&side=BUY&type=LIMIT&timeInForce=GTC&quantity=$quantity&price=$mprice&recvWindow=50000&timestamp=".$timestamp;
//$querystring = "symbol=$pair&side=BUY&type=MARKET&quantity=$quantity&recvWindow=50000&timestamp=".$timestamp;
$signature0 = hash_hmac('SHA256',$querystring ,$secret);
curl_setopt($ch, CURLOPT_URL, "https://api.binance.com/api/v3/order?symbol=$pair&side=BUY&type=LIMIT&timeInForce=GTC&quantity=$quantity&price=$mprice&recvWindow=50000&timestamp=$timestamp&signature=$signature0");

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
$query = mysqli_query($connection,"INSERT INTO trade(price,sellprice,quantity,date,type,lastbal,currency,pair,dlastbal,hmas,assetc,assetm,mrk,qu,clientOrderId,sellcid,ba,sa,sell,psell,stoploss) VALUES ('$mprice','$sellprice','$quantity123','$date','0','$coinbalance','$coin','$pair','$marketbalance','0','$assetc','$assetm','$market','$preq','$ids','0','0','2','0','$stop1','$lastmin')"); 
    
//buy end 

}else {echo "<br>Someting wrong , order not store in database ";}
} else {echo "<br>buy pair not ok";}
} else {echo "<br>waiting for new buy trand";}
} else {echo "<br>Buy Balance low";}
} else {echo "<br>Buy trand off";}
} else { echo "last 3 order not done";}

?>  
//
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
if($mprice > $presell){$sell = $mprice;}
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

if($price1 > "0.00000001" && $cuclose >= $price1){ echo "sell price check<br>";
if($coinbalance >= $quantitys && $psell == 2){ echo "sell fund enough";
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
    $querysellup = mysqli_query($connection,"update trade set type ='3' , sell ='$prices', selldate='$date', sellcid = '$bid124' where pair ='$pair' AND type = '0' AND id = '$idu'");}
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

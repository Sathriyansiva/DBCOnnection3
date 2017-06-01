<?php
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
$app = new Application();
$app['pdo'] = function ($app) {
    $pdo = new PDO(
        $app['mysql.dsn'],
        $app['mysql.user'],
        $app['mysql.password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    return $pdo;
};
$app->get('/', function (Application $app, Request $request) {
    $ip = $request->GetClientIp();
    $octets = explode($separator = ':', $ip);
    if (count($octets) < 2) {  // Must be ip4 address
        $octets = explode($separator = '.', $ip);
    }
    if (count($octets) < 2) {
        $octets = ['bad', 'ip'];  // IP address will be recorded as bad.ip.
    }
    // Replace empty chunks with zeros.
    $octets = array_map(function ($x) {
        return $x == '' ? '0' : $x;
    }, $octets);
    $user_ip = $octets[0] . $separator . $octets[1];
    // Insert a visit into the database.
    /** @var PDO $pdo */
    $pdo = $app['pdo'];
   if(isset($_GET['id']))
{
$id=$_GET['id'];
}

$format = strtolower($_GET['format']) == 'json'; //xml is the default
    // Look up the last 10 visits
	
	
    $select = $pdo->prepare(
        'SELECT DATE_FORMAT(fromdate,'%H:%i') as time,prod_name,DATE_FORMAT(fromdate, '%Y-%m-%d') 
	as fromdate,DATE_FORMAT(todate, '%Y-%m-%d') as todate,points FROM do_product_hdr where prod_cate=:id1');
    $select->execute(array(':id1'=>$id));
    $visits = [""];
    while ($row = $select->fetch(PDO::FETCH_ASSOC)) {
    $prod_name= $rows['prod_name'];
		$time= $rows['time'];
		$fromdate1= $rows['fromdate'];
		$todate1= $rows['todate'];
		$points= $rows['points'];
		$fromdate= strtotime($rows['fromdate']);
		$todate= strtotime($rows['todate']);
		
$timeDiff = abs($todate - $fromdate);

$numberDays = $timeDiff/86400;  // 86400 seconds in one day

// and you might want to convert to integer
$numberDays = intval($numberDays);
 $posts[] = array('prod_name' => $prod_name,'fromdate' =>$fromdate1, 'todate' =>$todate1,
		  'interval'=>$numberDays,'time'=>$time,'points'=>$points);	
    }
	if($format == 'json') {
    header('Content-type: application/json');
    echo json_encode(array('posts'=>$posts));
  }
  else {
    header('Content-type: text/xml');
    echo '';
    foreach($posts as $index => $post) {
      if(is_array($post)) {
        foreach($post as $key => $value) {
          echo '<',$key,'>';
          if(is_array($value)) {
            foreach($value as $tag => $val) {
              echo '<',$tag,'>',htmlentities($val),'</',$tag,'>';
            }
          }
          echo '</',$key,'>';
        }
      }
    }
    echo '';
  }
	return new Response(implode("\n", $visits), 200,
        ['Content-Type' => 'text/plain']);
});
# [END example]
return $app;

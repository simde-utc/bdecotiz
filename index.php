<?php
require 'vendor/autoload.php';
use \Ginger\Client\GingerClient;
use \Payutc\Client\AutoJsonClient;
use \Payutc\Client\JsonException;

// Load configuration
require "config.inc.php";
require "cas.php";

// Settings for cookies
$sessionPath = parse_url($_CONFIG["self_url"], PHP_URL_PATH);
session_set_cookie_params(0, $sessionPath);
session_start();

$app = new \Slim\Slim();
$payutcClient = new AutoJsonClient($_CONFIG["payutc_server"], "WEBSALE");
$gingerClient = new GingerClient($_CONFIG["ginger_apikey"], $_CONFIG["ginger_server"]);

$app->get('/', function() use($app, $gingerClient, $_CONFIG) {
    if(isset($_SESSION['username'])) {
        $userInfo = $gingerClient->getUser($_SESSION["username"]);
        $app->render('template.php', array(
            "title" => $_CONFIG["title"],
            "loggedin" => true,
            "logoutUrl" => $_CONFIG["self_url"]."logout",
            "userInfo" => $userInfo,
            "cotiseUrl" => $_CONFIG["self_url"]."cotiser"
        ));
    } else {
        $app->render('template.php', array(
            "title" => $_CONFIG["title"],
            "loggedin" => false,
            "loginUrl" => $_CONFIG["self_url"]."logincas"
        ));
    }
});

$app->get('/logincas', function() use($app, $payutcClient, $_CONFIG) {
    if(empty($_GET["ticket"])) {
        $casUrl = $payutcClient->getCasUrl()."login?service=".$_CONFIG["self_url"].'logincas';
        $app->response->redirect($casUrl, 303);
    } else {
        $cas = new Cas($payutcClient->getCasUrl());
        $user = $cas->authenticate($_GET["ticket"], $_CONFIG["self_url"].'logincas');
        $_SESSION['username'] = $user;
        $app->response->redirect($_CONFIG["self_url"], 303);
    }
});

$app->get('/logout', function() use($app, $_CONFIG, $payutcClient) {
    session_destroy();
    $casUrl = $payutcClient->getCasUrl()."logout?url=".$_CONFIG["self_url"];
    $app->response->redirect($casUrl, 303);
});

$app->get('/cotiser', function() use($app, $gingerClient, $payutcClient, $_CONFIG) {
	$userInfo = $gingerClient->getUser($_SESSION["username"]);
	if(!$userInfo->is_cotisant) {    
        $payutcClient->loginApp(array("key" => $_CONFIG["payutc_apikey"]));
        $vente = $payutcClient->createTransaction(array(
            "items" => json_encode(array(array($_CONFIG["payutc_artid"], 1))),
            "fun_id" => $_CONFIG["payutc_funid"],
            "mail" => $userInfo->mail,
            "return_url" => $_CONFIG["self_url"],
            "callback_url" => $_CONFIG["self_url"]."callback?username=".$_SESSION["username"]
        ));
        $app->response->redirect($vente->url, 303);
    } else {
        $app->response->redirect($_CONFIG["self_url"], 303);
    }
});

$app->get('/callback', function() use($gingerClient, $payutcClient, $_CONFIG) {
    if($_GET["tra_id"] && $_GET["username"]) {
        $tra_id = $_GET["tra_id"];
        $username = $_GET["username"];
        $userInfo = $gingerClient->getUser($username);
        $payutcClient->loginApp(array("key" => $_CONFIG["payutc_apikey"]));
        $transaction = $payutcClient->getTransactionInfo(array("fun_id" => $_CONFIG["payutc_funid"], "tra_id" => $tra_id));
        
        $transactionDate = new DateTime($transaction->created);
        $now = new DateTime("now");
        $interval = date_diff($transactionDate, $now);
        if($transaction->status == 'V' && 
            !$userInfo->is_cotisant && 
            $userInfo->mail == $transaction->email &&
            $interval->days < 10) 
        {
            $gingerClient->addCotisation(
                $username, 
                date("Y-m-d"), 
                date("Y-m-d", mktime(0,0,0,8,31,date("m") > 8 ? date("Y") + 1: date("Y"))), 
                $transaction->purchases[0]->pur_price/100);
        }
    }
});

$app->run();

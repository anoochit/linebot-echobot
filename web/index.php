<?php

require_once '../vendor/autoload.php';

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\FirePHPHandler;

$logger = new Logger('linebot');
$logger->pushHandler(new StreamHandler('php://stderr', Logger::DEBUG));

$httpClient = new \LINE\LINEBot\HTTPClient\CurlHTTPClient('rHESonstdV3FniND+25WHjgb+mTxTOeikt7zCGV9h0LgBFzCprU8IQwrXpgreO2TGyewjB87Imwmyz/RbxqF2N/i7mA+gMnmaS2cTF5GKLozTwE25XEHvRiaCtL74zveIKzb/UVfbaU4poxxB3WcrQdB04t89/1O/w1cDnyilFU=');
$bot = new \LINE\LINEBot($httpClient, ['channelSecret' => 'e8726b9bfd344a12c189f4fc6512da8a']);

$signature = $_SERVER['HTTP_' . \LINE\LINEBot\Constant\HTTPHeader::LINE_SIGNATURE];
try {
  $events = $bot->parseEventRequest(file_get_contents('php://input'), $signature);
} catch(\LINE\LINEBot\Exception\InvalidSignatureException $e) {
  error_log('parseEventRequest failed. InvalidSignatureException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownEventTypeException $e) {
  error_log('parseEventRequest failed. UnknownEventTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\UnknownMessageTypeException $e) {
  error_log('parseEventRequest failed. UnknownMessageTypeException => '.var_export($e, true));
} catch(\LINE\LINEBot\Exception\InvalidEventRequestException $e) {
  error_log('parseEventRequest failed. InvalidEventRequestException => '.var_export($e, true));
}

foreach ($events as $event) {
  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent)) {
    error_log('Non message event has come');
    continue;
  }
  if (!($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
    error_log('Non text message has come');
    continue;
  }

  if (($event instanceof \LINE\LINEBot\Event\MessageEvent\TextMessage)) {
		$messageText=strtolower(trim($event->getText()));
		switch ($messageText) {
			case "text" : 
				$outputText = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("text message");
				break;
			case "location" :
				$outputText = new \LINE\LINEBot\MessageBuilder\LocationMessageBuilder("Eiffel Tower", "Champ de Mars, 5 Avenue Anatole France, 75007 Paris, France", 48.858328, 2.294750);
				break;
			case "button" :
				$actions = array (
   					// general message action
   					New \LINE\LINEBot\TemplateActionBuilder\MessageTemplateActionBuilder("button 1", "text 1"),
   					// URL type action
   					New \LINE\LINEBot\TemplateActionBuilder\UriTemplateActionBuilder("Google", "http://www.google.com"),
   					// The following two are interactive actions
   					New \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("next page", "page=3"),
   					New \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("Previous", "page=1")
 				);
				$img_url = "https://cdn.shopify.com/s/files/1/0379/7669/products/sampleset2_1024x1024.JPG?v=1458740363";
 				$button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ButtonTemplateBuilder("button text", "description", $img_url, $actions);
 				$outputText = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("this message to use the phone to look to the Oh", $button);
				break;
			case "confirm" :
				$actions = array (
   					New \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("yes", "ans=y"),
   					New \LINE\LINEBot\TemplateActionBuilder\PostbackTemplateActionBuilder("no", "ans=N")
 				);
 				$button = new \LINE\LINEBot\MessageBuilder\TemplateBuilder\ConfirmTemplateBuilder("problem", $actions);
 				$outputText = new \LINE\LINEBot\MessageBuilder\TemplateMessageBuilder("this message to use the phone to look to the Oh", $button);
				 break;
			default :
				$outputText = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder("demo command: text, location, button, confirm to test message template");	
				break;
 
		}
		$textMessageBuilder = new \LINE\LINEBot\MessageBuilder\TextMessageBuilder('hello');
		$response = $bot->replyMessage($event->getReplyToken(), $outputText);
  }

  


}  
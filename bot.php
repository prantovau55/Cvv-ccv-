<?php

ini_set('log_errors', 1);
ini_set('error_log', 'php-error.log');

ignore_user_abort(true);
set_time_limit(0);

require_once 'config.php';
require_once 'telegram.php';

$bot = new TelegramBot(8074203200:AAHfNdvnn_EuBw2JWyuEPQfBhFu-sZMJbC4, BOT_LOGS, BOT_GROUP);

$bot->dbInfo(DB_HOST, DB_DATABASE, DB_USERNAME, DB_PASSWORD);

$update = isset($argv[1]) && !empty($argv[1]) ? base64_decode($argv[1]) : file_get_contents('php://input');

$bot->setData($update);

require 'classes/CurlX.php';
require 'classes/Response.php';
require 'classes/Tools.php';
require 'classes/Generator.php';

$curlx = new CurlX;
$response = new Response;
$tools = new Tools;
$generator = new GenCard;

$bot->setChkAPI($curlx, $response, $tools);

if (isset($bot->getData()->callback_query)) {
	require 'inline.php';

	exit();
}

if (isset($bot->getData()->message)) {
	$msg = $bot->getData()->message;

	$mess_id = $msg->message_id ?? '';
	$user_id = $msg->from->id ?? '';
	$first_n = $msg->from->first_name ?? '';
	$usern_n = $msg->from->username ?? '';
	$user_lg = $msg->from->language_code ?? '';
	$chat_id = $msg->chat->id ?? '';
	$chat_tt = $msg->chat->title ?? '';
	$chat_nm = $msg->chat->first_name ?? '';
	$chat_un = $msg->chat->username ?? '';
	$chat_tp = $msg->chat->type ?? '';
	$message = $msg->text ?? $msg->caption ?? '';
	$document = $msg->document ?? false;

	if (isset($msg->reply_to_message)) {
		$reply = $msg->reply_to_message;
		$reply_txt = $reply->text ?? $reply->caption ?? '';
		$reply_id = $reply->from->id ?? '';

		if (!$document) $document = $reply->document ?? false;
	}
} else {
	exit();
}

if ($document) {
	$file = $bot->getFile(array(
		'file_id' => $document->file_id
	));

	if (isset($file->file_path)) {
		$file_path = 'https://api.telegram.org/file/bot'.8074203200:AAHfNdvnn_EuBw2JWyuEPQfBhFu-sZMJbC4.'/'.$file->file_path;

		$file_data = $curlx->Get($file_path)->body;
	}
}

$person = empty($usern_n) ? "<a href='tg://openmessage?user_id={$7669522778}'>{$user_id}</a>" : "@$usern_n";

if (!$cmd = $bot->cmd($message)) exit;

if (substr($cmd->data, 0, 1) == '@' && strpos(strtolower($cmd->data), '@'.strtolower(Raven23cc_bot)) === false) exit;

if ($cmd->cmd == 'register') {
	$bot->register();

	exit;
}

$user_info = $bot->fetchUser($user_id);
$chat_info = $bot->fetchUser($chat_id);

$mass_limit = intval((10 / 100) * intval($user_info['credits'] ?? ''));

if ($mass_limit < 5) $mass_limit = 5;
if ($mass_limit > 20) $mass_limit = 20;

switch (strtolower($user_info['plan'] ?? '')) {
	case 'vip': $antispam = 20; break;
	case 'premium': $antispam = 20; break;
	default: $antispam = 40;
}

function checkUser($skip = False, $spam = True) {
	global $bot, $user_id, $chat_id, $chat_tp, $user_info, $chat_info;

	$inline_keyboard = [
		[
			[
				"text" => "Owner",
				"url" => "https://t.me/".prantovau9
			],
		]
	];

	if (!$user_info) {
		$bot->sendMsg("Please register first to use me. Use the /register command.");

		exit;
	}

	if (strtolower($user_info['status']) == 'banned') {
		$inline_keyboard = [[["text" => ꧁༒☬𝐏𝐕 𝐂𝐇𝐄𝐂𝐊𝐀𝐑☬༒꧂, "url" => "tg://user?id=".7669522778]]];

		$bot->sendMsg("You've Got Banned! Kindly Contact The Owner to Unban You!", $inline_keyboard);

		exit;
	}

	if ((!$chat_info || strtolower($chat_info['status']) == 'pending') && strtolower($user_info['status']) != 'active') {
		$bot->sendMsg("You Are Free User!!</b>\n\n", $inline_keyboard);

		exit;
	}

	if ($spam == True) {
		$anti_spam = $bot->fetchSpam($user_id);

		if ($anti_spam->status) {
			$bot->sendMsg("Try again after $anti_spam seconds.");

			exit;
		}
	}

	if ($user_info['credits'] < 1 && strtolower($user_info['plan']) == 'free' && $chat_tp == 'private' && !$skip) {
		$inline_keyboard = [[["text" => BOT_OWNER_NAME, "url" => "tg://user?id=".BOT_OWNER_ID]]];

		$bot->sendMsg("This Chat isn't Authorized to Use Me.", $inline_keyboard);

		exit;
	}

	if (strtolower($user_info['plan']) != 'free' && intval($user_info['expiry']) < time() && !$skip) {
		$sql = $bot->dbConn()->prepare("UPDATE `users` SET `plan` = :plan, `expiry` = :expiry WHERE `id` = :id");

		$sql->execute(array('plan' => 'free', 'expiry' => 0, 'id' => $user_id));

		$bot->sendMsg("Your Plan has Expired.", $inline_keyboard);

		exit;
	}
}

if ($cmd->cmd == 'claim') {
	checkUser(True);

	if (empty($cmd->data)) {
		$bot->sendMsg("Enter a Key!");

		exit;
	}

	$bot->claimKey($cmd->data);

	exit;
}

if ($cmd->cmd == 'xlaim') {
	checkUser(True);

	if (empty($cmd->data)) {
		$bot->sendMsg("Enter a Key!");

		exit;
	}

	$bot->claimKey($cmd->data, $chat_id);

	exit;
}

if ($cmd->cmd == 'start') {
	$bot->sendMsg("<b>Hi,</b>
<b>Welcome To Bot</b>
<b>Type /cmds For Commands</b>\n\n". $inline_keyboard);

	exit;
}

if ($cmd->cmd == 'cmds') {

		$bot->sendMsg("<b>Bot: Running ✅🌧️</b>\n
<b>• Welcome to my command panel,</b>
<b>here you can see my Gateways and Tools.</b>
<b>• Press the buttons to see my commands.</b>\n\n", [[['text' => 'Menu 🔎', 'callback_data' => 'gates']]]);
	exit;
}

if ($cmd->cmd == 'key') {
	checkUser(false, true);

	$bot->createKey($cmd->data);

	exit;
}

if ($cmd->cmd == 'auth') {
	checkUser(false, true);

	$bot->authUser($cmd->data ?? ($chat_id != $user_id ? $chat_id : null));

	exit;
}

if ($cmd->cmd == 'unauth') {
	checkUser(false, true);

	$bot->unAuthUser($cmd->data ?? ($chat_id != $user_id ? $chat_id : null));

	exit;
}

if ($cmd->cmd == 'prom') {
	checkUser(false, true);

	$bot->promUser($cmd->data ?? (isset($reply_id) ? $reply_id : ''));

	exit;
}

if ($cmd->cmd == 'ban') {
	checkUser(false, true);

	$bot->banUser($cmd->data ?? (isset($reply_id) ? $reply_id : ''));

	exit;
}

if ($cmd->cmd == 'bban') {
	checkUser(false, true);

	$bot->banBin($cmd->data ?? (isset($reply_text) ? $reply_text : ''));

	exit;
}

if ($cmd->cmd == 'agate') {
	checkUser(false, true);

	$bot->addGate($cmd->data);

	exit;
}

if ($cmd->cmd == 'ugate') {
	checkUser(false, true);

	$bot->updateGate($cmd->data);

	exit;
}

if ($cmd->cmd == 'gates') {
	checkUser(false, true);

	$gates = $bot->fetchGates();

	if (!$gates) {
		$bot->sendMsg("Not Available at The Moment!");

		exit;
	}
	
	$menus = [
		'auth' => 0,
		'ccn' => 0,
		'charge' => 0,
		'mass' => 0
	];

	$types = [/;

	$online = 0;

	$offline = 0;

	foreach ($gates as $gate) {
		$menus[$gate['menu']]++;

		$types[$gate['type']]++;

		if ($gate['status'] == '✅') $online++;
		else $offline++;
	}
	

	$gates_menu = "<b>Bot: Running ✅🌧️</b>\n
<b>• Welcome to my command panel,</b>
<b>here you can see my Gateways and Tools.</b>
<b>• Press the buttons to see my commands.</b>\n\n";
	$inline_keyboard = [[]];

	$inline_keyboard[] = [
		[
			'text' => '['.$menus['auth'].'] Auth',
			'callback_data' => 'menu auth'
		],
	];

	$inline_keyboard[] = [
		[
			'text' => '[Charge',
			'callback_data' => 'menu charge'
		],
		[
			'text' => '['.$menus['mass'].'] Mass',
			'callback_data' => 'menu mass'
		]
	];

	$inline_keyboard[] = [
		[
			'text' => ' Home', 'callback_data' => 'cmds'
		],
	];
  $inline_keyboard[] = [
  [
    "text" => 'OWNER', "url" => "tg://user?id=".7669522778.
  ]
    ];

	$bot->sendMsg($gates_menu, $inline_keyboard);

	exit;
}

if ($cmd->cmd == 'mass' || $cmd->cmd == 'gmass') {
	checkUser();

	$gate = $bot->fetchGate($cmd->cmd);

	$gate_info = "● <b>Info</b> - <i>".$gate['info']."</i>\n" .
		"● <b>Format</b> - <code>/".$gate['cmd']." ".(empty($gate['format']) ? "cc|month|year|cvv" : $gate['format'])."</code>\n" .
		"● <b>Created On</b> - <i>".$gate['creation']."</i>\n\n";

	if ($gate['status'] == '❌') {
		$bot->sendMsg($gate_info);

		exit();
	}

	$credits = intval($user_info['credits']);

	if ($credits < 1) {
		$bot->sendMsg("You don't Have Enough Balance to Use This Gate! Kindly Contact The Owner to Top Up Your Balance.</i>", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit();
	}

	if (!$cmd2 = $bot->cmd("/".$cmd->data)) {
		$bot->sendMsg($gate_info);

		exit;
	}

	$gate2 = $bot->fetchGate($cmd2->cmd);

	if (!$gate2) {
		$bot->sendMsg("Enter Vaild Gate Name to Use It for Mass!");

		exit;
	}

	$gate_info2 = "● <b>Info</b> - <i>".$gate['info']."</i>\n" .
		"● <b>Format</b> - <code>/".$gate['cmd']." ".(empty($gate['format']) ? "cc|month|year|cvv" : $gate['format'])."</code>\n" .
		"● <b>Created On</b> - <i>".$gate['creation']."</i>\n\n";
	if ($gate2['status'] == '❌') {
		$bot->sendMsg($gate_info2);

		exit;
	}

	if (strtolower($gate2['type']) == 'vip' && strtolower($user_info['plan']) != 'vip') {
		$bot->sendMsg("This Gate is for VIP Users Only! Kindly Contact The Owner to Purchase VIP Access.</i>", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit;
	}

	if (strtolower($gate2['type']) == 'premium' && strtolower($user_info['plan']) == 'free') {
		$bot->sendMsg("This Gate is for Premium And VIP Users Only! Kindly Contact The Owner to Purchase Premium Or VIP Access.</i>", [[["text" => "Owner", "url" => "tg://user?id=".@prantovau]]]);

		exit;
	}

	$data = $cmd2->data ?? (isset($reply_txt) ? $reply_txt : '');

	if ($cmd->cmd == 'gmass') {
		if (empty($data)) {
			$bot->sendMsg($gate_info);

			exit;
		}

		preg_match_all('/[\dx]+/', strtolower($data), $matches);

		if (!isset($matches[0], $matches[0][0]) || !is_numeric(substr($matches[0][0], 0, 6))) {
			$bot->sendMsg("Enter Vaild Bin or Extra!");

			exit;
		}

		$extra = $matches[0][0];

		$len = substr_compare($extra, 37, 0, 2) ? 16 : 15;

		if (is_numeric($extra) && strlen($extra) == $len) {
			$bot->sendMsg("Same Card Number, Try Another Bin or Extra!");

			exit;
		}

		$mm = $matches[0][1] ?? 'rnd';
		$yy = $matches[0][2] ?? 'rnd';
		$cvv = $matches[0][3] ?? 'rnd';

		$amo = $matches[0][4] ?? $mass_limit;

		if ($amo < 1 || $amo > $mass_limit) $amo = $mass_limit;

		$format = "<b>Command</b> - $extra|$mm|$yy|$cvv\n\n";

		$gen_cards = $generator->Gen($extra, $mm, $yy, $cvv, $amo);

		$data = '';

		foreach ($gen_cards as $gen_card) {
			$data .= "$gen_card\n";
		}
	}

	$listas = $bot->getCards($data);

	if (!$listas) {
		$bot->sendMsg($gate_info);

		exit;
	}

	$total = sizeof($listas);

	$total_checked = $total > $mass_limit ? $mass_limit : $total;

	$live_count = 0;
	$dead_count = 0;
	$untested_count = 0;

	$live_text = "";
	$dead_text = "";
	$untested_text = "";

	$extra = empty($gate2['extra']) ? '' : json_decode(''.$gate2['extra'].'');

	$checked = 0;

	$inline_keyboard = [[]];

	for ($i = $mass_limit; $i < sizeof($listas); $i++) {
		$untested_count++;

		$untested_text .= "<b>Card</b> ↯ <code>".$listas[$i][0]."|".$listas[$i][1]."|".$listas[$i][2]."|".$listas[$i][3]."</code>\n";
	}

	$untested_msg = "".strtoupper($gate['cmd'])."</i>\n" .
		"<b>━━━━━━━━━/b>\n" .
		"<b>Gate</b> ↯ <i>".$gate2['name']."</i> [<code>".$gate2['cmd']."</code>]\n" .
		"<b>Gateway</b> ↯ <i>".$gate2['info']."</i>\n" .
		"<b>━━━━━━━━━/b>\n" .
		(isset($format) ? $format : "") .
		"<b>Not Checked</b> ↯ <code>$untested_count</code>\n" .
		"<b>━━━━━━━━━/b>\n" .
		"$untested_text\n" .
		"<b>━━━━━━━━━/b>\n" .
		"<b>Checked By</b> ↯ $person [<code>".strtoupper($user_info['plan'])."</code>]\n" .
		"<b>Owner</b> ↯ <i>".BOT_OWNER_NAME."</i>\n";

	$untested_msg = preg_replace('/\n\n\n/', "\n\n", $untested_msg);

	if ($untested_count > 0) {
		$inline_keyboard[0][0] = [ "text" => "Remove Message", "callback_data" => "remove" ];

		$bot->sendMsg($untested_msg, $inline_keyboard);
	}

	foreach ($listas as $lista) {
		if (++$checked > $mass_limit) {
			$checked--;

			break;
		}

		$msg = "".ucfirst($gate['cmd'])." in Progress!</i>\n" .
			"<b>━━━━━━━━━/b>\n" .
			"<b>Gate</b> ↯ <i>".$gate2['name']."</i> [<code>".$gate2['cmd']."</code>]\n" .
			"<b>Gateway</b> ↯ <i>".$gate2['info']."</i>\n" .
			"<b>━━━━━━━━━/b>\n" .
			(isset($format) ? $format : "") .
			"<b>Checked</b> ↯ [<code>".($checked - 1)."</code>/<code>$total_checked</code>]\n" .
			"<b>━━━━━━━━━/b>\n" .
			"<b>Lives</b> ↯ <code>$live_count</code>\n" .
			"$live_text\n" .
			"<b>━━━━━━━━━/b>\n" .
			"<b>Deads</b> ↯ <code>$dead_count</code>\n" .
			"$dead_text\n" .
			"<b>━━━━━━━━━</b>\n" .
			"<b>Checked By</b> ↯ $person [<code>".strtoupper($user_info['plan'])."</code>]\n" .
			"<b>Author</b> ↯ <i>".BOT_OWNER_NAME."</i>\n";

		$msg = preg_replace('/\n\n\n/', "\n\n", $msg);

		if (!isset($msg_id)) {
			$msg_id = $bot->sendMsg($msg)->message_id ?? exit;
		} else {
			$bot->editMsg($msg, $msg_id);
		}

		$start = microtime(true);

		if (!$bot->luhnCheck($lista[0])) {
			$dead_count++;

			$dead_text .= "<b>Card</b> ↯ <code>".$lista[0]."|".$lista[1]."|".$lista[2]."|".$lista[3]."</code>\n" .
				"<b>Message</b> ↯ [ <i>This Card Number Faild Luhn Algorithm!</i> ]\n\n";

			continue;
		}

		if ($credits < 1) {
			$dead_count++;

			$dead_text .= "<b>Card</b> ↯ <code>".$lista[0]."|".$lista[1]."|".$lista[2]."|".$lista[3]."</code>\n" .
				"<b>Message</b> ↯ [ <i>You don't Have Enough Balance to Use This Gate!</i> ]\n\n";

			continue;
		}

		if ($bot->isBannedBin($lista[0])) {
			$dead_count++;

			$dead_text .= "<b>Card</b> ↯ <code>".$lista[0]."|".$lista[1]."|".$lista[2]."|".$lista[3]."</code>\n" .
				"<b>Message</b> ↯ [ <i>Sorry But This Bin is Banned!</i> ]\n\n";

			continue;
		}

		$result = $bot->chkAPI($gate2['file'], $lista, $extra);

		$resp = "<b>Card</b> ↯ <code>".$lista[0]."|".$lista[1]."|".$lista[2]."|".$lista[3]."</code>\n" .
			"<b>Message</b> ↯ [ <i>".$result->msg."</i> ]\n" .
			"<b>Taken</b> ↯ <code>".$bot->taken($start)."'s</code> | <b>Retry</b> ↯ <code>".$result->retry."</code>\n" .
			"[".$result->proxy['emoji']."] <b>Proxy</b> ↯ [ <i>".$result->proxy['status']."</i> ]\n";

		if ($result->emoji == '✅') {
			$credits--;

			$bot->cutCredit($user_id);

			$live_count++;

			$live_text .= "{$resp}<b>Credits</b> ↯ <code>".$credits."</code>\n\n";
		} else {
			$dead_count++;

			$dead_text .= "{$resp}\n";
		}
	}

	$msg = "".ucfirst($gate['cmd'])." Finished!</i>\n" .
		"<b>━━━━━━━━━/b>\n" .
		"<b>Gate</b> ↯ <i>".$gate2['name']."</i> [<code>".$gate2['cmd']."</code>]\n" .
		"<b>Gateway</b> ↯ <i>".$gate2['info']."</i>\n" .
		"<b>━━━━━━━━━/b>\n" .
		(isset($format) ? $format : "") .
		"<b>Checked</b> ↯ [<code>$checked</code>/<code>$total_checked</code>]\n" .
		"<b>━━━━━━━━━/b>\n" .
		"<b>Lives</b> ↯ <code>$live_count</code>\n" .
		"$live_text\n" .
		"<b>━━━━━━━━━/b>\n" .
		"<b>Deads</b> ↯ <code>$dead_count</code>\n" .
		"$dead_text\n" .
		"<b>━━━━━━━━━/b>\n" .
		"<b>Checked By</b> ↯ $person [<code>".strtoupper($user_info['plan'])."</code>]\n" .
		"<b>Author</b> ↯ <i>".BOT_OWNER_NAME."</i>\n";

	$msg = preg_replace('/\n\n\n/', "\n\n", $msg);

	if ($dead_count > 0) $inline_keyboard[0][0] = [ "text" => "[$dead_count/$checked] Delete Dead", "callback_data" => "cut_dead" ];

	$bot->editMsg($msg, $msg_id, $inline_keyboard);

	exit;
}

$gate = $bot->fetchGate($cmd->cmd);

if ($gate) {
	checkUser();

	$gate_info = "[".$gate['status']."] <b>".$gate['name']."</b> → [<i>".strtoupper($gate['type'])."</i>]\n" .
		"<b>Info</b> - <i>".$gate['info']."</i>\n" .
		"<b>Command</b> - <code>".(empty($gate['format']) ? "/".$gate['cmd']." cc|month|year|cvv" : $gate['format'])."</i>\n\n";

	if ($gate['status'] == '❌') {
		$bot->sendMsg($gate_info);

		exit();
	}

	if (strtolower($gate['type']) == 'credits' && $user_info['credits'] < 1) {
		$bot->sendMsg("You don't Have Enough Balance to Use This Gate! Kindly Contact The Owner to Top Up Your Balance.", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit();
	}

	if (strtolower($gate['type']) == 'vip' && strtolower($user_info['plan']) != 'vip' && strtolower($chat_info['plan']) != 'vip') {
		$bot->sendMsg("his Gate is for VIP Users Only! Kindly Contact The Owner to Purchase VIP Access.", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit();
	}

	if (strtolower($gate['type']) == 'premium' && strtolower($user_info['plan']) == 'free' && strtolower($chat_info['plan']) == 'free') {
		$bot->sendMsg("This Gate is for Premium Users Only!", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit();
	}

	if (empty($gate['file']) || !is_file('gates/'.$gate['file'].'.php')) {
		$bot->sendMsg("This Gate isn't Available at The Moment, Please Try Again Later!");

		exit();
	}

	$lista = $bot->getCards($cmd->data ?? (isset($reply_txt) ? $reply_txt : ''))[0] ?? null;

	if (!$lista) {
		$bot->sendMsg($gate_info);

		exit();
	}

	if (!$bot->luhnCheck($lista[0])) {
		$bot->sendMsg("This Card Number Faild Luhn Algorithm!\nKindly Put Vaild Card Number Next Time.");

		exit();
	}

	if ($bot->isBannedBin($lista[0])) {
		$bot->sendMsg("Sorry But This Bin is Banned!");

		exit();
	}

	$bin = $bot->binlookUp($lista[0]);

	$msg = "Processing....";

	$msg_id = $bot->sendMsg($msg)->message_id ?? die();

	$start = microtime(true);
	$extra = empty($gate['extra']) ? '' : json_decode(''.$gate['extra'].'');

	$result = $bot->chkAPI($gate['file'], $lista, $extra);
  
  $per = ["😊", "😍", "🥳", "🎉", "👍", "🌟", "🔥", "❤️", "😎"];
  $sw = $per[array_rand($per)];
  $ghonta =["🕐", "🕒", "🕔", "🕘", "🕚", "🕜", "🕜", "🕞", "🕠"];
  
  $somoy = $ghonta[array_rand($ghonta)];
  $dor = ["🐳", "😈", "🤪", "🤧", "🤡", "🤓", "🦖", "🐬", "🦥"];
  $sd = $dor[array_rand($dor)];

  $mi = ["📩", "🎃", "🧨", "🎀", "🧧", "🎁", "🎟️", "📄", "📑", "📃", "🎫", "💎", "🃏", "🗿", "📧", "✉️", "📨"];
  $mp = $mi[array_rand($mi)];
  
	$msg = "<b> ".$result->status."</b>" ."<b>".$result->emoji."\n</b>\n" .
		"<b>• 𝗖𝗮𝗿𝗱</b> : ".$lista[0]."|".$lista[1]."|".$lista[2]."|".$lista[3]."\n" .
		"<b>• 𝐆𝐚𝐭𝐞𝐰𝐚𝐲</b> : <b>".$gate['name']."</b>\n" .
		"<b>• 𝐑𝐞𝐬𝐩𝐨𝐧𝐬𝐞</b> : [<b>".$result->msg."</b>]\n\n" .
		"<b>• 𝗜𝗻𝗳𝗼:</b> : <b>".$bin->scheme."</b> - <b>".$bin->type."</b> - <b>".$bin->brand."</b>\n" .
		"<b>• 𝐈𝐬𝐬𝐮𝐞𝐫</b> : <b>".$bin->bank."</b>\n" .
		"<b>• 𝐂𝐨𝐮𝐧𝐭𝐫𝐲</b> : <b>".$bin->country."</b>".$bin->emoji."\n" .
    "\n".
		"<b>• 𝗧𝗶𝗺𝗲</b> : <b>".$bot->taken($start)." 𝐬𝐞𝐜𝐨𝐧𝐝𝐬</b>\n\n";

	$bot->editMsg($msg, $msg_id);

	exit();
}

if ($cmd->cmd == 'atool') {
	checkUser(false, true);

	$bot->addTool($cmd->data);

	exit;
}

if ($cmd->cmd == 'utool') {
	checkUser(false, true);

	$bot->updateTool($cmd->data);

	exit;
}

if ($cmd->cmd == 'tools') {
	checkUser(false, true);

	$tools = $bot->fetchTools();

	if (!$tools) {
		$bot->sendMsg("Not Available at The Movement!</i>");

		exit;
	}

	$page = empty(intval($cmd->data)) ? 1 : $cmd->data;

	$page = intval($page) - 1;

	if ($page < 1 || !isset($tools[$page * 5])) $page = 0;

	$tools_menu = "Tools";

	$start = $page * 5;

	$last = $start + 5;

	for ($i = $start; $i < $last; $i++) {
		if (isset($tools[$i])) $tools_menu .= "[".$tools[$i]['status']."] <b>".$tools[$i]['name']."</b> → [<i>".strtoupper($tools[$i]['type'])."</i>] → (<code>".($i + 1)."</code>)\n" .
			"<b>Info</b> - <i>".$tools[$i]['info']."</i>\n" .
			"<b>Command</b> - <code>/".$tools[$i]['cmd'].(empty($tools[$i]['format']) ? "" : " ".$tools[$i]['format'])."</i>\n\n";
	}

	$inline_keyboard = [[]];

	if ($page > 0) $inline_keyboard[0][] = ['text' => 'Previous', 'callback_data' => "tools".($page - 1).""];
	if (isset($tools[$last++])) $inline_keyboard[0][] = ['text' => 'Next', 'callback_data' => "tools".($page + 1).""];

	$inline_keyboard[] = [
		[
			'text' => 'Home', 'callback_data' => 'cmds'
		],
	];

	$bot->sendMsg($tools_menu, $inline_keyboard);

	exit;
}

$tool = $bot->fetchTool($cmd->cmd);

if ($tool) {
	checkUser();

	$tool_info = "[".$tool['status']."] <b>".$tool['name']."</b> → [<i>".strtoupper($tool['type'])."</i>]\n" .
		"[☇] <b>Info</b> → <i>".$tool['info']."</i>\n" .
		"[☇] <b>Format</b> → <code>/".$tool['cmd'].(empty($tool['format']) ? "" : " ".$tool['format'])."</code>\n" .
		"[☇] <b>Comment</b> → <i>".$tool['comm']."</i>\n" .
		"[☇] <b>Created At</b> → <i>".$tool['creation']."</i>\n\n";

	if ($tool['status'] == '❌') {
		$bot->sendMsg($tool_info);

		exit();
	}

	if (strtolower($tool['type']) == 'credits' && $user_info['credits'] < 1) {
		$bot->sendMsg("You don't Have Enough Balance to Use This Tool! Kindly Contact The Owner to Top Up Your Balance.", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit();
	}

	if (strtolower($tool['type']) == 'vip' && strtolower($user_info['plan']) != 'vip' && strtolower($chat_info['plan']) != 'vip') {
		$bot->sendMsg("This Tool is for VIP Users Only! Kindly Contact The Owner to Purchase VIP Access.", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit();
	}

	if (strtolower($tool['type']) == 'premium' && strtolower($user_info['plan']) == 'free' && strtolower($chat_info['plan']) == 'free') {
		$bot->sendMsg("This Tool is for Premium And VIP Users Only! Kindly Contact The Owner to Purchase Premium Or VIP Access.</i>", [[["text" => "Owner", "url" => "tg://user?id=".BOT_OWNER_ID]]]);

		exit();
	}

	if (empty($tool['file']) || !is_file('tools/'.$tool['file'].'.php')) {
		$bot->sendMsg("❌ <b>Error</b> → <i>This Tool isn't Available at The Moment, Please Try Again Later</i>!");

		exit();
	}

	$start = microtime(true);

	require 'tools/'.$tool['file'].'.php';

	$bot->sendMsg("[❃] <b>".BOT_NAME."</b> → <i>Tools</i> → <i>".$tool['name']."</i>\n\n" .
		"{$msg}\n\n" .
		"[🛠] <b>Tool</b> ↯ <i>".$tool['info']."</i> [<code>".$tool['cmd']."</code>]\n" .
		"[⌛] <b>Taken</b> ↯ <code>".$bot->taken($start)."'s</code>\n" .
		"[👤] <b>Checked By</b> ↯ $person [<code>".strtoupper($user_info['plan'])."</code>]\n" .
		"[🤴] <b>Owner</b> ↯ <i>".BOT_OWNER_NAME."</i>\n");

	exit();
}

if ($cmd->cmd == 'asho') {
	checkUser(false, true);

	if (strtolower($user_info['range']) != 'owner') exit;

	if (!$cmd->data) {
		$bot->sendMsg("Kindly Input Gate Info!");

		exit;
	}

	$lines = explode("\n", $cmd->data);

	foreach ($lines as $line) {
		$split = explode('|', $line);

		if (sizeof($split) < 5) {
			$bot->sendMsg("Not Enough Parameters!");

			continue;
		}

		$bot->addShopify($split[0], $split[1], $split[2], $split[3], $split[4]);
	}

	exit;
}

if ($cmd->cmd == 'asp') {
	checkUser(false, true);

	if (strtolower($user_info['range']) != 'owner') exit;

	if (!$cmd->data) {
		$bot->sendMsg("Kindly Input Gate Info!");

		exit;
	}

	$split = explode('|', $cmd->data);

	if (sizeof($split) < 4) {
		$bot->sendMsg("Not Enough Parameters!");

		exit;
	}

	$bot->addASP($split[0], $split[1], $split[2], $split[3]);
}

?>
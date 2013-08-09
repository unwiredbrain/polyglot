<?php

if (!($loader = require_once __DIR__ . '/../vendor/autoload.php')) {
    die('You need to install the project dependencies using Composer.');
}

use Polyglot\Speaker;

$path = realpath(__DIR__ . '/locale');

$polyglot = Speaker::getInstance();
$polyglot->all('fr_FR')->setPath($path)->register('datetimes', 'formats')->register('messages');

$messages = $polyglot->getContext('messages');
$datetimes = $polyglot->getContext('datetimes');

$format = $polyglot->using('formats')->translate('{month_as_number} - {month_as_text}');

foreach (new DatePeriod(new DateTime('@1357020000', new DateTimeZone('UTC')), new DateInterval('P1M'), 11) as $period) {
    echo $polyglot->interpolate($format, array(
        'month_as_number' => $period->format('m'),
        'month_as_text' => $datetimes->translate($period->format('F'))
    )) . PHP_EOL;
}

echo $messages->translate('You\'re currently signed in as {username}', array('username' => 'unwiredbrain')) . PHP_EOL;
echo $messages->translate('Sign out') . PHP_EOL;
echo $messages->translate('You have {count} new message.', array('count' => '8')) . PHP_EOL;
echo $messages->translate('You have {count} new message.', array('count' => '1')) . PHP_EOL;

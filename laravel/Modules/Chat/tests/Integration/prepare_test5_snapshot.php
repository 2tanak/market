<?php

require __DIR__ . '/../../../../vendor/autoload.php';

$app = require_once __DIR__ . '/../../../../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;

$snapshot = [
    'messages_count' => DB::table('messages')->count(),
    'max_message_id' => (int) DB::table('messages')->max('id'),
    'chats_count' => DB::table('chats')->count(),
    'max_chat_id' => (int) DB::table('chats')->max('id'),
    'chats_updated_at' => DB::table('chats')->pluck('updated_at', 'id')->all(),
    'jobs_count' => DB::table('jobs')->count(),
    'max_job_id' => (int) DB::table('jobs')->max('id'),
];

$snapshotKey = $argv[1] ?? 'test5:db_snapshot';
Redis::setex($snapshotKey, 120, json_encode($snapshot));

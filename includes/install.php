<?php

require_once 'config.php';

use Illuminate\Database\Capsule\Manager as Capsule;

Capsule::schema()->create('activity', function ($table) {
    $table->increments('id');
    $table->enum('type', ['rep', 'post', 'topic', 'join', 'follow_thread', 'follow_user', 'profile_photo_change']);
    $table->string('path')->nullable();
    $table->string('title')->nullable();
    $table->longText('content')->nullable();
    $table->string('author')->nullable();
    $table->string('author_path')->nullable();
    $table->string('author_photo')->nullable();
    $table->string('target')->nullable();
    $table->string('target_path')->nullable();
    $table->string('category')->nullable();
    $table->string('category_path')->nullable();
    $table->timestamp('created_at');
});

die('Installed!');

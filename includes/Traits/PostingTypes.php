<?php

namespace Feedr\Traits;

use Illuminate\Database\Capsule\Manager as Capsule;

trait PostingTypes
{
    private function insert($data)
    {
        $author = $data['author'];
        $data['content'] = isset($data['content']) ? preg_replace('/\n\n\n/', "\n", $data['content']) : '';
        $data['content'] = preg_replace('/\n/', '<br/>', $data['content']);
        $data['created_at'] = date('Y-m-d H:i:s');

        return Capsule::table('activity')->updateOrInsert(compact('author', 'created_at'), $data);
    }

    private function insertPostMessage($raw)
    {
        $type = 'post';
        preg_match("#/forum/(profile/[^/]+/)\" data-ipsHover data-ipsHover-target=\".*?/forum/profile/.*?\" class=\"ipsUserPhoto ipsUserPhoto_mini\" title=\"Go to (.*?)'s profile\">.*?<img src='.*?/forum/(.*?)' alt=''>.*?<a href='.*?/forum/(topic/.*?)' data-linkType=\"link\" data-searchable>([^<]+)</a>.*?in\s*<a href='.*?/forum/(forum/.*?)'>(.*?)</a>.*?<div data-ipsTruncate data-ipsTruncate-type='remove' data-ipsTruncate-size='\S+ lines'>\s*(.*?)\s*</div>.*?<time datetime=.(.*?)Z#si", $raw, $parsed);

        list(, $author_path, $author, $author_photo, $path, $title, $category_path, $category, $content, $created_at) = $parsed;
        $this->insert(compact('type', 'author', 'author_path', 'author_photo', 'path', 'title', 'category_path', 'category', 'content', 'created_at'));
    }

    private function insertTopicMessage($raw)
    {
        $type = 'topic';
        preg_match("#/forum/(profile/[^/]+/)\" data-ipsHover data-ipsHover-target=\".*?/forum/profile/.*?\" class=\"ipsUserPhoto ipsUserPhoto_mini\" title=\"Go to (.*?)'s profile\">.*?<img src='.*?/forum/(.*?)' alt=''>.*?<a href='.*?/forum/(topic/.*?)' data-linkType=\"link\" data-searchable>([^<]+)</a>.*?in\s*<a href='.*?/forum/(forum/.*?)'>(.*?)</a>.*?<div data-ipsTruncate data-ipsTruncate-type='remove' data-ipsTruncate-size='\S+ lines'>\s*(.*?)\s*</div>.*?<time datetime=.(.*?)Z#si", $raw, $parsed);

        list(, $author_path, $author, $author_photo, $path, $title, $category_path, $category, $content, $created_at) = $parsed;
        $this->insert(compact('type', 'author', 'author_path', 'author_photo', 'path', 'title', 'category_path', 'category', 'content', 'created_at'));
    }

    private function insertFollowMessage($raw, $type)
    {
        $type = 'follow_' . $type;
        preg_match("#<a href=.*?/forum/(profile/.*?)['\"].*?>(.*?)</a>.*?<a href=.*?/forum/((?:topic|profile)/.*?)['\"].*?>(.*?)</a>.*?<time datetime=.(.*?)Z#si", $raw, $parsed);

        if ($type == 'follow_thread') {
            list(, $author_path, $author, $title, $path, $created_at) = $parsed;
            $this->insert(compact('type', 'author_path', 'author', 'title', 'path', 'created_at'));
        } else {
            list(, $author_path, $author, $target_path, $target, $created_at) = $parsed;
            $this->insert(compact('type', 'author_path', 'author', 'target_path', 'target', 'created_at'));
        }
    }

    private function insertJoinMessage($raw)
    {
        $type = 'join';
        preg_match("#<a href='.*?/forum/(profile/.*?)' data-ipsHover.*?>(.*?)</a>.*?<time datetime=.(.*?)Z#si", $raw, $parsed);

        list(, $author_path, $author, $created_at) = $parsed;
        $this->insert(compact('type', 'author_path', 'author', 'created_at'));
    }

    private function insertRepMessage($raw)
    {
        $type = 'rep';
        preg_match("#<a href=\".*?/forum/(profile/.*?)\".*?>(.*?)</a>.*?gave ([a-z]+) reputation.*?<a href=./(topic/.*?).*?>(.*?)</a>.*?<time datetime=.(.*?)Z#si", $raw, $parsed);

        list(, $author_path, $author, $title, $target_path, $target, $created_at) = $parsed;
        $this->insert(compact('type', 'author_path', 'author', 'title', 'target_path', 'target', 'created_at'));
    }

    private function insertProfilePhotoChangeMessage($raw)
    {
        $type = 'profile_photo_change';
        preg_match("#<img src='.*?/forum/(uploads/.*?)'.*?<a href=.*?/forum/(profile/.*?)['\"].*?>(.*?)</a>.*?<time datetime=.(.*?)Z#si", $raw, $parsed);

        list(, $author_photo, $author_path, $author, $created_at) = $parsed;
        $this->insert(compact('type', 'author_photo', 'author_path', 'author', 'created_at'));
    }
}
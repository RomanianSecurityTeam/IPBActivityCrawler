<?php

namespace Feedr;

use GuzzleHttp\Client;
use GuzzleHttp\Cookie\FileCookieJar;
use Illuminate\Database\Capsule\Manager as Capsule;

class API
{
    use Traits\PostingTypes;

    const ACTIVITY_URL = 'https://rstforums.com/forum/discover/';
    const AUTH_URL = 'https://rstforums.com/forum/login/';

    private $client;

    public function __construct()
    {
        $this->login();
    }

    public function parseActivity()
    {
        $html = $this->body(static::ACTIVITY_URL);

        preg_match_all("#<li class='ipsStreamItem .*?>(.*?)</li>#si", $html, $results);

        $results = array_reverse($results[1]);

        foreach ($results as $activity) {
            switch (true) {
                case preg_match("#</a> started following.*?forum/profile#", $activity):
                    $this->insertFollowMessage($activity, 'user');
                    break;
                case preg_match("#</a> started following.*?forum/topic#", $activity):
                    $this->insertFollowMessage($activity, 'thread');
                    break;
                case preg_match("#title='Post'><i class='fa fa-comment'>#", $activity):
                    $this->insertPostMessage($activity);
                    break;
                case preg_match("#title='Topic'><i class='fa fa-comments'>#", $activity):
                    $this->insertTopicMessage($activity);
                    break;
                case preg_match("#</a> joined the community#", $activity):
                    $this->insertJoinMessage($activity);
                    break;
                case preg_match("#reputation to a post in#", $activity):
                    $this->insertRepMessage($activity);
                    break;
                case preg_match("#changed their profile photo#", $activity):
                    $this->insertProfilePhotoChangeMessage($activity);
                    break;
            }
        }

        $this->cleanOldData();
    }

    private function cleanOldData()
    {
        $lastTwentyIds = array_map(function ($item) {
            return $item->id;
        }, Capsule::table('activity')->orderBy('id', 'desc')->take(50)->get(['id'])->toArray());

        Capsule::table('activity')->whereNotIn('id', $lastTwentyIds)->delete();
    }

    private function body($url, $config = null)
    {
        return $this->client->get($url, $config)->getBody()->getContents();
    }

    public function getData($lastId)
    {
        die(Capsule::table('activity')
            ->where('id', '>', (int) $lastId)
            ->orderBy('id', 'asc')
            ->get()
            ->each(function ($item) {
                $item->date = date('Y-m-d H:i:s', strtotime($item->created_at));
            })
            ->toJSON());
    }

    private function login()
    {
        $user = env('USER');
        $pass = env('PASS');

        $this->client = new Client(['cookies' => true]);

        $html = $this->body(static::AUTH_URL);
        $csrfKey = match('#name="csrfKey" value="(.*?)"#i', $html);
        $this->client->post(static::AUTH_URL, [
            'headers'     => ['Referer' => static::AUTH_URL],
            'form_params' => [
                'login__standard_submitted' => 1,
                'csrfKey'                   => $csrfKey,
                'auth'                      => $user,
                'password'                  => $pass,
                'remember_me'               => 0,
                'remember_me_checkbox'      => 1,
                'signin_anonymous'          => 0,
            ],
        ]);
    }
}
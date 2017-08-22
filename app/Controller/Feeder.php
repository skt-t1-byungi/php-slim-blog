<?php

namespace App\Controller;

use App\Model\Post;
use Illuminate\Database\Eloquent\Collection;
use Suin\RSSWriter\Channel;
use Suin\RSSWriter\Feed;
use Suin\RSSWriter\Item;

class Feeder
{
    public function __invoke()
    {
        $feed = new Feed();

        /** @var Collection $posts */
        $posts = Post::latest()->take(10)->get();

        /** @var Post $latestPost */
        $latestPost = $posts->first();

        $channel = (new Channel())
            ->title(getenv('ADMIN_PROVIDER_NAME') . ' 블로그')
            ->description(getenv('BLOG_DESCRIPTION'))
            ->url(getenv('HTTP_BASE_URL'))
            ->feedUrl(url_for('rss'))
            ->language('ko-KR')
            ->pubDate($latestPost->created_at->timestamp)
            ->appendTo($feed);

        $posts->each(function ($post) use ($channel) {
            /** @var Post $post */
            $item = new Item();

            $item
                ->title($post->title)
                ->description($post->parsed_content)
                ->url($post->link(true))
                ->author(getenv('ADMIN_PROVIDER_NAME'))
                ->pubDate($post->created_at->timestamp)
                ->guid($post->link(true), true)
                ->appendTo($channel);
        });

        return response()->write($feed)->withHeader('Content-Type', 'application/xml');
    }
}
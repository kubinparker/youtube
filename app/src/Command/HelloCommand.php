<?php

declare(strict_types=1);

namespace App\Command;

use Cake\Command\Command;
use Cake\Console\Arguments;
use Cake\Console\ConsoleIo;

use App\Controller\AppController;
use Google\Service\YouTube;

class HelloCommand extends Command
{
    public function execute(Arguments $args, ConsoleIo $io)
    {

        $app = new AppController();
        $app->google_service();

        $service = new YouTube($app->_google);

        $is_pagenext = true;
        $next_pagetoken = '';

        while ($is_pagenext) {

            $param = [
                'q' => '栃木県旅行',
                'maxResults' => 50,
                'regionCode' => 'JP',
            ];

            if (!is_null($next_pagetoken) && $next_pagetoken != '') {
                $param['pageToken'] = $next_pagetoken;
            }
            $io->out($param);
            $searchResponse = $service->search->listSearch('id,snippet', $param);

            foreach ($searchResponse['items'] as $searchResult) {
                if (!$searchResult->id->videoId) continue;

                try {
                    $snippet = $searchResult->snippet;
                    $channel_details = $service->channels->listChannels("statistics, snippet", ['id' => $snippet->channelId]);

                    if (!$channel_details->items || !isset($channel_details->items[0])) continue;

                    $video = [
                        'code' => $searchResult->id->videoId,
                        'title' => $snippet->title,
                        'thumbnail_default' => $snippet->thumbnails->default->url,
                        'thumbnail_medium' => $snippet->thumbnails->medium->url,
                        'thumbnail_high' => $snippet->thumbnails->high->url,
                        'published_at' => $snippet->publishedAt,
                        'view_counts' => intval($service->videos->listVideos("statistics", ['id' => $searchResult->id->videoId])->items[0]->statistics->viewCount),
                        'channel_code' => $snippet->channelId
                    ];

                    $channel_detail = $channel_details->items[0];

                    $channel = [
                        'code' => $snippet->channelId,
                        'channel_name' => $snippet->channelTitle,
                        'count_register' => intval($channel_detail->statistics->subscriberCount),
                        'thumbnail_default' => $channel_detail->snippet->thumbnails->default->url,
                        'thumbnail_medium' => $channel_detail->snippet->thumbnails->medium->url,
                        'thumbnail_high' => $channel_detail->snippet->thumbnails->high->url,
                    ];

                    $this->fetchTable('Videos')->_save($video);
                    $this->fetchTable('Channels')->_save($channel);
                } catch (\Exception $e) {
                    print_r($e->getMessage());
                }
            }
            $is_pagenext = !is_null($searchResponse->nextPageToken);
            $next_pagetoken = $searchResponse->nextPageToken;
        }
    }
}

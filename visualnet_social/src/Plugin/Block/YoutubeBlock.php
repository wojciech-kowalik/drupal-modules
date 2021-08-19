<?php

namespace Drupal\visualnet_social\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Block(
 *   id = "youtube_block",
 *   admin_label = @Translation("Youtube block")
 * )
 */
class YoutubeBlock extends BlockBase
{
    /**
     * @var string
     */
    private $apiUrl = 'https://www.googleapis.com/youtube/v3/playlistItems';

    /**
     * {@inheritdoc}
     * @example PLbUXbQ2ag-5C5Bm0aNGB9Ikr4e7outw0v,
     *          AIzaSyBMVgkNiY7Ifkz-ywbnndmACiQNfudDflo
     */
    public function build()
    {
        $entities = null;
        $others   = [];
        $config   = \Drupal::config('visualnet_social.settings');

        $params = array(
            'part'       => 'snippet',
            'playlistId' => $config->get('visualnet_social_youtube_playlist_id'),
            'key'        => $config->get('visualnet_social_youtube_key'),
            'maxResults' => $config->get('visualnet_social_youtube_max_results'),
        );

        $this->apiUrl .= '?' . http_build_query($params);

        $client = new GuzzleClient();

        try {

            $response = $client->get($this->apiUrl);
            $entities = json_decode($response->getBody());

        } catch (RequestException $e) {

            if ($e->getCode() == Response::HTTP_NOT_FOUND) {
                drupal_set_message('Wrong service url', 'error');
            }

            if ($e->getCode() == Response::HTTP_FORBIDDEN) {
                drupal_set_message('Wrong Youtube configuration', 'error');
            }

        }

        if (is_null($entities)) {
            drupal_set_message('There is a problem with connecting service', 'error');
            return;
        }

        $first  = $entities->items[0];
        $others = array_slice($entities->items, 1, sizeof($entities->items) - 1);

        return [
            '#theme'    => 'youtube',
            '#first'    => $first,
            '#entities' => $others,
            '#hash'     => $config->get('visualnet_social_youtube_playlist_id'),
            '#attached' => [
                'library' => [
                    'visualnet_social/visualnet_social.assets',
                ],
            ],
        ];

    }

}

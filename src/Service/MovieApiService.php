<?php
namespace App\Service;

use App\Model\MovieModel;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class MovieApiService
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @return MovieModel[]
     */
    public function callMoviesApi(string $method, string $url, string $bearer_token) {
        $response = $this->client->request(
            $method,
            $url,
            ['auth_bearer' => $bearer_token]
        );

        $content = $response->toArray();
        $data = [];

        foreach ($content['results'] as $result) {
            $model = new MovieModel($result['title']);
            array_push($data, $model);
        }

        return $data;
    }
}
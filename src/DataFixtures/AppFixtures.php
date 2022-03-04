<?php

namespace App\DataFixtures;

use App\Entity\Post;
use App\Service\MovieApiService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Config\FrameworkConfig;

class AppFixtures extends Fixture
{
    private MovieApiService $movieApiService;

    public function __construct(MovieApiService $movieApiService)
    {
        $this->movieApiService = $movieApiService;
    }

    public function load(ObjectManager $manager): void
    {
        $data = $this->movieApiService->callMoviesApi(
            'GET',
            'https://api.themoviedb.org/3/discover/movie?language=fr-FR&sort_by=popularity.desc&page=1&primary_release_date.gte=2021-01-01&primary_release_date.lte=2021-12-31',
            'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1ZWNhYzcwNjhlYTFhZTNmYjI1ZGUzZWVkYjU3ZTQzNCIsInN1YiI6IjYyMjFiZjVmNDJiZjAxMDA2ZjliZmRmMSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.itqUem9B7PfHAMIywuGIgBudrz6n_cMp55TsovQWbIs'
        );

        foreach ($data as $model) {
            $post = new Post();
            $post->setTitle($model->getTitle());
            $manager->persist($post);
        }

        $manager->flush();
    }
}

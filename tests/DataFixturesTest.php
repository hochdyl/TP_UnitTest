<?php

namespace App\Tests;

use App\DataFixtures\AppFixtures;
use App\Service\MovieApiService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class DataFixturesTest extends KernelTestCase
{
    protected function setUp(): void {
        parent::setUp();
        exec("php bin/console doctrine:database:drop --env=test --force");
        exec("php bin/console doctrine:database:create --env=test");
        exec("php bin/console doctrine:migration:migrate -n --env=test");
    }

    public function testFixtures()
    {
        $appFixtures = self::getContainer()->get(AppFixtures::class);
        $em = self::getContainer()->get(EntityManagerInterface::class);
        $movieApiService = self::getContainer()->get(MovieApiService::class);
        $data = $movieApiService->callMoviesApi(
            'GET',
            'https://api.themoviedb.org/3/discover/movie?language=fr-FR&sort_by=popularity.desc&page=1&primary_release_date.gte=2021-01-01&primary_release_date.lte=2021-12-31',
            'eyJhbGciOiJIUzI1NiJ9.eyJhdWQiOiI1ZWNhYzcwNjhlYTFhZTNmYjI1ZGUzZWVkYjU3ZTQzNCIsInN1YiI6IjYyMjFiZjVmNDJiZjAxMDA2ZjliZmRmMSIsInNjb3BlcyI6WyJhcGlfcmVhZCJdLCJ2ZXJzaW9uIjoxfQ.itqUem9B7PfHAMIywuGIgBudrz6n_cMp55TsovQWbIs'
        );
        $appFixtures->load($em);

        $this->assertCount(20, $data);
    }
}

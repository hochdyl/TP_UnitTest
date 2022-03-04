<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiTest extends WebTestCase
{
    public function testIndexRoute(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/');

        $response = $client->getResponse();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "The resource you requested could not be found."], $responseData);
    }

    public function testGetPosts(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/post');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertIsArray($responseData);
        $this->assertCount(20, $responseData);
    }

    public function testGetPost(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/post/1');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['id' => 1, 'title' => 'Spider-Man: No Way Home', 'comments' => []], $responseData);
    }

    public function testPostNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/post/21');

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Post not found."], $responseData);
    }

    public function testAddPost(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/post', ['title' => 'Testing']);

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['id' => 21, 'title' => 'Testing', 'comments' => []]);
    }

    public function testAddPostIncorrectData(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/post', ['testing' => 'Testing']);

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData,
            [
                "type" => "https://symfony.com/errors/validation",
                "title" => "Validation Failed",
                "detail" => "title: Title cannot be empty !",
                "violations" => [
                    [
                        "propertyPath" => "title",
                        "title" => "Title cannot be empty !",
                        "parameters" => [
                            "{{ value }}" => "null"
                        ],
                        "type" => "urn:uuid:c1051bb4-d103-4f74-8988-acbcafc7fdc3"
                    ]
                ]
            ]
        );
    }

    public function testUpdatePost(): void
    {
        $client = static::createClient();
        $client->jsonRequest('PUT', '/api/post/21', ['title' => 'Update testing']);

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['id' => 21, 'title' => 'Update testing', 'comments' => []]);
    }

    public function testUpdatePostNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('PUT', '/api/post/22', ['title' => 'Update testing']);

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['message' => 'Post not found.']);
    }

    public function testUpdatePostIncorrectFormat(): void
    {
        $client = static::createClient();
        $client->jsonRequest('PUT', '/api/post/21');

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['message' => 'Request content is empty or wrongly formatted.']);
    }

    public function testAddComment(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/comment/21', ['content' => 'Testing comment']);

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, [
            'id' => 21,
            'title' => 'Update testing',
            'comments' => [
                0 => [
                    'id' => 1,
                    'content' => 'Testing comment'
                ]
            ]
        ]);
    }

    public function testAddCommentPostNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/comment/22', ['content' => 'Testing comment']);

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Post not found."], $responseData);
    }

    public function testAddCommentIncorrectData(): void
    {
        $client = static::createClient();
        $client->jsonRequest('POST', '/api/comment/21', ['testing' => 'Testing']);

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData,
            [
                "type" => "https://symfony.com/errors/validation",
                "title" => "Validation Failed",
                "detail" => "content: Content cannot be empty !",
                "violations" => [
                    [
                        "propertyPath" => "content",
                        "title" => "Content cannot be empty !",
                        "parameters" => [
                            "{{ value }}" => "null"
                        ],
                        "type" => "urn:uuid:c1051bb4-d103-4f74-8988-acbcafc7fdc3"
                    ]
                ]
            ]
        );
    }

    public function testGetComment(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/comment/1');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['id' => 1, 'content' => 'Testing comment']);
    }

    public function testGetAllComment(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/comment');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData,
            [
                0 => [
                    "id" => 1,
                    "content" => "Testing comment"
                ]
            ]
        );
    }

    public function testCommentNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('GET', '/api/comment/2');

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Comment not found."], $responseData);
    }

    public function testUpdateComment(): void
    {
        $client = static::createClient();
        $client->jsonRequest('PUT', '/api/comment/1', ['content' => 'Update testing comment']);

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['id' => 1, 'content' => 'Update testing comment']);
    }

    public function testUpdateCommentNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('PUT', '/api/comment/2', ['content' => 'Update testing']);

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['message' => 'Comment not found.']);
    }

    public function testUpdateCommentIncorrectFormat(): void
    {
        $client = static::createClient();
        $client->jsonRequest('PUT', '/api/comment/1');

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals($responseData, ['message' => 'Request content is empty or wrongly formatted.']);
    }

    public function testDeleteComment(): void
    {
        $client = static::createClient();
        $client->jsonRequest('DELETE', '/api/comment/1');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Comment deleted."], $responseData);
    }

    public function testDeleteAllComment(): void
    {
        $client = static::createClient();
        $client->jsonRequest('DELETE', '/api/comment');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Comment deleted."], $responseData);
    }

    public function testDeleteCommentNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('DELETE', '/api/comment/1');

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Comment not found."], $responseData);
    }

    public function testDeletePost(): void
    {
        $client = static::createClient();
        $client->jsonRequest('DELETE', '/api/post/21');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Post deleted."], $responseData);
    }

    public function testDeletePostNotFound(): void
    {
        $client = static::createClient();
        $client->jsonRequest('DELETE', '/api/post/21');

        $response = $client->getResponse();
        $this->assertResponseStatusCodeSame(400);
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Post not found."], $responseData);
    }

    public function testDeleteAllPost(): void
    {
        $client = static::createClient();
        $client->jsonRequest('DELETE', '/api/post');

        $response = $client->getResponse();
        $this->assertResponseIsSuccessful();
        $this->assertJson($response->getContent());
        $responseData = json_decode($response->getContent(), true);
        $this->assertEquals(['message' => "Post deleted."], $responseData);
    }
}
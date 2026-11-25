<?php

use App\Models\Language;

test('anyone can list the language catalog ordered by name', function () {
    Language::factory()->create(['code' => 'es', 'name' => 'Spanish']);
    Language::factory()->create(['code' => 'en', 'name' => 'English']);

    $response = $this->getJson('/api/v1/languages');

    $response->assertOk()
        ->assertJsonCount(2, 'data')
        ->assertJsonPath('data.0.name', 'English')
        ->assertJsonPath('data.1.name', 'Spanish')
        ->assertJsonStructure([
            'data' => [
                '*' => ['id', 'code', 'name'],
            ],
        ]);
});

test('the language catalog returns an empty list when there are no languages', function () {
    $this->getJson('/api/v1/languages')
        ->assertOk()
        ->assertExactJson(['data' => []]);
});

test('unknown api routes respond with a json 404', function () {
    $this->get('/api/v1/does-not-exist')
        ->assertNotFound()
        ->assertHeader('Content-Type', 'application/json')
        ->assertJsonStructure(['message']);
});

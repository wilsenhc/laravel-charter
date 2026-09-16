<?php

use App\Jobs\RecordApplicationBuildStatJob;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;

function webmcp_initialize(): TestResponse
{
    return test()->postJson('/mcp/charter', [
        'jsonrpc' => '2.0',
        'id' => 'init-1',
        'method' => 'initialize',
        'params' => [
            'protocolVersion' => '2025-06-18',
            'capabilities' => (object) [],
            'clientInfo' => ['name' => 'webmcp-test', 'version' => '1.0.0'],
        ],
    ]);
}

function webmcp_notification_initialized(): TestResponse
{
    return test()->postJson('/mcp/charter', [
        'jsonrpc' => '2.0',
        'method' => 'notifications/initialized',
        'params' => (object) [],
    ]);
}

function webmcp_rpc(string $method, string $id, array $params = [], array $headers = []): TestResponse
{
    return test()->postJson('/mcp/charter', [
        'jsonrpc' => '2.0',
        'id' => $id,
        'method' => $method,
        'params' => $params,
    ], $headers);
}

function webmcp_handshake(): void
{
    $response = webmcp_initialize();

    test()->expect($response->status())->toBe(200)
        ->and($response->json('result.protocolVersion'))->toBe('2025-06-18');

    test()->expect(webmcp_notification_initialized()->status())->toBe(202);
}

it('serves the full JSON-RPC handshake with both Charter tools', function () {
    webmcp_handshake();

    $response = webmcp_rpc('tools/list', 'list-1');

    $response->assertOk();

    $tools = collect($response->json('result.tools'));

    expect($tools->pluck('name'))->toContain('build-application', 'build-package');

    $applicationTool = $tools->firstWhere('name', 'build-application');

    expect($applicationTool['inputSchema']['type'])->toBe('object')
        ->and($applicationTool['inputSchema']['properties'])->toHaveKeys(['name', 'services'])
        ->and($applicationTool['annotations']['readOnlyHint'])->toBeTrue();
});

it('returns the generated script from tools/call', function () {
    Queue::fake();

    webmcp_handshake();

    $response = webmcp_rpc('tools/call', 'call-1', [
        'name' => 'build-application',
        'arguments' => ['name' => 'my-app', 'services' => ['redis']],
    ]);

    $response->assertOk();

    expect($response->json('result.isError'))->toBeFalse()
        ->and($response->json('result.content.0.type'))->toBe('text')
        ->and($response->json('result.content.0.text'))->toContain('laravel new my-app');
});

it('records WebMCP builds via the X-Mcp-Source header', function () {
    Queue::fake();

    webmcp_handshake();

    $response = webmcp_rpc('tools/call', 'call-2', [
        'name' => 'build-application',
        'arguments' => ['name' => 'my-app', 'services' => ['redis']],
    ], ['X-Mcp-Source' => 'webmcp']);

    $response->assertOk();

    Queue::assertPushed(RecordApplicationBuildStatJob::class, fn (RecordApplicationBuildStatJob $job): bool => $job->data['mcp_source'] === 'webmcp');
});

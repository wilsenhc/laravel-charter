<?php

use App\Jobs\RecordApplicationBuildStat;
use Illuminate\Support\Facades\Queue;
use Illuminate\Testing\TestResponse;

function webmcp_initialize(string $sessionId = ''): TestResponse
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
    ], $sessionId !== '' ? ['Mcp-Session-Id' => $sessionId] : []);
}

function webmcp_notification_initialized(string $sessionId): TestResponse
{
    return test()->postJson('/mcp/charter', [
        'jsonrpc' => '2.0',
        'method' => 'notifications/initialized',
        'params' => (object) [],
    ], ['Mcp-Session-Id' => $sessionId]);
}

function webmcp_rpc(string $sessionId, string $method, string $id, array $params = [], array $headers = []): TestResponse
{
    return test()->postJson('/mcp/charter', [
        'jsonrpc' => '2.0',
        'id' => $id,
        'method' => $method,
        'params' => $params,
    ], ['Mcp-Session-Id' => $sessionId, ...$headers]);
}

function webmcp_session(string $id = 'init-1'): string
{
    $response = webmcp_initialize();

    test()->expect($response->status())->toBe(200);

    $sessionId = $response->headers->get('Mcp-Session-Id');

    test()->expect($sessionId)->not->toBeNull();

    $initialized = webmcp_notification_initialized($sessionId);

    test()->expect($initialized->status())->toBe(202);

    return $sessionId;
}

it('serves the full JSON-RPC handshake with both Charter tools', function () {
    $sessionId = webmcp_session();

    $response = webmcp_rpc($sessionId, 'tools/list', 'list-1');

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

    $sessionId = webmcp_session();

    $response = webmcp_rpc($sessionId, 'tools/call', 'call-1', [
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

    $sessionId = webmcp_session();

    $response = webmcp_rpc($sessionId, 'tools/call', 'call-2', [
        'name' => 'build-application',
        'arguments' => ['name' => 'my-app', 'services' => ['redis']],
    ], ['X-Mcp-Source' => 'webmcp']);

    $response->assertOk();

    Queue::assertPushed(RecordApplicationBuildStat::class, fn (RecordApplicationBuildStat $job): bool => $job->data['mcp_source'] === 'webmcp');
});

<?php

namespace App\Mcp\Traits;

trait DetectsMcpSource
{
    private function detectMcpSource(): string
    {
        $agent = strtolower(request()->userAgent() ?? '');

        return match (true) {
            str_contains($agent, 'opencode') => 'opencode',
            str_contains($agent, 'claude') => 'claude',
            str_contains($agent, 'cursor') => 'cursor',
            str_contains($agent, 'codex') => 'codex',
            default => 'mcp',
        };
    }
}

<?php

namespace App\Mcp\Servers;

use App\Mcp\Tools\BuildApplicationTool;
use App\Mcp\Tools\BuildPackageTool;
use Laravel\Mcp\Server;
use Laravel\Mcp\Server\Attributes\Instructions;
use Laravel\Mcp\Server\Attributes\Name;
use Laravel\Mcp\Server\Attributes\Version;

#[Name('Charter for Laravel Builder')]
#[Version('1.0.0')]
#[Instructions('This server provides tools for scaffolding new Laravel applications and packages. Use the build-application tool to generate a bash script that creates a new Laravel project with Sail and your preferred options. Use the build-package tool to generate a bash script that bootstraps a new Laravel package with your chosen features and metadata.')]
class CharterServer extends Server
{
    protected array $tools = [
        BuildApplicationTool::class,
        BuildPackageTool::class,
    ];

    protected array $resources = [];

    protected array $prompts = [];
}

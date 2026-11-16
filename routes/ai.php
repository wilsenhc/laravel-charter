<?php

declare(strict_types=1);

use App\Mcp\Servers\CharterServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/charter', CharterServer::class);

<?php

use App\Mcp\Servers\CharterServer;
use Laravel\Mcp\Facades\Mcp;

Mcp::web('/mcp/charter', CharterServer::class);

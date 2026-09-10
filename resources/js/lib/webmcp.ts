const MCP_ENDPOINT = '/mcp/charter';
const MCP_SOURCE_HEADER = 'X-Mcp-Source: webmcp';
const CHARTER_TOOL_NAMES = ['build-application', 'build-package'];

interface ModelContextTool {
    name: string;
    description: string;
    inputSchema: Record<string, unknown>;
    annotations: { readOnlyHint: boolean };
    execute: (
        args: Record<string, unknown>,
        options: { signal: AbortSignal },
    ) => Promise<string> | string;
}

interface ModelContext {
    registerTool(tool: ModelContextTool): Promise<void>;
}

type JsonRpcResult = Record<string, any>;

let sessionId: string | null = null;
let initialization: Promise<void> | null = null;
let requestId = 0;

function headers(): Record<string, string> {
    return {
        'Content-Type': 'application/json',
        'X-Mcp-Source': 'webmcp',
        ...(sessionId !== null ? { 'Mcp-Session-Id': sessionId } : {}),
    };
}

async function initializeSession(): Promise<void> {
    initialization ??= (async () => {
        const response = await fetch(MCP_ENDPOINT, {
            method: 'POST',
            headers: headers(),
            body: JSON.stringify({
                jsonrpc: '2.0',
                id: ++requestId,
                method: 'initialize',
                params: {
                    protocolVersion: '2025-06-18',
                    capabilities: {},
                    clientInfo: { name: 'Charter WebMCP', version: '1.0.0' },
                },
            }),
        });

        sessionId = response.headers.get('Mcp-Session-Id');

        if (!response.ok) {
            throw new Error(`MCP initialize failed with status ${response.status}`);
        }

        const message = await response.json();

        if (message.error) {
            throw new Error(`MCP initialize failed: ${message.error.message}`);
        }

        fetch(MCP_ENDPOINT, {
            method: 'POST',
            headers: headers(),
            body: JSON.stringify({
                jsonrpc: '2.0',
                method: 'notifications/initialized',
                params: {},
            }),
        }).catch(() => {});
    })();

    return initialization;
}

async function rpc(method: string, params: Record<string, unknown> = {}, signal?: AbortSignal): Promise<JsonRpcResult> {
    await initializeSession();

    const response = await fetch(MCP_ENDPOINT, {
        method: 'POST',
        headers: headers(),
        signal,
        body: JSON.stringify({
            jsonrpc: '2.0',
            id: ++requestId,
            method,
            params,
        }),
    });

    const message = await response.json();

    if (message.error) {
        throw new Error(message.error.message ?? `MCP request [${method}] failed`);
    }

    return message.result;
}

function contentText(content: Array<{ text?: string }> | undefined): string {
    return (content ?? []).map((item) => item.text ?? '').join('');
}

async function registerTools(): Promise<void> {
    try {
        await initializeSession();

        const { tools } = await rpc('tools/list');

        const modelContext = (document as Document & { modelContext: ModelContext }).modelContext;

        for (const tool of tools) {
            if (!CHARTER_TOOL_NAMES.includes(tool.name)) {
                continue;
            }

            await modelContext.registerTool({
                name: tool.name,
                description: tool.description ?? '',
                inputSchema: tool.inputSchema ?? { type: 'object', properties: {} },
                annotations: { readOnlyHint: true },
                execute: async (args, { signal }) => {
                    const result = await rpc('tools/call', { name: tool.name, arguments: args }, signal);

                    if (result.isError) {
                        throw new Error(contentText(result.content) || `Tool [${tool.name}] failed`);
                    }

                    return contentText(result.content);
                },
            });
        }
    } catch (error) {
        console.warn('Charter WebMCP tools were not registered:', error);
    }
}

if (typeof document !== 'undefined' && 'modelContext' in document) {
    void registerTools();
}

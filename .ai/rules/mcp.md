---
paths:
  - 'app/Mcp/**'
---

# Mcp

## Always pin MCP tool names with #[Name]
Laravel\\Mcp tool names fall back to Str::kebab(class_basename), so BuildApplicationTool would expose as "build-application-tool". WebMCP registration in resources/js/lib/webmcp.ts and all docs reference "build-application"/"build-package", so every new tool MUST carry #[Name('kebab-name')] to keep protocol names matching the docs.

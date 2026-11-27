---
name: Charter for Laravel
description: A terminal-notebook builder that turns Laravel Sail configuration into one copyable command.
colors:
  primary: "#b54424"
  primary-foreground: "#ffffff"
  logo-orange: "#f15a30"
  ink: "#171717"
  canvas: "#fdfcfc"
  surface-soft: "#f8f7f7"
  surface-card: "#f1eeee"
  surface-dark: "#171717"
  surface-dark-elevated: "#222222"
  muted-foreground: "#646262"
  ash: "#9a9898"
  hairline: "rgba(15, 0, 0, 0.12)"
  grid-line: "rgba(15, 0, 0, 0.05)"
  accent-surface: "#eae8e8"
  on-dark: "#ffffff"
  on-dark-muted: "#8c8c8c"
  success: "#30d158"
  warning: "#ff9f0a"
  danger: "#ff3b30"
  dark-canvas: "#171717"
  dark-surface: "#222222"
  dark-muted-foreground: "#8c8c8c"
  dark-hairline: "rgba(253, 252, 252, 0.14)"
  dark-grid-line: "rgba(253, 252, 252, 0.04)"
typography:
  display:
    fontFamily: "Geist Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "30px"
    fontWeight: 700
    lineHeight: 1.2
    letterSpacing: "-0.025em"
  headline:
    fontFamily: "Geist Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "18px"
    fontWeight: 600
    lineHeight: 1.55
    letterSpacing: "-0.025em"
  title:
    fontFamily: "Geist Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "16px"
    fontWeight: 500
    lineHeight: 1.375
  body:
    fontFamily: "Geist Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "14px"
    fontWeight: 400
    lineHeight: 1.5
  label:
    fontFamily: "Geist Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "14px"
    fontWeight: 500
    lineHeight: 1.4
  micro:
    fontFamily: "Geist Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "12px"
    fontWeight: 400
    lineHeight: 1.5
  status:
    fontFamily: "Geist Mono, ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, 'Liberation Mono', 'Courier New', monospace"
    fontSize: "11px"
    fontWeight: 500
    lineHeight: 1
    letterSpacing: "0.05em"
rounded:
  none: "0px"
  sm: "4px"
  full: "9999px"
spacing:
  xs: "4px"
  sm: "8px"
  md: "12px"
  lg: "16px"
  xl: "24px"
  xxl: "32px"
  page: "28px"
components:
  button-primary:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.primary-foreground}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    padding: "4px 10px"
    height: "32px"
  button-primary-hover:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.primary-foreground}"
    rounded: "{rounded.sm}"
  button-outline:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    padding: "4px 10px"
    height: "32px"
  input:
    backgroundColor: "{colors.surface-soft}"
    textColor: "{colors.ink}"
    typography: "{typography.body}"
    rounded: "{rounded.sm}"
    padding: "4px 10px"
    height: "32px"
  chip-selected:
    backgroundColor: "{colors.primary}"
    textColor: "{colors.primary-foreground}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    padding: "4px 12px"
    height: "32px"
  chip-unselected:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.label}"
    rounded: "{rounded.sm}"
    padding: "4px 12px"
    height: "32px"
  card:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    rounded: "{rounded.none}"
    padding: "16px"
  terminal-panel:
    backgroundColor: "{colors.surface-dark}"
    textColor: "{colors.on-dark}"
    typography: "{typography.body}"
    rounded: "{rounded.none}"
    padding: "16px"
  primary-nav:
    backgroundColor: "{colors.canvas}"
    textColor: "{colors.ink}"
    typography: "{typography.label}"
    rounded: "{rounded.none}"
    height: "56px"
---

# Design System: Charter for Laravel

## Overview

**Creative North Star: "The Build Sheet"**

Charter is a page from a build worksheet: warm paper with a faint 32px grid, every word in Geist Mono, configuration presented as a checklist of bracketed choices, and the finished command pasted at the top like the result of the calculation. The visitor is not reading a marketing page; they are filling in a sheet. The sheet should feel measured, legible, and already half-finished before they touch it.

The system's visual DNA is inherited from the captured opencode.ai terminal-native system that seeded this project: one monospaced face, cream canvas, hairline rules instead of shadows, `[x]` / `[ ]` markers instead of icons, and 4px corners on the few things you can click. Charter renames the accent to Solder Terracotta (`{colors.primary}`), a fired-tool red rather than a brand blue, and turns the surface into a working build sheet rather than a marketing page. The command panel is the one inherited dark TUI moment, now doing real work as a live result rather than a mockup.

The result is a tool that looks like it was assembled by someone who builds things: flat, exact, and quietly opinionated. It carries no photography, no gradients, no decorative illustration, and no dark mode of its own beyond a straightforward inversion.

**Key Characteristics:**
- One typeface for every role: Geist Mono, from the 30px page title to the 11px terminal status.
- One accent: Solder Terracotta, used for the primary action and the selected state, almost nothing else.
- One dark moment per page: the live command terminal. No other dark surface.
- One column: configuration stacks; the finished command sits at the top, never in a sidebar.
- One icon vocabulary: ASCII brackets (`[x]`, `[ ]`, `[+]`, `[−]`, `$`, `#`) before any SVG.
- Flat on paper: 1px hairlines, square containers, 4px interactive corners, zero drop shadows.

## Colors

A warm monochrome workspace interrupted once by a terracotta action color and once by a near-black terminal.

### Primary
- **Solder Terracotta** (`{colors.primary}` — `#b54424`): the primary button, the selected chip, the active switch, and the focus action of the page. White text on top. Hover is the same terracotta at 80% opacity over the paper, which reads as a slightly softer solder rather than a different color.
- **Logo Orange** (`{colors.logo-orange}` — `#f15a30`): the mountain mark in the wordmark only. It is brighter than the UI accent and is never used for buttons, chips, or state.

### Neutral
- **Paper** (`{colors.canvas}` — `#fdfcfc`): every page background, card surface, and outlined control. It has a faint warm cast; it is not pure white.
- **Ink** (`{colors.ink}` — `#171717`): body and heading text in light mode.
- **Muted Ink** (`{colors.muted-foreground}` — `#646262`): descriptions, legends, hints, inactive nav, and footer copy.
- **Ash** (`{colors.ash}` — `#9a9898`): disabled text and the least-emphasis annotation.
- **Hairline** (`{colors.hairline}` — `rgba(15, 0, 0, 0.12)`): the 1px border on every card, input, chip, and header/footer rule. Borders do the work shadows would do elsewhere.
- **Grid Line** (`{colors.grid-line}` — `rgba(15, 0, 0, 0.05)`): the 32px paper grid painted on the root element.
- **Soft Surface** (`{colors.surface-soft}` — `#f8f7f7`): input wells and the full-bleed `band` sections.
- **Card Surface** (`{colors.surface-card}` — `#f1eeee`): the command block in its pre-terminal life, disabled fills, and tonal emphasis.
- **Accent Surface** (`{colors.accent-surface}` — `#eae8e8`): hover fill for ghost/outline controls.
- **Terminal** (`{colors.surface-dark}` — `#171717`): the command panel. In dark mode it steps up to `{colors.surface-dark-elevated}` (`#222222`) so it separates from the `#171717` page.

### Semantic
Semantic color is reserved for inline state, chiefly inside the terminal panel and form errors. It is never page decoration.
- **Success** (`{colors.success}` — `#30d158`): `[READY]` in the terminal toolbar.
- **Warning** (`{colors.warning}` — `#ff9f0a`): `[FIX FIELDS]` and the invalid hint in the terminal.
- **Danger** (`{colors.danger}` — `#ff3b30`): destructive errors outside the terminal (invalid fields).

### Named Rules
**The Solder Rule.** Terracotta is the action and the selected state, and nothing else. If it is not clickable or currently chosen, it does not get the accent; its scarcity is what makes the Copy button read as the next step.

**The One Dark Moment Rule.** A page may have exactly one near-black terminal surface. The command panel earns it; do not add a second dark card, a dark footer, or a dark hero.

## Typography

**Display Font:** Geist Mono (falling back to ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New)
**Body Font:** Geist Mono (same stack)
**Label/Mono Font:** Geist Mono

**Character:** One voice, no pairing. The identity comes from refusing a second family: headings, body, labels, buttons, badges, footer fine print, and terminal output all sit in the same monospaced face. Hierarchy is built from size and weight, never from a new font, italics, or letter-spaced display caps.

### Hierarchy
- **Display** (700, 30px, 1.2, -0.025em): the page title only ("Laravel Sail app in one command").
- **Headline** (600, 18px, 1.55, -0.025em): section headings such as "Frequently Asked Questions" and the MCP page's "Available Tools".
- **Title** (500, 16px, 1.375): card titles ("Application", "Services", "Options"), nav wordmark, and command block headings in prose.
- **Body** (400, 14px, 1.5): descriptions, hints, labels, and terminal output. Prose is capped at `max-w-prose` (65–75ch).
- **Label** (500, 14px, 1.4): field labels, buttons, chips, and nav links.
- **Micro** (400, 12px, 1.5): legends, debug lines, Docker notices, and the `#` configuration summary.
- **Status** (500, 11px, 1, 0.05em, uppercase): `[READY]` / `[FIX FIELDS]` only.

### Named Rules
**The One Face Rule.** Geist Mono carries every role. Do not introduce a display face, a serif, or a sans body; a second family would read as a different product.

## Layout

A single centered column, `max-w-5xl` (1024px), with 20px side gutters and 28px top/bottom padding. Blocks stack with a 24px gap. The page title and its one-paragraph description open the page with 32px below them; the rest of the page is cards in the same column.

**Order is the layout.** On the builders the live command comes first, directly under the intro, then the configuration it reflects: Application → Services → Options → FAQ. Package is command → Package → optional metadata. No field that changes the command sits below it, and nothing lives in a second column.

**The paper grid** is painted on `html` with two 1px linear gradients at a 32px pitch. It is part of the page surface, not an overlay; content sits directly on it, and the grid never appears inside cards or the terminal.

**Header and footer.** The header is 56px, sticky, with a hairline bottom, the wordmark at left, three nav links (`Application`, `Package`, `MCP`), and the Install/GitHub/language/appearance controls at right. It gains a translucent blur only after scroll. The footer is a centered, hairline-topped block of muted 14px links separated by `|`, ending with the disclaimer and credits.

**Rhythm.** 16px is the standard card padding, 12px the gap between a label and its control, 8px inside chip rows, and 4px for tight inline gaps. Section-to-section separation is 24px; the FAQ card sits 24px below the configuration blocks.

**Responsive.** Breakpoints follow Tailwind defaults: `sm` 640, `md` 768, `lg` 1024, `xl` 1280, `2xl` 1536. Below `lg` the header nav collapses into the mobile sheet (the same three links plus install, language and appearance); below `sm` field pairs stack to one column, chip targets grow to 44px minimum height, and the terminal toolbar wraps the status and Copy to a second row.

### Named Rules
**The Build Sheet Rule.** Configuration is the checklist; the command is the computed result. Put the command at the top of the sheet, then the choices, never the other way round.

**The One Column Rule.** Pages use one content column. No sidebar, side panel, or sticky secondary column; a live result belongs inline at the top of the same column.

**The Menu Page Rule.** A page reachable from the header menu carries no "Back to…" link. Footer-only pages (Privacy, Terms, Stats, Glossary, Comparison) keep theirs.

## Elevation & Depth

The system is flat. There are no drop shadows on cards, buttons, inputs, or navigation. Depth is conveyed by three things only: 1px hairlines, tonal surface changes (paper → soft → card → terminal), and the single dark terminal panel. The `band` utility uses a `100vmax` box-shadow purely as a paint bleed for full-bleed tinted sections; it is not elevation. The sticky header is the only element allowed a backdrop blur, and only while the page is scrolled.

### Shadow Vocabulary
- **Bleed** (`box-shadow: 0 0 0 100vmax var(--surface-soft)`): paints a full-bleed `band` section; clipped horizontally so it never adds scroll width. Never use it to lift an element.
- **Focus ring** (`box-shadow: 0 0 0 3px color-mix(in oklab, var(--ring) 50%, transparent)` via `ring-3`): focus-visible only.
- **Panel separation** (1px `{colors.dark-hairline}` border): the terminal's only edge treatment.

### Named Rules
**The Flat Paper Rule.** Surfaces are flat at rest. If you reach for a shadow, reach for a hairline or a tone change instead.

## Shapes

Containers are square (`{rounded.none}`): cards, the terminal panel, the header, the footer, and full-width bands all meet the page at 90°. Interactive elements are 4px (`{rounded.sm}`): buttons, inputs, selects, chips, tabs, the terminal tool toggles. The switch track and its thumb are the only fully round shapes (`{rounded.full}`), because a switch has to read as a switch.

Borders are always 1px. A card is a hairline rectangle, not a raised tile. Divider lines inside cards run edge to edge. The terminal panel's inner dividers are 1px white at 10% opacity.

Iconography is ASCII first: `[x]` selected, `[ ]` unselected, `[+]` closed disclosure, `[−]` open disclosure, `$` shell prompt, `#` terminal comment, `·` inline separator. SVG (Lucide) is reserved for actions and controls where a glyph is genuinely clearer: copy, check, download, info, menu, select and accordion chevrons, GitHub, heart, refresh, language and appearance. The bracket markers are never replaced by checkboxes or icon fonts.

### Named Rules
**The Bracket Rule.** State is spelled in ASCII before it is drawn. Chips use `[x]` / `[ ]`; the package metadata disclosure uses `[+]` / `[−]`. Do not replace those markers with checkbox components, chevrons, or icon fonts. The FAQ is the one toggle that uses the accordion primitive's chevron rather than a bracket.

## Components

### Buttons
- **Shape:** 4px radius; 32px default height (`h-8`), 28px small (`h-7`), 10px horizontal padding.
- **Primary:** Solder Terracotta fill, white label, 14px medium. Hover softens to 80% opacity over paper; active shifts down 1px; disabled drops to 50% opacity and becomes non-interactive.
- **Outline:** paper fill, ink label, 1px hairline border; hover uses `{colors.accent-surface}`.
- **Ghost:** transparent until hover, then `{colors.accent-surface}` or the tinted muted surface.
- **Focus:** 3px ring at 50% of the ink ring color; never remove it.

### Chips
- **Style:** real `<button>` elements with `aria-pressed`; 4px radius, 1px hairline, 14px medium, `[x]` / `[ ]` prefix in 12px at 70% opacity.
- **Selected:** Solder Terracotta fill, white text; hover at 90%.
- **Unselected:** paper fill, ink text, hairline border; hover softens to the muted surface.
- **Targets:** 32px desktop, 44px minimum on touch (`min-h-11`).

### Cards / Containers
- **Corner Style:** square (`{rounded.none}`).
- **Background:** `{colors.canvas}` on the page; `{colors.surface-card}` only for disabled or tonal fills.
- **Border:** 1px `{colors.hairline}`, edge to edge.
- **Shadow Strategy:** none (see Elevation).
- **Internal Padding:** 16px, with a 16px gap between a bordered header and its content. Card headers carry the hairline bottom; titles are 16px medium.

### Inputs / Fields
- **Style:** 32px height, `{colors.surface-soft}` well, 1px hairline, 4px radius, 10px horizontal padding, 16px text below `md` (avoids iOS zoom) and 14px at `md` and up.
- **Focus:** border shifts to ink plus a 3px 50% ring.
- **Error:** danger border and ring; the error text sits under the field at 12px and is wired with `aria-describedby`.
- **Selects** use the same trigger geometry; selected values display their label, not the raw flag (`PostgreSQL`, not `pgsql`).

### Navigation
- **Header:** 56px, sticky, paper background, hairline bottom. Wordmark 16px bold with the logo mark. Links are 14px: muted at rest, ink semibold when active. Three links only: Application, Package, MCP.
- **Right cluster:** Install and GitHub as outline buttons, then language and appearance icon buttons; all controls are 32px.
- **Mobile:** below `lg`, a hamburger opens a right-hand sheet (72 width) listing the same links, install, language, and appearance. The Install CTA stays visible in the header where space allows.
- **Footer:** hairline top, centered 14px muted links separated by `|`, then the non-affiliation disclaimer and credits.

### Terminal Command Panel (signature)
The page's one dark surface, placed directly under the page intro.
- **Geometry:** square near-black panel, 1px white/10 border, 16–20px padding; no radius, no shadow.
- **Toolbar:** `curl` / `wget` as a bracketed toggle group (active 10% white fill, inactive 60% white); a status (`[READY]` in success green or `[FIX FIELDS]` in warning amber, 11px uppercase); and the terracotta Copy button.
- **Command:** `$` prompt at 35% white, then the command in white 14px, fully wrapping (`overflow-wrap: anywhere`) rather than clipping. Prompt and cursor are `select-none`, so copying or selecting yields only the command.
- **Summary:** `#` comment at 35% white, then the live configuration tokens at 60% white (`php 8.5 · vue · pest · pnpm · mysql · redis · boost`).
- **Hint:** 12px at 60% white; on invalid input it becomes the warning-coloured fix message.
- **States:** ready (green status, enabled Copy), invalid (amber status, disabled Copy, specific fix line), copied (check icon plus "Copied" for 2s).

### Fields and Disclosure
- **Package metadata** is a native `<details>` inside a hairline card, collapsed by default: `[+] Package Metadata (optional)` becomes `[−]` when open, with a hairline above the revealed fields. The summary is a 44px target.
- **FAQ** uses the accordion primitive: 14px-medium questions with 10px padding, 4px radius, hairline rows, and a Lucide chevron that flips on open. No shadows, no nested cards.

### Tabs, Switches, Toasts
- **Tabs** (MCP client config) are 4px rounded, muted track, paper active thumb.
- **Switches** are 32×18.4px tracks; terracotta when checked, hairline-bordered input colour when not; the thumb is paper.
- **Toast** (PWA update) slides up 300ms ease-out from the bottom-right; it is the only entrance animation in the system.

## Do's and Don'ts

### Do:
- **Do** keep every page to one column, with the live command at the top on both builders.
- **Do** render all type in Geist Mono; build hierarchy from 30/18/16/14/12/11px sizes and 700/600/500/400 weights.
- **Do** use Solder Terracotta only for the primary action and the selected state.
- **Do** keep the logo mark in its own Logo Orange (`{colors.logo-orange}`); it is not the UI accent.
- **Do** use `[x]` / `[ ]` for chip state and `[+]` / `[−]` for disclosure; keep them as text.
- **Do** separate with 1px hairlines and tone changes; containers stay square, controls 4px.
- **Do** allow exactly one terminal panel per page, and let the command wrap in full.
- **Do** keep a page that lives in the header menu free of back links.
- **Do** show labels in human terms (`Database`, `PostgreSQL`) while the command keeps CLI values (`--database=pgsql`).

### Don't:
- **Don't** add a sidebar, second column, side panel, or sticky secondary column.
- **Don't** put the command below the fields that change it, or reintroduce a "Command" heading; the panel and page title already say what it is.
- **Don't** add drop shadows, gradients, glass, glow, or dark cards beyond the single terminal panel.
- **Don't** use semantic colors (success/warning/danger) as page decoration or marketing accents.
- **Don't** introduce a second typeface, italics, or a display serif.
- **Don't** replace bracket markers with checkboxes, chevrons, or icon fonts.
- **Don't** round containers; only interactive elements get 4px, and only the switch track is fully round.
- **Don't** put a second dark surface on a page, or reuse the terminal panel for non-command content.

## Recent Decisions

Changes made on 2026-11-25, recorded so later work does not undo them.

- **Single column, no sidebar.** The builders' configuration/result split was removed after review; the rule is now documented in `.ai/rules/pages.md` and the command lives inline.
- **Command first.** The live terminal panel moved directly under the page intro so the result is visible without scrolling. The page-level note says it updates as the options below change.
- **Terminal overdrive.** The command block became the page's one dark TUI moment: status, configuration summary, wrapped command, selected-tool toggle, and disabled/invalid state in amber.
- **"Command" label removed** from the panel toolbar; the word survives only as the tab group's accessible name.
- **Distillation.** Removed the duplicated "How it works" sections, the three competing hero pills, and the duplicate Database Driver + database chips; the Database select now adds its Sail service automatically. Services and features use `[x]` / `[ ]` instead of the ambiguous `[−]`.
- **Package metadata** is a collapsed `[+]` disclosure, no longer a block below the command.
- **Menu MCP.** MCP joined Application and Package in the header navigation; menu pages no longer show "Back to Charter", while footer-only pages keep their back links.
- **Copy correction.** The builders create a Laravel app or package; Sail is the dev environment the app command sets up, so copy never calls the product a "Laravel Sail app".

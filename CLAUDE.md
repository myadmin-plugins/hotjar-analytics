# MyAdmin Hotjar Analytics Plugin

Composer plugin integrating Hotjar analytics into the MyAdmin control panel via Symfony EventDispatcher hooks.

## Commands

```bash
composer install                                                     # install deps
vendor/bin/phpunit tests/ -v        # run all tests
vendor/bin/phpunit tests/ -v --coverage-clover coverage.xml --whitelist src/  # with coverage
```

## Architecture

- **Plugin class**: `src/Plugin.php` · namespace `Detain\MyAdminHotjar` · PSR-4 autoloaded
- **Tests**: `tests/PluginTest.php` · namespace `Detain\MyAdminHotjar\Tests` · PHPUnit 9.6
- **PHPUnit config**: `phpunit.xml.dist`
- **CI**: `.scrutinizer.yml` (primary) · `.codeclimate.yml` · `.travis.yml` (legacy)
- **CI/CD workflows**: `.github/` contains automated test and deployment workflows
- **IDE config**: `.idea/` contains project IDE settings — `inspectionProfiles/`, `deployment.xml`, `encodings.xml`
- **Dependency**: `symfony/event-dispatcher ^5.0` — all event handlers accept `GenericEvent $event`

## Plugin Lifecycle Methods

All methods in `src/Plugin.php` are `public static`:

```php
public static function getHooks(): array           // returns event name => [class, method] map
public static function getMenu(GenericEvent $event)        // adds admin menu entries via ACL check
public static function getRequirements(GenericEvent $event) // registers class/function autoload paths
public static function getSettings(GenericEvent $event)    // handles plugin config settings
```

## Adding Requirements

In `getRequirements()`, retrieve loader from `$event->getSubject()` and call:
```php
$loader->add_requirement('class.ClassName', 'src/ClassName.php');
$loader->add_requirement('function_name', 'src/file.inc.php');
```

## Static Properties

`Plugin::$name`, `Plugin::$description`, `Plugin::$help`, `Plugin::$type` — all `public static string`.

## Coding Conventions

- Tab indentation (per `.scrutinizer.yml` `use_tabs: true`)
- No closing PHP tags (`avoid_closing_tag: true`)
- camelCase properties and parameters
- `UPPERCASE` constants
- Alphabetically ordered `use` statements, unused ones removed
- All event handler methods return `void` (no explicit return type required)
- Menu ACL guard: `if ($GLOBALS['tf']->ima == 'admin') { function_requirements('has_acl'); if (has_acl('...')) { ... } }`

## Test Conventions

- Use `ReflectionClass` in `setUp()` to introspect `Plugin::class`
- Assert visibility (`isPublic`, `isStatic`) for every method and property
- Anonymous class stubs (no Mockery/Prophecy) for objects like the `$loader` in `getRequirements()`
- Test file: `tests/PluginTest.php` — one test class, granular single-assertion methods

<!-- caliber:managed:pre-commit -->
## Before Committing

**IMPORTANT:** Before every git commit, you MUST ensure Caliber syncs agent configs with the latest code changes.

First, check if the pre-commit hook is already installed:
```bash
grep -q "caliber" .git/hooks/pre-commit 2>/dev/null && echo "hook-active" || echo "no-hook"
```

- If **hook-active**: the hook handles sync automatically — just commit normally. Tell the user: "Caliber will sync your agent configs automatically via the pre-commit hook."
- If **no-hook**: run Caliber manually before committing:
  1. Tell the user: "Caliber: Syncing agent configs with your latest changes..."
  2. Run: `caliber refresh && git add CLAUDE.md .claude/ .cursor/ .cursorrules .github/copilot-instructions.md .github/instructions/ AGENTS.md CALIBER_LEARNINGS.md 2>/dev/null`
  3. After it completes, briefly tell the user what Caliber updated. Then proceed with the commit.

If `caliber` is not found, tell the user: "This project uses Caliber for agent config sync. Run /setup-caliber to get set up."
<!-- /caliber:managed:pre-commit -->

<!-- caliber:managed:learnings -->
## Session Learnings

Read `CALIBER_LEARNINGS.md` for patterns and anti-patterns learned from previous sessions.
These are auto-extracted from real tool usage — treat them as project-specific rules.
<!-- /caliber:managed:learnings -->

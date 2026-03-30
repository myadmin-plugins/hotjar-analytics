---
name: myadmin-requirements
description: Registers class or function autoload requirements via $loader->add_requirement() inside getRequirements(GenericEvent $event) in src/Plugin.php. Use when user says 'register class', 'add autoload', 'require file', 'load dependency', or 'add requirement'. Do NOT use for getSettings(), getMenu(), or getHooks() changes.
---
# myadmin-requirements

## Critical

- **Class key format:** `'class.ClassName'` (prefix `class.` + PascalCase name, no extension)
- **Function key format:** bare function name, e.g. `'function_name'` (no prefix)
- **Path format:** starts with `'/../vendor/'` followed by the package path and `src/` directory + filename
- Multiple functions from the same include file each get their own `add_requirement()` call — never combine
- Never add a closing PHP tag (`?>`)
- Indentation: tabs only

## Instructions

1. Open `src/Plugin.php` and locate `getRequirements(GenericEvent $event)`.
   Verify the method signature is:
   ```php
   public static function getRequirements(GenericEvent $event)
   ```

2. Retrieve the loader from the event subject — this line must be first inside the method:
   ```php
   $loader = $event->getSubject();
   ```
   Verify `$loader` is assigned before any `add_requirement()` call.

3. Add each requirement with `$loader->add_requirement($name, $path)` referencing files under `src/`:
   - For a **class** (e.g. `src/Hotjar.php` in `src/Plugin.php`):
     ```php
     $loader->add_requirement('class.Hotjar', '/../vendor/detain/myadmin-hotjar-analytics/src/Hotjar.php');
     ```
   - For a **function** (e.g. `src/abuse.inc.php`):
     ```php
     $loader->add_requirement('deactivate_kcare', '/../vendor/detain/myadmin-hotjar-analytics/src/abuse.inc.php');
     ```
   Verify: key uses `class.` prefix for classes, bare name for functions.

4. If adding a new test, add a single-assertion method to `tests/PluginTest.php` using the anonymous-class stub pattern:
   ```php
   public function testGetRequirementsRegistersMyNewThing(): void
   {
       $loader = new class {
           /** @var array<int, array{0: string, 1: string}> */
           public array $requirements = [];
           public function add_requirement(string $name, string $path): void
           {
               $this->requirements[] = [$name, $path];
           }
       };
       $event = new GenericEvent($loader);
       Plugin::getRequirements($event);
       $names = array_column($loader->requirements, 0);
       $this->assertContains('class.MyNewThing', $names);
   }
   ```
   Also update `testGetRequirementsCallsAddRequirement` to expect the new count.
   Verify the test passes: `vendor/bin/phpunit tests/ -v`

## Examples

**User says:** "Register the `MyWidget` class and `render_widget` function from `src/Widget.php` and `src/widget.inc.php`."

**Actions taken:**
- In `getRequirements()` in `src/Plugin.php`, append after existing calls:
  ```php
  $loader->add_requirement('class.MyWidget', '/../vendor/detain/myadmin-hotjar-analytics/src/Widget.php');
  $loader->add_requirement('render_widget', '/../vendor/detain/myadmin-hotjar-analytics/src/widget.inc.php');
  ```
- Add two test methods (`testGetRequirementsRegistersMyWidgetClass`, `testGetRequirementsRegistersRenderWidget`) using the anonymous-class stub.
- Update `testGetRequirementsCallsAddRequirement` count assertion from `4` to `6`.

**Result:** `vendor/bin/phpunit tests/ -v` passes.

## Common Issues

- **Wrong key for a class** (`'Hotjar'` instead of `'class.Hotjar'`): MyAdmin's loader looks up classes by the `class.` prefix. Always prepend `class.` for PHP class files.
- **Path starts with `/vendor/` instead of `/../vendor/`**: The leading `/../` is required because the loader resolves relative to the web root. Missing it causes a file-not-found at runtime.
- **Two functions in the same file → one call**: Each function name must have its own `add_requirement()` line even if the path is identical. The loader registers names individually.
- **Test count mismatch** (`Failed asserting that 4 matches expected 5`): You added a requirement but forgot to update `testGetRequirementsCallsAddRequirement` — change `assertCount(N, ...)` to match the new total.
- **Tabs vs spaces** (`PHP_CodeSniffer: Expected tabs`): `.scrutinizer.yml` enforces `use_tabs: true`. Run `make php-cs-fixer` if linting fails.

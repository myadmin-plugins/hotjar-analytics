---
name: plugin-lifecycle
description: Adds or modifies lifecycle methods on src/Plugin.php following the static method pattern: getHooks(), getMenu(GenericEvent), getRequirements(GenericEvent), getSettings(GenericEvent). Use when user says 'add hook', 'register event', 'add menu item', 'add requirement', or 'modify plugin settings'. Do NOT use for writing tests or creating new plugin packages.
---
# plugin-lifecycle

## Critical

- All methods in `src/Plugin.php` MUST be `public static` — no instance methods.
- Event handler methods (`getMenu`, `getRequirements`, `getSettings`) MUST accept exactly `GenericEvent $event` and return void (no explicit return type required).
- `getHooks()` MUST return `array` with format `'event.name' => [__CLASS__, 'methodName']`. Comment out unused hooks with `//` rather than deleting them.
- Use tabs for indentation (never spaces) per `.scrutinizer.yml` `use_tabs: true`.
- No closing PHP tag (`?>`) at end of file.
- `use` statements must be alphabetically ordered; remove unused ones.

## Instructions

1. **Read `src/Plugin.php`** to understand the current state of hooks and requirements before making any changes. Verify the file uses namespace `Detain\MyAdminHotjar` and imports `Symfony\Component\EventDispatcher\GenericEvent`.

2. **To register a new event hook** — add the entry to `getHooks()` return array and implement the handler method:
   ```php
   public static function getHooks()
   {
       return [
           'system.settings' => [__CLASS__, 'getSettings'],
           'ui.menu' => [__CLASS__, 'getMenu'],
       ];
   }
   ```
   Verify the event name string matches what the MyAdmin core dispatches.

3. **To add a menu item** in `getMenu()` — always wrap in the admin ACL guard:
   ```php
   public static function getMenu(GenericEvent $event)
   {
       $menu = $event->getSubject();
       if ($GLOBALS['tf']->ima == 'admin') {
           function_requirements('has_acl');
           if (has_acl('client_billing')) {
               // add menu entry using $menu
           }
       }
   }
   ```
   Verify the ACL string (`'client_billing'`) is the correct permission for the feature.

4. **To register a class or function requirement** in `getRequirements()` — retrieve the loader from the event subject, then call `add_requirement()`:
   ```php
   public static function getRequirements(GenericEvent $event)
   {
       /** @var \MyAdmin\Plugins\Loader $this->loader */
       $loader = $event->getSubject();
       // For a class file:
       $loader->add_requirement('class.ClassName', '/../vendor/detain/myadmin-hotjar-analytics/src/ClassName.php');
       // For a function include file:
       $loader->add_requirement('function_name', '/../vendor/detain/myadmin-hotjar-analytics/src/file.inc.php');
   }
   ```
   Class keys use `class.` prefix for classes, bare name for functions. Paths start with `/../vendor/detain/myadmin-hotjar-analytics/`.

5. **To handle plugin settings** in `getSettings()` — retrieve settings from the event subject:
   ```php
   public static function getSettings(GenericEvent $event)
   {
       /** @var \MyAdmin\Settings $settings **/
       $settings = $event->getSubject();
       // interact with $settings object here
   }
   ```

6. **Verify** by running tests: `vendor/bin/phpunit tests/ -v`. All existing tests must pass. If you added a hook, a test in `tests/PluginTest.php` may need updating (e.g. `testGetHooksReturnsEmptyArray`).

## Examples

**User says:** "Register the system.settings and ui.menu hooks and add a menu item under has_acl('hotjar_analytics')."

**Actions taken:**
1. Read `src/Plugin.php` — hooks array is currently empty (all commented out).
2. Uncomment both entries in `getHooks()` return array.
3. Update `getMenu()` ACL check from `'client_billing'` to `'hotjar_analytics'`.

**Result in `src/Plugin.php`:**
```php
public static function getHooks()
{
	return [
		'system.settings' => [__CLASS__, 'getSettings'],
		'ui.menu' => [__CLASS__, 'getMenu'],
	];
}

public static function getMenu(GenericEvent $event)
{
	$menu = $event->getSubject();
	if ($GLOBALS['tf']->ima == 'admin') {
		function_requirements('has_acl');
		if (has_acl('hotjar_analytics')) {
			// menu entry here
		}
	}
}
```

## Common Issues

- **`Call to undefined function function_requirements()`** in `getMenu()`: This function is provided by the MyAdmin core at runtime. It will not exist when running unit tests in isolation — do not call `getMenu()` directly in tests without stubbing globals.
- **`testGetHooksReturnsEmptyArray` fails after adding hooks**: Update that test in `tests/PluginTest.php` to `assertNotEmpty()` or add a new assertion for the specific event key, e.g. `assertArrayHasKey('system.settings', $hooks)`.
- **Wrong `add_requirement` path**: Paths must start with `/../vendor/detain/myadmin-hotjar-analytics/` (note the leading `/../`). Omitting this prefix causes the loader to resolve incorrectly relative to the webroot.
- **Method not dispatched**: If a new handler method is never called, confirm its event name string is added to the `getHooks()` return array and is not commented out.

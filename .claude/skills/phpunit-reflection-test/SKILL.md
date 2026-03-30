---
name: phpunit-reflection-test
description: Writes PHPUnit 9 tests in `tests/PluginTest.php` following the project's ReflectionClass-based pattern: asserting visibility, parameter types, static properties, and method signatures. Use when user says 'add test', 'write test', 'test this method', or 'test coverage'. Do NOT use for modifying src/ files or for integration tests against real services.
---
# phpunit-reflection-test

## Critical

- **Never use Mockery or Prophecy** — use anonymous class stubs only (see Examples)
- **One assertion per test method** — do not combine unrelated assertions in a single test
- **Tab indentation** — the project uses tabs, not spaces (`.scrutinizer.yml`: `use_tabs: true`)
- **No closing PHP tag** at end of file
- All tests go in `tests/PluginTest.php`, class `PluginTest extends TestCase`, namespace `Detain\MyAdminHotjar\Tests`
- Run tests with: `vendor/bin/phpunit tests/ -v`

## Instructions

1. **Read `src/Plugin.php`** to identify all public static properties and methods before writing any test. Verify their names, types, and parameter signatures.

2. **Set up `ReflectionClass` in `setUp()`:**
   ```php
   private ReflectionClass $reflection;

   protected function setUp(): void
   {
       $this->reflection = new ReflectionClass(Plugin::class);
   }
   ```
   Verify `$this->reflection` is available in every test before proceeding.

3. **Assert static property visibility** — one test per property using `getProperty()`:
   ```php
   public function testNamePropertyIsPublicStatic(): void
   {
       $property = $this->reflection->getProperty('name');
       $this->assertTrue($property->isPublic());
       $this->assertTrue($property->isStatic());
   }
   ```

4. **Assert static property values** — one test per property using direct class access:
   ```php
   public function testNamePropertyValue(): void
   {
       $this->assertSame('Hotjar Plugin', Plugin::$name);
   }
   ```

5. **Assert method visibility** — one test per method using `getMethod()`:
   ```php
   public function testGetHooksIsPublicStatic(): void
   {
       $method = $this->reflection->getMethod('getHooks');
       $this->assertTrue($method->isPublic());
       $this->assertTrue($method->isStatic());
   }
   ```

6. **Assert `GenericEvent` parameter type** for event handler methods (`getMenu`, `getRequirements`, `getSettings`):
   ```php
   public function testGetMenuAcceptsGenericEvent(): void
   {
       $method = $this->reflection->getMethod('getMenu');
       $params = $method->getParameters();
       $this->assertCount(1, $params);
       $this->assertSame('event', $params[0]->getName());
       $type = $params[0]->getType();
       $this->assertNotNull($type);
       $this->assertSame(GenericEvent::class, $type->getName());
   }
   ```

7. **Stub loader with anonymous class** when testing `getRequirements()`:
   ```php
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
   // assert on $loader->requirements
   ```
   Verify count matches expected `add_requirement` calls in `src/Plugin.php` before asserting `assertCount()`.

8. **Assert `getHooks()` return format** for each hook entry (event name is string, handler is array of 2):
   ```php
   foreach ($hooks as $eventName => $handler) {
       $this->assertIsString($eventName);
       $this->assertIsArray($handler);
       $this->assertCount(2, $handler);
   }
   ```

9. **Required imports** at top of `tests/PluginTest.php` (alphabetical order):
   ```php
   use Detain\MyAdminHotjar\Plugin;
   use PHPUnit\Framework\TestCase;
   use ReflectionClass;
   use Symfony\Component\EventDispatcher\GenericEvent;
   ```

## Examples

**User says:** "Add a test that verifies getRequirements registers the class.Hotjar requirement"

**Actions taken:**
1. Read `src/Plugin.php` — confirm `$loader->add_requirement('class.Hotjar', ...)` is called
2. Add to `tests/PluginTest.php`:
```php
public function testGetRequirementsRegistersHotjarClass(): void
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
    $this->assertContains('class.Hotjar', $names);
}
```
3. Run `vendor/bin/phpunit tests/ -v` — confirm green

**Result:** Single-assertion test that stubs the loader with an anonymous class and checks `add_requirement` was called with `'class.Hotjar'`.

## Common Issues

- **"Class not found" on `ReflectionClass`:** Ensure `vendor/bin/phpunit` is used with the `phpunit.xml.dist` config — verify bootstrap is loading the autoloader correctly.
- **`$type->getName()` call on null:** `getType()` returns `null` for untyped parameters. Always assert `$this->assertNotNull($type)` before calling `$type->getName()`.
- **`assertCount()` fails on requirements:** Count calls in `src/Plugin.php` — the expected number is the literal count of `$loader->add_requirement(...)` lines. Re-read the file if the count changed.
- **Test passes locally but fails in CI:** Scrutinizer runs with `use_tabs: true`. Mixed indentation causes parse errors. Use tabs throughout `tests/PluginTest.php`.
- **`isStatic()` returns false unexpectedly:** The method or property may not actually be declared `static` in `src/Plugin.php`. Verify with `grep -n 'public static' src/Plugin.php` before writing the assertion.

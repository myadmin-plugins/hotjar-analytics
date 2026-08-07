<?php

declare(strict_types=1);

namespace Detain\MyAdminHotjar\Tests;

use Detain\MyAdminHotjar\Plugin;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Tests for the Hotjar Analytics Plugin class.
 */
class PluginTest extends TestCase
{
    /**
     * @var ReflectionClass<Plugin>
     */
    private ReflectionClass $reflection;

    protected function setUp(): void
    {
        $this->reflection = new ReflectionClass(Plugin::class);
    }

    /**
     * Tests that the Plugin class can be instantiated.
     */
    public function testCanBeInstantiated(): void
    {
        $plugin = new Plugin();
        $this->assertInstanceOf(Plugin::class, $plugin);
    }

    /**
     * Tests that the $name static property is set correctly.
     */
    public function testNamePropertyValue(): void
    {
        $this->assertSame('Hotjar Plugin', Plugin::$name);
    }

    /**
     * Tests that the $description static property is set correctly.
     */
    public function testDescriptionPropertyValue(): void
    {
        $this->assertSame('Allows handling of Hotjar based Analytics', Plugin::$description);
    }

    /**
     * Tests that the $help static property is an empty string.
     */
    public function testHelpPropertyValue(): void
    {
        $this->assertSame('', Plugin::$help);
    }

    /**
     * Tests that the $type static property is set to 'plugin'.
     */
    public function testTypePropertyValue(): void
    {
        $this->assertSame('plugin', Plugin::$type);
    }

    /**
     * Tests that $name is a public static property.
     */
    public function testNamePropertyIsPublicStatic(): void
    {
        $property = $this->reflection->getProperty('name');
        $this->assertTrue($property->isPublic());
        $this->assertTrue($property->isStatic());
    }

    /**
     * Tests that $description is a public static property.
     */
    public function testDescriptionPropertyIsPublicStatic(): void
    {
        $property = $this->reflection->getProperty('description');
        $this->assertTrue($property->isPublic());
        $this->assertTrue($property->isStatic());
    }

    /**
     * Tests that $help is a public static property.
     */
    public function testHelpPropertyIsPublicStatic(): void
    {
        $property = $this->reflection->getProperty('help');
        $this->assertTrue($property->isPublic());
        $this->assertTrue($property->isStatic());
    }

    /**
     * Tests that $type is a public static property.
     */
    public function testTypePropertyIsPublicStatic(): void
    {
        $property = $this->reflection->getProperty('type');
        $this->assertTrue($property->isPublic());
        $this->assertTrue($property->isStatic());
    }

    /**
     * Tests that the class has exactly four static properties.
     */
    public function testClassHasFourStaticProperties(): void
    {
        $staticProperties = array_filter(
            $this->reflection->getProperties(),
            static fn(\ReflectionProperty $p) => $p->isStatic()
        );
        $this->assertCount(4, $staticProperties);
    }

    /**
     * Tests that every hook registration in getHooks() names a real handler.
     *
     * Every registration in this plugin's getHooks() is currently commented out,
     * so nothing it declares reaches the event dispatcher. Asserting that the
     * returned array is empty would only bless that state; what is worth
     * asserting is that the registrations written in the method body - live or
     * commented - still name public static handlers on this class, so a rename
     * fails here instead of silently breaking whoever re-enables the lines.
     */
    public function testGetHooksRegistrationsNameLiveHandlers(): void
    {
        $method = $this->reflection->getMethod('getHooks');
        $lines = file((string) $method->getFileName());
        $this->assertNotFalse($lines, 'Plugin source should be readable');
        $body = implode('', array_slice(
            $lines,
            $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1
        ));

        preg_match_all(
            "/'([^']+)'\s*=>\s*\[\s*(?:__CLASS__|self::class|static::class|[A-Za-z_\\\\]+::class)\s*,\s*'([^']+)'\s*\]/",
            $body,
            $registrations,
            PREG_SET_ORDER
        );

        $this->assertNotEmpty(
            $registrations,
            'getHooks() should declare at least one "event.name" => [__CLASS__, "method"] registration'
        );

        foreach ($registrations as $registration) {
            [, $eventName, $handlerName] = $registration;

            $this->assertNotSame('', trim($eventName), 'Hook event names must not be empty');
            $this->assertTrue(
                $this->reflection->hasMethod($handlerName),
                "getHooks() registers '{$eventName}' => {$handlerName}() but that method does not exist on ".Plugin::class
            );

            $handler = $this->reflection->getMethod($handlerName);
            $this->assertTrue($handler->isPublic(), "Handler {$handlerName}() for '{$eventName}' must be public");
            $this->assertTrue($handler->isStatic(), "Handler {$handlerName}() for '{$eventName}' must be static");
        }
    }

    /**
     * Tests that getHooks is a public static method.
     */
    public function testGetHooksIsPublicStatic(): void
    {
        $method = $this->reflection->getMethod('getHooks');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
    }

    /**
     * Tests that getHooks has no required parameters.
     */
    public function testGetHooksHasNoParameters(): void
    {
        $method = $this->reflection->getMethod('getHooks');
        $this->assertSame(0, $method->getNumberOfRequiredParameters());
    }

    /**
     * Tests that getMenu is a public static method.
     */
    public function testGetMenuIsPublicStatic(): void
    {
        $method = $this->reflection->getMethod('getMenu');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
    }

    /**
     * Tests that getMenu accepts a GenericEvent parameter.
     */
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

    /**
     * Tests that getRequirements is a public static method.
     */
    public function testGetRequirementsIsPublicStatic(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
    }

    /**
     * Tests that getRequirements accepts a GenericEvent parameter.
     */
    public function testGetRequirementsAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getRequirements');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Tests that getSettings is a public static method.
     */
    public function testGetSettingsIsPublicStatic(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $this->assertTrue($method->isPublic());
        $this->assertTrue($method->isStatic());
    }

    /**
     * Tests that getSettings accepts a GenericEvent parameter.
     */
    public function testGetSettingsAcceptsGenericEvent(): void
    {
        $method = $this->reflection->getMethod('getSettings');
        $params = $method->getParameters();
        $this->assertCount(1, $params);
        $this->assertSame('event', $params[0]->getName());

        $type = $params[0]->getType();
        $this->assertNotNull($type);
        $this->assertSame(GenericEvent::class, $type->getName());
    }

    /**
     * Tests that the constructor has no required parameters.
     */
    public function testConstructorHasNoParameters(): void
    {
        $constructor = $this->reflection->getConstructor();
        $this->assertNotNull($constructor);
        $this->assertSame(0, $constructor->getNumberOfRequiredParameters());
    }

    /**
     * Tests that the class is not abstract.
     */
    public function testClassIsNotAbstract(): void
    {
        $this->assertFalse($this->reflection->isAbstract());
    }

    /**
     * Tests that the class is not final.
     */
    public function testClassIsNotFinal(): void
    {
        $this->assertFalse($this->reflection->isFinal());
    }

    /**
     * Tests that the class resides in the correct namespace.
     */
    public function testClassNamespace(): void
    {
        $this->assertSame('Detain\\MyAdminHotjar', $this->reflection->getNamespaceName());
    }

    /**
     * Tests that the class has the expected public methods.
     */
    public function testClassHasExpectedPublicMethods(): void
    {
        $expectedMethods = ['getHooks', 'getMenu', 'getRequirements', 'getSettings'];
        foreach ($expectedMethods as $methodName) {
            $this->assertTrue(
                $this->reflection->hasMethod($methodName),
                "Plugin class should have method: {$methodName}"
            );
        }
    }

    /**
     * Tests that getSettings retrieves the event subject without modification.
     */
    public function testGetSettingsRetrievesSubject(): void
    {
        $subject = new \stdClass();
        $event = new GenericEvent($subject);

        Plugin::getSettings($event);

        $this->assertSame($subject, $event->getSubject());
    }

    /**
     * Tests that every source getRequirements() registers is a file that exists.
     *
     * This replaces five tests (testGetRequirementsCallsAddRequirement,
     * testGetRequirementsRegistersHotjarClass, ...RegistersDeactivateKcare,
     * ...RegistersDeactivateAbuse and ...RegistersGetAbuseLicenses) that asserted
     * the registration table contained four specific names. They only ever read
     * the table back and never looked at the filesystem, so they stayed green for
     * years while every one of those registrations pointed at a file this package
     * has never shipped - a function_requirements() call on any of them would have
     * fataled. Asserting the source resolves to a real file is the property those
     * tests should have checked.
     *
     * getRequirements() registers nothing today, so the loop body does not run and
     * the test passes vacuously. That is the correct result for an empty table; the
     * closing assertion still executes, so the test is never assertion-free, and the
     * loop starts guarding the moment a registration is added back.
     */
    public function testGetRequirementsRegistersOnlySourcesThatExist(): void
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

        $packageRoot = dirname(__DIR__);
        $checked = [];

        foreach ($loader->requirements as [$name, $source]) {
            $this->assertNotSame('', trim($source), "Requirement '{$name}' registers an empty source path");

            // Sources are written relative to the host's INCLUDE_ROOT, e.g.
            // '/../vendor/detain/myadmin-hotjar-analytics/src/Foo.php'. Inside this
            // package the same file lives under the package root, so strip anything
            // up to and including the package directory before resolving.
            $relative = $source;
            $marker = '/myadmin-hotjar-analytics/';
            $position = strpos($relative, $marker);
            if ($position !== false) {
                $relative = substr($relative, $position + strlen($marker));
            }
            $resolved = $packageRoot.'/'.ltrim($relative, '/');

            $this->assertFileExists(
                $resolved,
                "getRequirements() registers '{$name}' => '{$source}' but no such file exists at {$resolved}"
            );

            $checked[] = $name;
        }

        $this->assertSame(
            array_column($loader->requirements, 0),
            $checked,
            'Every source registered by getRequirements() must be checked'
        );
    }

    /**
     * Tests that all static properties are of type string.
     */
    public function testAllStaticPropertiesAreStrings(): void
    {
        $this->assertIsString(Plugin::$name);
        $this->assertIsString(Plugin::$description);
        $this->assertIsString(Plugin::$help);
        $this->assertIsString(Plugin::$type);
    }

    /**
     * Tests that every hook getHooks() returns is actually dispatchable.
     *
     * The host copies each entry straight into a Symfony EventDispatcher
     * listener, so an entry only does something if its event name is a non-empty
     * string and its handler resolves to a public static method that PHP will
     * accept as a callable. The closing assertion proves the loop covered every
     * entry, so this test always asserts something even while the plugin's
     * registrations are commented out.
     */
    public function testGetHooksAreDispatchableCallables(): void
    {
        $hooks = Plugin::getHooks();
        $validated = [];

        foreach ($hooks as $eventName => $handler) {
            $this->assertIsString($eventName, 'Hook event names must be strings');
            $this->assertNotSame('', trim($eventName), 'Hook event names must not be empty');

            $this->assertIsArray($handler, "Handler for '{$eventName}' must be a [class, method] pair");
            $this->assertArrayHasKey(0, $handler, "Handler for '{$eventName}' is missing its class");
            $this->assertArrayHasKey(1, $handler, "Handler for '{$eventName}' is missing its method");
            $this->assertTrue(class_exists($handler[0]), "Handler class {$handler[0]} for '{$eventName}' does not exist");
            $this->assertTrue(
                method_exists($handler[0], $handler[1]),
                "Handler {$handler[0]}::{$handler[1]}() for '{$eventName}' does not exist"
            );

            $method = new \ReflectionMethod($handler[0], $handler[1]);
            $this->assertTrue($method->isPublic(), "Handler {$handler[1]}() for '{$eventName}' must be public");
            $this->assertTrue($method->isStatic(), "Handler {$handler[1]}() for '{$eventName}' must be static");
            $this->assertTrue(is_callable($handler), "Handler for '{$eventName}' must be callable as given");

            $validated[] = $eventName;
        }

        $this->assertSame(array_keys($hooks), $validated, 'Every hook returned by getHooks() must be validated');
    }

    /**
     * Tests that all event handler methods have a void return (no explicit return).
     */
    public function testEventHandlersReturnVoid(): void
    {
        $eventMethods = ['getMenu', 'getRequirements', 'getSettings'];
        foreach ($eventMethods as $methodName) {
            $method = $this->reflection->getMethod($methodName);
            $returnType = $method->getReturnType();
            $this->assertTrue(
                $returnType === null || $returnType->getName() === 'void',
                "{$methodName} should return void or have no return type"
            );
        }
    }

    /**
     * Tests that the Plugin class does not implement any interfaces.
     */
    public function testClassImplementsNoInterfaces(): void
    {
        $interfaces = $this->reflection->getInterfaces();
        $this->assertEmpty($interfaces);
    }

    /**
     * Tests that the Plugin class has no parent class.
     */
    public function testClassHasNoParent(): void
    {
        $this->assertFalse($this->reflection->getParentClass());
    }
}

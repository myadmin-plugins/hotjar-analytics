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
     * Tests that getHooks returns an array.
     */
    public function testGetHooksReturnsArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertIsArray($hooks);
    }

    /**
     * Tests that getHooks returns an empty array (all hooks are commented out).
     */
    public function testGetHooksReturnsEmptyArray(): void
    {
        $hooks = Plugin::getHooks();
        $this->assertEmpty($hooks);
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
     * Tests that getRequirements calls add_requirement on the loader subject.
     */
    public function testGetRequirementsCallsAddRequirement(): void
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

        $this->assertCount(4, $loader->requirements);
    }

    /**
     * Tests that getRequirements registers the class.Hotjar requirement.
     */
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

    /**
     * Tests that getRequirements registers the deactivate_kcare requirement.
     */
    public function testGetRequirementsRegistersDeactivateKcare(): void
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
        $this->assertContains('deactivate_kcare', $names);
    }

    /**
     * Tests that getRequirements registers the deactivate_abuse requirement.
     */
    public function testGetRequirementsRegistersDeactivateAbuse(): void
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
        $this->assertContains('deactivate_abuse', $names);
    }

    /**
     * Tests that getRequirements registers the get_abuse_licenses requirement.
     */
    public function testGetRequirementsRegistersGetAbuseLicenses(): void
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
        $this->assertContains('get_abuse_licenses', $names);
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
     * Tests that the getHooks return value uses class reference format when hooks are defined.
     */
    public function testGetHooksReturnFormat(): void
    {
        $hooks = Plugin::getHooks();
        foreach ($hooks as $eventName => $handler) {
            $this->assertIsString($eventName, 'Hook event name should be a string');
            $this->assertIsArray($handler, 'Hook handler should be an array');
            $this->assertCount(2, $handler, 'Hook handler should have class and method');
        }
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

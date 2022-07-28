<?php

namespace Detain\MyAdminHotjar;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminHotjar
 */
class Plugin
{
    public static $name = 'Hotjar Plugin';
    public static $description = 'Allows handling of Hotjar based Analytics';
    public static $help = '';
    public static $type = 'plugin';

    /**
     * Plugin constructor.
     */
    public function __construct()
    {
    }

    /**
     * @return array
     */
    public static function getHooks()
    {
        return [
            //'system.settings' => [__CLASS__, 'getSettings'],
            //'ui.menu' => [__CLASS__, 'getMenu'],
        ];
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getMenu(GenericEvent $event)
    {
        $menu = $event->getSubject();
        if ($GLOBALS['tf']->ima == 'admin') {
            function_requirements('has_acl');
            if (has_acl('client_billing')) {
            }
        }
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getRequirements(GenericEvent $event)
    {
        /**
         * @var \MyAdmin\Plugins\Loader $this->loader
         */
        $loader = $event->getSubject();
        $loader->add_requirement('class.Hotjar', '/../vendor/detain/myadmin-hotjar-analytics/src/Hotjar.php');
        $loader->add_requirement('deactivate_kcare', '/../vendor/detain/myadmin-hotjar-analytics/src/abuse.inc.php');
        $loader->add_requirement('deactivate_abuse', '/../vendor/detain/myadmin-hotjar-analytics/src/abuse.inc.php');
        $loader->add_requirement('get_abuse_licenses', '/../vendor/detain/myadmin-hotjar-analytics/src/abuse.inc.php');
    }

    /**
     * @param \Symfony\Component\EventDispatcher\GenericEvent $event
     */
    public static function getSettings(GenericEvent $event)
    {
        /**
         * @var \MyAdmin\Settings $settings
         **/
        $settings = $event->getSubject();
    }
}

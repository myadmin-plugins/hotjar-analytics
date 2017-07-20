<?php

namespace Detain\MyAdminHotjar;

use Symfony\Component\EventDispatcher\GenericEvent;

/**
 * Class Plugin
 *
 * @package Detain\MyAdminHotjar
 */
class Plugin {

	public static $name = 'Hotjar Plugin';
	public static $description = 'Allows handling of Hotjar emails and honeypots';
	public static $help = '';
	public static $type = 'plugin';

	/**
	 * Plugin constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return array
	 */
	public static function getHooks() {
		return [
			//'system.settings' => [__CLASS__, 'getSettings'],
			//'ui.menu' => [__CLASS__, 'getMenu'],
		];
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getMenu(GenericEvent $event) {
		$menu = $event->getSubject();
		if ($GLOBALS['tf']->ima == 'admin') {
			function_requirements('has_acl');
					if (has_acl('client_billing'))
							$menu->add_link('admin', 'choice=none.abuse_admin', '//my.interserver.net/bower_components/webhostinghub-glyphs-icons/icons/development-16/Black/icon-spam.png', 'Hotjar');
		}
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getRequirements(GenericEvent $event) {
		$loader = $event->getSubject();
		$loader->add_requirement('class.Hotjar', '/../vendor/detain/myadmin-hotjar-analytics/src/Hotjar.php');
		$loader->add_requirement('deactivate_kcare', '/../vendor/detain/myadmin-hotjar-analytics/src/abuse.inc.php');
		$loader->add_requirement('deactivate_abuse', '/../vendor/detain/myadmin-hotjar-analytics/src/abuse.inc.php');
		$loader->add_requirement('get_abuse_licenses', '/../vendor/detain/myadmin-hotjar-analytics/src/abuse.inc.php');
	}

	/**
	 * @param \Symfony\Component\EventDispatcher\GenericEvent $event
	 */
	public static function getSettings(GenericEvent $event) {
		$settings = $event->getSubject();
		$settings->add_text_setting('General', 'Hotjar', 'abuse_imap_user', 'Hotjar IMAP User:', 'Hotjar IMAP Username', ABUSE_IMAP_USER);
		$settings->add_text_setting('General', 'Hotjar', 'abuse_imap_pass', 'Hotjar IMAP Pass:', 'Hotjar IMAP Password', ABUSE_IMAP_PASS);
	}

}

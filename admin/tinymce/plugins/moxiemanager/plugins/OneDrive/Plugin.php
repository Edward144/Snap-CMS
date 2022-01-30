<?php
/**
 * Plugin.php
 *
 * Copyright 2003-2013, Moxiecode Systems AB, All rights reserved.
 */

/**
 * ...
 */
class MOXMAN_OneDrive_Plugin implements MOXMAN_IPlugin, MOXMAN_Http_IHandler {
	public function init() {
		MOXMAN::getPluginManager()->get("core")->bind("AuthInfo", "onAuthInfo", $this);
	}

	/**
	 * Process a request using the specified context.
	 *
	 * @param MOXMAN_Http_Context $httpContext Context instance to pass to use for the handler.
	 */
	public function processRequest(MOXMAN_Http_Context $httpContext) {
		$request = $httpContext->getRequest();
		$response = $httpContext->getResponse();

		if ($request->get("action") == "onedrive") {
			$response->sendContent(
				'<!DOCTYPE html>' .
				'<html>' .
				'<body>' .
					'<script src="//js.live.net/v5.0/wl.js"></script>' .
					'<script>window.close();</script>' .
				'</body>' .
				'</html>'
			);
		}
	}

	public function onAuthInfo($args) {
		$args->put("onedrive.client_id", MOXMAN::getConfig()->get("onedrive.client_id"));
	}
}

MOXMAN::getPluginManager()->add("onedrive", new MOXMAN_OneDrive_Plugin());
?>
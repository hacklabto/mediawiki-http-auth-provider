<?php
/**
 * MediaWiki HTTP Basic Auth Session Provider
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 */

namespace MediaWiki\Session;

use Config;
use User;
use WebRequest;

/**
 * Session provider for HTTP Basic Auth
 */
class BasicAuthSessionProvider extends SessionProvider {
	/**
	 * If we find Basic authentication, we provide a basic Session object.
	 * Otherwise return null.
	 */
	public function provideSessionInfo( WebRequest $request ) {
		if ( !isset( $_SERVER['PHP_AUTH_USER'] ) ) {
			return null;
		}

		$name = $_SERVER['PHP_AUTH_USER'];

		return new SessionInfo(
			SessionInfo::MAX_PRIORITY,
			[
				'provider' => $this,
				'userInfo' => UserInfo::newFromName($name, true),
			],
		);
	}

	/**
	 * From SessionProvider documentation:
	 * "Indicate whether self::persistSession() can save arbitrary session IDs"
	 * 
	 * We authenticate upstream, so MediaWiki cannot give us a session ID.
	 */
	public function persistsSessionId() {
		return false;
	}

	/**
	 * We authenticate upstream, so MediaWiki cannot change user.
	 */
	public function canChangeUser() {
		return false;
	}

	/**
	 * This is a no-op: we authenticate upstream, so the upstream authenticator
	 * persists sessions.
	 */
	public function persistSession( SessionBackend $session, WebRequest $request ) {
	}

	/**
	 * This is a no-op. From SessionProvider documentation:
	 *
	 * "A backend that cannot persist sesison ID or user info should implement
	 * this as a no-op."
	 */
	public function unpersistSession( WebRequest $request ) {
	}

	/**
	 * This is a no-op: we authenticate upstream, so if we want to prevent
	 * sessions for the user, we disable the user in the upstream authenticator.
	 */
	public function preventSessionsForUser( $username ) {
	}
}

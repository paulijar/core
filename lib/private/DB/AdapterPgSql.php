<?php
/**
 * @author Bart Visscher <bartv@thisnet.nl>
 * @author Morris Jobke <hey@morrisjobke.de>
 *
 * @copyright Copyright (c) 2017, ownCloud GmbH
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 *
 */


namespace OC\DB;

class AdapterPgSql extends Adapter {
	public function lastInsertId($table) {
		return $this->conn->fetchColumn('SELECT lastval()');
	}

	const UNIX_TIMESTAMP_REPLACEMENT = 'cast(extract(epoch from current_timestamp) as integer)';
	public function fixupStatement($statement) {
		$statement = str_replace( '`', '"', $statement );
		$statement = str_ireplace( 'UNIX_TIMESTAMP()', self::UNIX_TIMESTAMP_REPLACEMENT, $statement );
		// BIGSERIAL could not be used in statements altering column type
		// see https://github.com/owncloud/core/pull/28364#issuecomment-315006853
		$statement = preg_replace('|(ALTER [^s]+ TYPE )(BIGSERIAL)|i', '\1BIGINT', $statement);
		return $statement;
	}
}

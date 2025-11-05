-- ========================================================================
-- Copyright (C) 2012-2017      Noé Cendrier  <noe.cendrier@altairis.fr>
--
-- This program is free software; you can redistribute it and/or modify
-- it under the terms of the GNU General Public License as published by
-- the Free Software Foundation; either version 2 of the License, or
-- (at your option) any later version.
--
-- This program is distributed in the hope that it will be useful,
-- but WITHOUT ANY WARRANTY; without even the implied warranty of
-- MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
-- GNU General Public License for more details.
--
-- You should have received a copy of the GNU General Public License
-- along with this program. If not, see <http://www.gnu.org/licenses/>.
--
-- ========================================================================

CREATE TABLE llx_gestion_flotte_alerte
(
    rowid                                       integer AUTO_INCREMENT PRIMARY KEY,
    nom                                         VARCHAR(25),
    valeur                                      VARCHAR(50),
    email                                       VARCHAR(50),
    sujet                                       VARCHAR(255),
    msg                                         TEXT,
    fk_user                                     integer, --Utilisateur qui à modifier la valeur
    date_creation                               timestamp DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)ENGINE=innodb;  
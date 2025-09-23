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

CREATE TABLE llx_vehicule
(
  rowid                     integer AUTO_INCREMENT PRIMARY KEY,
  reference_interne         VARCHAR(100) NOT NULL,        -- numéro d'identification interne
  numero_chassis            VARCHAR(100) UNIQUE NOT NULL, -- numéro de châssis
  code_immobilisation       VARCHAR(100) NULL,
  type_vehicule             integer NOT NULL,
  plaque_immatriculation    VARCHAR(50) NULL,
  fk_user_creation          integer, -- identifiant de l'utilisateur qui l'a créé
  fk_user_modif             integer, -- indentifiant du dernier utilisateur qui l'a modifié
  date_creation             DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at                DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                            UNIQUE KEY ux_internal_ref (internal_ref)
)ENGINE=innodb;  
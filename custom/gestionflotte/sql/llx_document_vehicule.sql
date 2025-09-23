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

CREATE TABLE llx_document_vehicule
(
  rowid                     integer AUTO_INCREMENT PRIMARY KEY,
  fk_vehicule               INT NOT NULL,
  nom_fichier               VARCHAR(255) NOT NULL,   -- nom fichier
  chemin_fichier            VARCHAR(1024) NULL,      -- chemin de stockage sur serveur
  type_document             integer,       -- ex: assurance, carte_grise, contrôle_tech
  fk_user_creation          integer,
  date_creation             DATETIME DEFAULT CURRENT_TIMESTAMP,
  date_renouvelle           DATE, -- Date debut de validité du document
  date_expiration           DATE, -- date d'expiration du document
  note                      TEXT NULL,
  FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE CASCADE

)ENGINE=innodb;  
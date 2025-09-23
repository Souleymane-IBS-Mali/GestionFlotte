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

CREATE TABLE llx_incident_vehicule
(
  rowid                     integer AUTO_INCREMENT PRIMARY KEY,
  fk_vehicule               integer NOT NULL,
  date_incident             DATE NOT NULL,
  commentaire               TEXT,
  gravite                   ENUM('faible','moyenne','élevée') DEFAULT 'faible', -- Niveau de gravité
  numero_rapport            VARCHAR(100), -- Numéro du rapport d’incident (police, assurance…)
  fk_user_creation          integer,
  date_creation             DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (fk_vehicule) REFERENCES vehicles(rowid) ON DELETE CASCADE

)ENGINE=innodb;  
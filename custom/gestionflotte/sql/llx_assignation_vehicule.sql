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

CREATE TABLE llx_assignation_vehicule
(
  rowid             INT AUTO_INCREMENT PRIMARY KEY,
    fk_vehicule       INT NOT NULL, -- identifiant du véhicule à assigner
    fk_user           INT NOT NULL, -- identifiant de l'utilisateur à qui le véhicule est assigné
    date_debut        DATE NOT NULL,
    date_fin          DATE,
    etat_vehicule     TEXT NOT NULL,
    document          VARCHAR(100),
    equipement        VARCHAR(50),
    fk_user_creation  INT,           -- identifiant de l'utilisateur qui a créé l'assignation
    note              TEXT NULL,
    date_creation     DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_assign_vehicule FOREIGN KEY (fk_vehicule) REFERENCES llx_vehicule(rowid) ON DELETE CASCADE,
    CONSTRAINT fk_assign_user FOREIGN KEY (fk_user) REFERENCES llx_user(rowid)
)ENGINE=innodb;  
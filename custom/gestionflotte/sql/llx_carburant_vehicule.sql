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

CREATE TABLE llx_carburant_vehicule
(
    rowid                       integer AUTO_INCREMENT PRIMARY KEY,
    libelle                     VARCHAR(100),
    fk_vehicule                 integer NOT NULL,   -- identifiant du véhicule à assigner
    quantite                    FLOAT,
    cout                        FLOAT NOT NULL,
    kilometre                   FLOAT NOT NULL,
    date_demande                DATE DEFAULT CURRENT_TIMESTAMP,
    soumis                      INT DEFAULT 0,
    valider                     INT DEFAULT 0,
    rejeter                     INT DEFAULT 0,
    approuver                   INT DEFAULT 0,
    fk_user_valider_rejeter     INT,
    fk_user_approuver           INT,
    fk_user_creation            integer,            -- identifiant de l'utilisateur qui a créé l'assignation
    date_creation               DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_carburant_vehicule FOREIGN KEY (fk_vehicule) REFERENCES llx_vehicule(rowid) ON DELETE CASCADE,
    CONSTRAINT fk_carburant_user FOREIGN KEY (fk_user_creation) REFERENCES llx_user(rowid)
) ENGINE=InnoDB;
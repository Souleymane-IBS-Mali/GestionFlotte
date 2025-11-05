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
  rowid                   INT AUTO_INCREMENT PRIMARY KEY,
  nom                     VARCHAR(100),
  reference_interne       VARCHAR(100) NOT NULL,        -- numéro d'identification interne
  numero_chassis          VARCHAR(100) NOT NULL UNIQUE, -- numéro de châssis (VIN)
  code_immobilisation     VARCHAR(100) NOT NULL,
  fk_type_vehicule        INT NOT NULL,                 -- FK vers une table de type véhicule (si existe)
  plaque_immatriculation  VARCHAR(50) NOT NULL,
  date_acquisition        DATE,
  marque                  VARCHAR(50),
  modele                  VARCHAR(50),
  fk_type_carburant       INT NOT NULL, --le type de carburant que consomme un véhicule
  panne                   INT DEFAULT 0, -- 1= le véhicule est en panne
  img                     VARCHAR(100), -- Image du vehicule
  note                    VARCHAR(255),
  kilometrage             VARCHAR(15),
  fk_stationnement        integer,
  kilometrage_calcule     VARCHAR(15) DEFAULT,
  actif                   TINYINT(1) DEFAULT 1,         -- 1 = utilisable, 0 = hors service
  fk_user_creation        INT,                          -- identifiant de l'utilisateur créateur
  fk_user_modif           INT,                          -- identifiant du dernier modificateur
  date_creation           DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at              DATETIME DEFAULT CURRENT_TIMESTAMP 
                        ON UPDATE CURRENT_TIMESTAMP,

  UNIQUE KEY ux_reference_interne (reference_interne),

  CONSTRAINT fk_vehicule_user_create FOREIGN KEY (fk_user_creation) REFERENCES llx_user(rowid),
  CONSTRAINT fk_vehicule_user_modif  FOREIGN KEY (fk_user_modif) REFERENCES llx_user(rowid)
) ENGINE=InnoDB;

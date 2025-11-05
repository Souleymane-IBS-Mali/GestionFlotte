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
CREATE TABLE llx_image_vehicule
(
  rowid               INT AUTO_INCREMENT PRIMARY KEY,
  fk_vehicule         INT NOT NULL,                        -- Référence vers le véhicule
  fk_user_creation    INT NOT NULL,                                 -- Utilisateur ayant créé le document
  img                 VARCHAR(255) NOT NULL,               -- Nom du fichier
  date_obtention      DATE,                                -- Date d’obtention de l'image
  commentaire         TEXT,                                --Description de l'image
  date_creation       DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Date de création

  -- 🔐 Contraintes
  CONSTRAINT fk_image_vehicule
    FOREIGN KEY (fk_vehicule)
    REFERENCES llx_vehicule(rowid)
    ON DELETE CASCADE
) ENGINE=InnoDB;

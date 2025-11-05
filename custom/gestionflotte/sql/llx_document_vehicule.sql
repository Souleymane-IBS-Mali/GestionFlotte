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
  rowid               INT AUTO_INCREMENT PRIMARY KEY,
  fk_vehicule         INT NOT NULL,                        -- Référence vers le véhicule
  fk_type_vehicule    INT NOT NULL,                        -- Type de véhicule (optionnel, mais souvent utile)
  fk_type_document    INT NOT NULL,                        -- Type de document : assurance, carte grise, etc.
  tarif               float NOT NULL,
  fk_user_creation    INT NOT NULL,                                 -- Utilisateur ayant créé le document
  nom_fichier         VARCHAR(255) NOT NULL,               -- Nom du fichier
  note                TEXT,
  date_creation       DATETIME DEFAULT CURRENT_TIMESTAMP,  -- Date de création
  date_obtention      DATE,                                -- Date d’obtention du document
  date_debut          DATE,                                -- Date de validation (si applicable)
  date_expiration     DATE,                                -- Date d’expiration

  -- 🔐 Contraintes
  CONSTRAINT fk_document_vehicule_vehicule
    FOREIGN KEY (fk_vehicule)
    REFERENCES llx_vehicule(rowid)
    ON DELETE CASCADE,

  CONSTRAINT fk_document_vehicule_typedocument
    FOREIGN KEY (fk_type_document)
    REFERENCES llx_type_document(rowid)
    ON DELETE CASCADE

) ENGINE=InnoDB;

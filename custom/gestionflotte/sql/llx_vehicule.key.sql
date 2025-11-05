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
--ALTER TABLE llx_vehicule DROP COLUMN nom_colonne;
ALTER TABLE llx_vehicule ADD kilometrage_calcule VARCHAR(15);
ALTER TABLE llx_vehicule ADD fk_stationnement integer;

--DROP TABLE llx_assignation_vehicule;
--DROP TABLE llx_carburant_vehicule;
--DROP TABLE llx_document_vehicule;
--DROP TABLE llx_maintenance_vehicule;
--DROP TABLE llx_document_vehicule;
--DROP TABLE llx_incident_vehicule;
--DROP TABLE llx_image_vehicule;
--DROP TABLE llx_vehicule;
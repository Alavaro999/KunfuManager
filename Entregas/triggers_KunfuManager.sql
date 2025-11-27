-- KunfuManager: script de creación (MySQL)
DELIMITER $$

-- 1 Cuando se aprueba/rechaza una solicitud de inventario actualizar inventario
CREATE TRIGGER trg_solicitudes_inventario
AFTER UPDATE ON solicitudes_inventario
FOR EACH ROW
BEGIN
-- Si pasa de no aprobado a aprobado: restar cantidad
IF OLD.estado <> 'aprobado' AND NEW.estado = 'aprobado' THEN
UPDATE inventario
SET cantidad_disponible = GREATEST(0, cantidad_disponible - NEW.cantidad)
WHERE id_item = NEW.id_item;
END IF;

END$$
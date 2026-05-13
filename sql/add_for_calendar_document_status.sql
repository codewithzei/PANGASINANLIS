-- Run on existing databases that do not yet have "For Calendar".
INSERT INTO `document_statuses` (`document_status_name`, `status`, `is_deleted`, `created_at`)
SELECT 'For Calendar', 'active', 0, CURRENT_TIMESTAMP
FROM (SELECT 1 AS `_`) AS `_row`
WHERE NOT EXISTS (
    SELECT 1 FROM `document_statuses` WHERE `document_status_name` = 'For Calendar' AND COALESCE(`is_deleted`, 0) = 0
);

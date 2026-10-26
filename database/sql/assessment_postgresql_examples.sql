-- Dream Pawn Shop: PostgreSQL-only SQL examples for assessment practice.
-- IMPORTANT:
-- 1. This file is intentionally NOT a Laravel migration and is not run automatically.
-- 2. Run only against a disposable/dev PostgreSQL database first.
-- 3. The procedure below changes a real category only when explicitly called.
-- 4. Transaction examples use a temporary table and do not modify application data.

-- Stored procedure: archive a category without physically deleting it.
CREATE OR REPLACE PROCEDURE archive_category(p_category_id BIGINT)
LANGUAGE plpgsql
AS $$
BEGIN
    UPDATE categories
       SET is_archived = TRUE,
           updated_at = CURRENT_TIMESTAMP
     WHERE id = p_category_id
       AND is_archived = FALSE;

    IF NOT FOUND THEN
        RAISE NOTICE 'Category % was not found or is already archived.', p_category_id;
    END IF;
END;
$$;

-- To call this procedure deliberately in a development database:
-- CALL archive_category(1);

-- SQL function: calculate a fine in complete 24-hour overdue blocks.
-- This is an educational example; confirm the project fine policy before using it in production.
CREATE OR REPLACE FUNCTION calculate_late_fine(
    p_due_at TIMESTAMP,
    p_returned_at TIMESTAMP,
    p_rate_per_24_hours NUMERIC DEFAULT 10000
)
RETURNS NUMERIC
LANGUAGE plpgsql
IMMUTABLE
AS $$
DECLARE
    overdue_seconds BIGINT;
    overdue_blocks BIGINT;
BEGIN
    IF p_due_at IS NULL OR p_returned_at IS NULL OR p_returned_at <= p_due_at THEN
        RETURN 0;
    END IF;

    overdue_seconds := EXTRACT(EPOCH FROM (p_returned_at - p_due_at));
    overdue_blocks := CEIL(overdue_seconds / 86400.0);

    RETURN overdue_blocks * p_rate_per_24_hours;
END;
$$;

-- Example read-only call:
-- SELECT calculate_late_fine(
--     TIMESTAMP '2026-01-01 13:00:00',
--     TIMESTAMP '2026-01-02 13:00:01'
-- ); -- 20000, because the overdue period has entered a second 24-hour block.

-- Trigger function and trigger example. Use a disposable database before applying
-- this to an existing table; the application schema may already have this trigger.
CREATE OR REPLACE FUNCTION assessment_set_updated_at()
RETURNS TRIGGER
LANGUAGE plpgsql
AS $$
BEGIN
    NEW.updated_at := CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$;

-- Example trigger on categories; replace only if you intend to use this exact trigger.
-- DROP TRIGGER IF EXISTS assessment_categories_updated_at ON categories;
-- CREATE TRIGGER assessment_categories_updated_at
-- BEFORE UPDATE ON categories
-- FOR EACH ROW
-- EXECUTE FUNCTION assessment_set_updated_at();

-- COMMIT example: writes only to a temporary table for safe demonstration.
BEGIN;
CREATE TEMPORARY TABLE assessment_transaction_demo (
    id INTEGER PRIMARY KEY,
    note TEXT NOT NULL
) ON COMMIT PRESERVE ROWS;
INSERT INTO assessment_transaction_demo (id, note) VALUES (1, 'This row is committed');
COMMIT;

-- ROLLBACK example: the second row should not persist after ROLLBACK.
BEGIN;
INSERT INTO assessment_transaction_demo (id, note) VALUES (2, 'This row is rolled back');
ROLLBACK;

-- Verify the transaction result: only id = 1 should be returned.
SELECT id, note FROM assessment_transaction_demo ORDER BY id;
DROP TABLE assessment_transaction_demo;

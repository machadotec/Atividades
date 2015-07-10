CREATE TABLE system_changelog (
    id INTEGER PRIMARY KEY NOT NULL,
    logdate timestamp,
    login TEXT,
    tablename TEXT,
    primarykey TEXT,
    pkvalue TEXT,
    operation TEXT,
    columnname TEXT,
    oldvalue TEXT,
    newvalue TEXT);
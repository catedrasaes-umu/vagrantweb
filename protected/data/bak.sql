
CREATE TABLE cached_data_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    node_name VARCHAR(128) NOT NULL,
    node_status INTEGER NOT NULL DEFAULT FALSE,
    vm_name VARCHAR(128) NOT NULL,
    provider VARCHAR(128) NOT NULL,
    status VARCHAR(128) NOT NULL,
    expiration INTEGER
);





CREATE TABLE node_table (
    node_name VARCHAR(128) PRIMARY KEY NOT NULL,
    node_address VARCHAR(128) NOT NULL,
    node_port INTEGER NOT NULL,
    node_password VARCHAR(64) NOT NULL
);

CREATE TABLE cached_data_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT, 
    node_name VARCHAR(128) NOT NULL,
    node_status INTEGER NOT NULL DEFAULT FALSE,
    vm_name VARCHAR(128) NOT NULL,
    provider VARCHAR(128) NOT NULL,
    status VARCHAR(128) NOT NULL,
    expiration INTEGER
);

CREATE TABLE operation_table (
	    id INTEGER PRIMARY KEY NOT NULL,
	    operation_id INTEGER NOT NULL,
	    operation_command VARCHAR(255) NOT NULL,
	    node_name VARCHAR(128) NOT NULL,
	    operation_status INTEGER NOT NULL DEFAULT 100,
	    operation_result VARCHAR(255) DEFAULT "NotSet"
);


/*
MD5 generates a 128-bit hash value. You can use CHAR(32) or BINARY(16)
SHA-1 generates a 160-bit hash value. You can use CHAR(40) or BINARY(20)
SHA-224 generates a 224-bit hash value. You can use CHAR(56) or BINARY(28)
SHA-256 generates a 256-bit hash value. You can use CHAR(64) or BINARY(32)
SHA-384 generates a 384-bit hash value. You can use CHAR(96) or BINARY(48)
SHA-512 generates a 512-bit hash value. You can use CHAR(128) or BINARY(64)
*/

INSERT INTO node_table (node_name, node_address, node_port,node_password) VALUES ('Nodo2', 'casacloud.inf.um.es', 3333,'lalala');
/*INSERT INTO node_table (node_name, node_address, node_port,node_password) VALUES ('Nodo3', 'casanodo2.inf.um.es', 3333,'lalala');
INSERT INTO node_table (node_name, node_address, node_port,node_password) VALUES ('Nodo4', '128.0.0.1', 3333,'lalala');*/
INSERT INTO node_table (node_name, node_address, node_port,node_password) VALUES ('Nodo1', 'casanodo2.inf.um.es', 3333,'lalala');
/*INSERT INTO node_table (node_name, node_address, node_port,node_password) VALUES ('Nodo5', '127.0.0.1', 3333,'lalala');*/



/*INSERT INTO node_cached_data (node_name, vmname, provider, status) VALUES ('fran', 'pruebavm', 'pruebaprovider','running');*/




PRAGMA foreign_keys=true;

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
    expiration INTEGER,
    priority INTEGER DEFAULT 0    
);

CREATE TABLE operation_table (
    id INTEGER PRIMARY KEY NOT NULL,
    operation_id INTEGER NOT NULL,
    operation_command VARCHAR(255) NOT NULL,
    operation_specific VARCHAR(255) NOT NULL DEFAULT '',        
    node_name VARCHAR(128) NOT NULL,
    operation_status INTEGER NOT NULL DEFAULT 100,
    operation_result TEXT DEFAULT "NotSet",
    operation_timestamp TEXT NOT NULL,
    username VARCHAR(128) NOT NULL
);

CREATE TABLE project_table (
    id INTEGER PRIMARY KEY NOT NULL,
    project_name VARCHAR(255) NOT NULL
);

CREATE TABLE project_node_machine_table(
    id INTEGER PRIMARY KEY NOT NULL,
    project_id INTEGER REFERENCES project_table(id) ON UPDATE CASCADE ON DELETE CASCADE,
    node_name VARCHAR(128) NOT NULL REFERENCES node_table(node_name) ON UPDATE CASCADE ON DELETE CASCADE,
    machine_name VARCHAR(255) NOT NULL,
    priority INTEGER DEFAULT 0
    --FOREIGN KEY(project_id) REFERENCES project_table(id) ON DELETE CASCADE,
    --FOREIGN KEY(node_name) REFERENCES node_table(node_name) ON UPDATE SET
);


CREATE TABLE user_virtual_machine_table (
    id INTEGER PRIMARY KEY NOT NULL,
    node_name VARCHAR(128) NOT NULL REFERENCES node_table(node_name) ON UPDATE CASCADE ON DELETE CASCADE,
    machine_name VARCHAR(255) NOT NULL,
    --machine_uuid VARCHAR(255) NOT NULL,
    user_id INTEGER NOT NULL,    
    FOREIGN KEY(user_id) REFERENCES Users(id) ON DELETE CASCADE
);

CREATE TABLE launcher_table (
    id INTEGER PRIMARY KEY NOT NULL,
    project_id INTEGER NOT NULL,
    status INTEGER DEFAULT 0,
    FOREIGN KEY(project_id) REFERENCES project_table(id)
);

CREATE TABLE project_pending_operations_table (
    id INTEGER PRIMARY KEY NOT NULL,
    pnm_id INTEGER NOT NULL,    
    project_id INTEGER NOT NULL,
    command VARCHAR(255),
    status INTEGER DEFAULT -1,
    status_msg VARCHAR(255),
    operation_id INTEGER,
    operation_timestamp TEXT NOT NULL,
    username VARCHAR(128) NOT NULL,
    -- user_id INTEGER NOT NULL,
    FOREIGN KEY(pnm_id) REFERENCES project_node_machine_table(id) ON DELETE CASCADE,
    FOREIGN KEY(project_id) REFERENCES project_table(id) ON DELETE CASCADE
    -- FOREIGN KEY(user_id) REFERENCES users(id) ON DELETE CASCADE
    
);

CREATE TABLE Users (
    id INTEGER NOT NULL PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,
    --salt VARCHAR(128) NOT NULL,
    email VARCHAR(128) NOT NULL    
);


CREATE TABLE project_user_table (
    user_id INTEGER NOT NULL,
    project_id INTEGER NOT NULL,
    FOREIGN KEY(user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY(project_id) REFERENCES project_table(id) ON DELETE CASCADE
);


INSERT INTO AuthItem (name,type,description,data) VALUES("AdminRole",2,"Super Admin Role",'N');

UPDATE AuthItem SET description='Allow users to delete node boxes' WHERE name='Box.Delete';
UPDATE AuthItem SET description='Allow users to add boxes to nodes' WHERE name='Box.Add';
UPDATE AuthItem SET description='* Allow users to check project operation status ' WHERE name='Launcher.Checkidle';
UPDATE AuthItem SET description='* Allow users to execute project operations' WHERE name='Launcher.Execute';
UPDATE AuthItem SET description='Allow users to view node information' WHERE name='Node.View';
UPDATE AuthItem SET description='Allow users to create new nodes' WHERE name='Node.Create';
UPDATE AuthItem SET description='Allow users to update node configuration' WHERE name='Node.Update';
UPDATE AuthItem SET description='Allow user to delete nodes' WHERE name='Node.Delete';
UPDATE AuthItem SET description='* Allow users to list nodes and vms' WHERE name='Node.Index';
UPDATE AuthItem SET description='Allow users to manage nodes' WHERE name='Node.Admin';
UPDATE AuthItem SET description='Allow users to see node configuration' WHERE name='Node.Showconfig';
UPDATE AuthItem SET description='Allow users to edit node configuration' WHERE name='Node.Editconfig';
UPDATE AuthItem SET description='Allow users to change node password' WHERE name='Node.Passwordchange';
UPDATE AuthItem SET description='Allow users to upload a config file to node' WHERE name='Node.Uploadconfig';
UPDATE AuthItem SET description='* Allow users to check operation status' WHERE name='Operation.QueryOperations';
UPDATE AuthItem SET description='Allow users to view projects' WHERE name='Project.View';
UPDATE AuthItem SET description='Allow users to create projects' WHERE name='Project.Create';
UPDATE AuthItem SET description='Allow users to delete project pending operations' WHERE name='Project.Deleteallpending';
UPDATE AuthItem SET description='Allow users to delete completed project operations' WHERE name='Project.Deletecompleted';
UPDATE AuthItem SET description='Allow users to update project configuration' WHERE name='Project.Update';
UPDATE AuthItem SET description='Allow users to delete projects' WHERE name='Project.Delete';
UPDATE AuthItem SET description='Allow users to list projects' WHERE name='Project.Index';
UPDATE AuthItem SET description='Allow users to manage projects' WHERE name='Project.Admin';
UPDATE AuthItem SET description="Allow users to change priority in project's virtual machines" WHERE name='Project.Updatepriority';
UPDATE AuthItem SET description='Allow users to add virtual machines to projects' WHERE name='Project.Addmachine';
UPDATE AuthItem SET description='Allow users to delete project virtual machines' WHERE name='Project.Removemachine';
UPDATE AuthItem SET description='Allow users to execute bulk startup operations in projects' WHERE name='Project.Batchrun';
UPDATE AuthItem SET description='Allow users to execute bulk pause operations in projects' WHERE name='Project.Batchpause';
UPDATE AuthItem SET description='Allow users to execute bulk stop operations in projects' WHERE name='Project.Batchstop';
UPDATE AuthItem SET description='Allow users to delete operations in projects' WHERE name='Project.Deleteoperation';
UPDATE AuthItem SET description='* Allow users to logout' WHERE name='Site.Logout';

UPDATE AuthItem SET description="Allow users to view user's configuration" WHERE name='User.View';
UPDATE AuthItem SET description='Allow users to create new users' WHERE name='User.Create';
UPDATE AuthItem SET description='Allow users to update user information' WHERE name='User.Update';
UPDATE AuthItem SET description='Allow users to delete user' WHERE name='User.Delete';
UPDATE AuthItem SET description='Allow users to list users' WHERE name='User.Index';
UPDATE AuthItem SET description='Allow users to manage users' WHERE name='User.Admin';
UPDATE AuthItem SET description='* Allow users to execute commands in virtual machines ' WHERE name='Vm.Command';
UPDATE AuthItem SET description='Allow users to create new virtual machines' WHERE name='Vm.Create';
UPDATE AuthItem SET description='Allow users to destroy virtual machine' WHERE name='Vm.Destroy';
UPDATE AuthItem SET description='Allow users to delete virtual machine from config file and its data' WHERE name='Vm.Delete';
UPDATE AuthItem SET description='* Allow users to execute bulk startup operations to virtual machines' WHERE name='Vm.Batchrun';
UPDATE AuthItem SET description='* Allow users to execute bulk pause operations to virtual machines' WHERE name='Vm.Batchpause';
UPDATE AuthItem SET description='* Allow users to execute bulk stop operations to virtual machines' WHERE name='Vm.Batchstop';
UPDATE AuthItem SET description='Allow users to execute bulk snapshot operations to virtual machines' WHERE name='Vm.Batchsnapshot';
UPDATE AuthItem SET description='Allow users to take snapshots in virtual machines' WHERE name='Vm.TakeSnapshot';
UPDATE AuthItem SET description='Allow users to list virtual machine snapshots' WHERE name='Vm.SnapshotList';
UPDATE AuthItem SET description='Allow users to delete virtual machine snapshots ' WHERE name='Vm.Deletesnapshot';
UPDATE AuthItem SET description='Allow users to restore snapshots' WHERE name='Vm.Restoresnapshot';

UPDATE AuthItem SET description='Allow users to generate new virtual machines configs' WHERE name='Vm.Genconfig';
UPDATE AuthItem SET description='Allow users to delete roles' WHERE name='User.DeleteRole';
UPDATE AuthItem SET description='Allow users to create new roles' WHERE name='User.AddRole';
UPDATE AuthItem SET description='Allow users to access web index page' WHERE name='Site.Index';
UPDATE AuthItem SET description='Allow users to see errors' WHERE name='Site.Error';
UPDATE AuthItem SET description='Allow users to access contact page' WHERE name='Site.Contact';
UPDATE AuthItem SET description='Allow users to acces login page' WHERE name='Site.Login';
UPDATE AuthItem SET description='Allow users to list project users' WHERE name='Project.Users';
UPDATE AuthItem SET description='Allow users to manage users to projects' WHERE name='Project.Manageusers';
UPDATE AuthItem SET description='Allow users to delete users assigned to projects' WHERE name='Project.Deleteuser';
UPDATE AuthItem SET description='Allow users to assign users to projects' WHERE name='Project.Adduser';
UPDATE AuthItem SET description="Allow users to list node's operations" WHERE name='Node.Operations';
UPDATE AuthItem SET description="Allow users to remove node's operations" WHERE name='Operation.Clear';
UPDATE AuthItem SET description='Allow users to see last operations performed on a node' WHERE name='Operation.Last';
UPDATE AuthItem SET description='Allow users to access the control panel page' WHERE name='Site.Controlpanel';

UPDATE AuthItem SET description='Allow users to perform all operations related to boxes' WHERE name='Box.*';
UPDATE AuthItem SET description='Allow users to perform all operations related to project execution process' WHERE name='Launcher.*';
UPDATE AuthItem SET description='Allow users to perform all operations related to nodes' WHERE name='Node.*';
UPDATE AuthItem SET description='Allow users to perform all actions related to operations' WHERE name='Operation.*';
UPDATE AuthItem SET description='Allow users to perform all operations related to projects' WHERE name='Project.*';
UPDATE AuthItem SET description='Allow users to perform all operations related to the site' WHERE name='Site.*';
UPDATE AuthItem SET description='Allow users to perform all operations related to users' WHERE name='User.*';
UPDATE AuthItem SET description='Allow users to perform all operations related to virtual machines' WHERE name='Vm.*';

UPDATE AuthItem SET description='* Allow users to check if nodes are active' WHERE name='Node.Ping';
UPDATE AuthItem SET description='Allow users to delete all pending operations in projects' WHERE name='Project.Deleteall';
UPDATE AuthItem SET description='* Allow users to access the index web page' WHERE name='Site.Proxy';
UPDATE AuthItem SET description='Allow users to assign virtual machines to users' WHERE name='User.Addvm';
UPDATE AuthItem SET description='Allow users to deassign virtual machines from users' WHERE name='User.Removevm';
UPDATE AuthItem SET description='* Allow users to view virtual machines configuration' WHERE name='Vm.View';
UPDATE AuthItem SET description="* Allow users to view see node downloads's information" WHERE name='Node.Downloadsinfo';
UPDATE AuthItem SET description="Allow users to delete node downloads" WHERE name='Node.Deleteboxdownloads';







/*INSERT INTO users (username, password, email) VALUES ('demo','$2a$10$JTJf6/XqC94rrOtzuF397OHa4mbmZrVTBOQCmYD9U.obZRUut4BoC','webmaster@example.com');*/
/*INSERT INTO users (username, password, email) VALUES ('admin','$2a$13$cmJJge1eMqQKbGU9.26rJuM6QAeXfY0H6xpMylCKtO1TbguA2gb5.','webmaster@example.com');*/


/*
MD5 generates a 128-bit hash value. You can use CHAR(32) or BINARY(16)
SHA-1 generates a 160-bit hash value. You can use CHAR(40) or BINARY(20)
SHA-224 generates a 224-bit hash value. You can use CHAR(56) or BINARY(28)
SHA-256 generates a 256-bit hash value. You can use CHAR(64) or BINARY(32)
SHA-384 generates a 384-bit hash value. You can use CHAR(96) or BINARY(48)
SHA-512 generates a 512-bit hash value. You can use CHAR(128) or BINARY(64)
*/

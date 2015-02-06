DROP TABLE IF EXISTS `cached_data_table`;
CREATE TABLE `cached_data_table` (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT, 
    node_name VARCHAR(128) NOT NULL,
    node_status INTEGER NOT NULL DEFAULT FALSE,
    vm_name VARCHAR(128) NOT NULL,
    provider VARCHAR(128) NOT NULL,
    status VARCHAR(128) NOT NULL,
    expiration INTEGER,
    priority INTEGER DEFAULT 0    
);


DROP TABLE IF EXISTS `operation_table`;
CREATE TABLE `operation_table` (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    operation_id INTEGER NOT NULL,
    operation_command VARCHAR(255) NOT NULL,
    operation_specific VARCHAR(255) NOT NULL DEFAULT '',        
    node_name VARCHAR(128) NOT NULL,
    operation_status INTEGER NOT NULL DEFAULT 100,
    operation_result TEXT not null,
    operation_timestamp TEXT NOT NULL,
    username VARCHAR(128) NOT NULL
);


DELIMITER $$
create trigger operation_table_trigger before insert on `operation_table`
for each row
begin
   if (NEW.operation_result is null ) then
       set NEW.operation_result = 'NotSet';
   end if;
END$$



DROP TABLE IF EXISTS `project_node_machine_table`;
CREATE TABLE `project_node_machine_table`(
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    project_id INTEGER REFERENCES project_table(id) ON UPDATE CASCADE ON DELETE CASCADE,
    node_name VARCHAR(128) NOT NULL REFERENCES node_table(node_name) ON UPDATE CASCADE ON DELETE CASCADE,
    machine_name VARCHAR(255) NOT NULL,
    priority INTEGER DEFAULT 0   
);


DROP TABLE IF EXISTS `project_table`;
CREATE TABLE `project_table` (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    project_name VARCHAR(255) NOT NULL
);

#Creando tablas de permisos
DROP TABLE IF EXISTS `AuthItem`;
CREATE TABLE `AuthItem`
(
   name varchar(64) not null,
   type integer not null,
   description text,
   bizrule text,
   data text,
   primary key (name)
);
INSERT INTO `AuthItem` VALUES('AdminRole',2,'Admin Role',NULL,'N;');
INSERT INTO `AuthItem` VALUES('AuthenticatedRole',2,'Authenticated Role',NULL,'N;');
INSERT INTO `AuthItem` VALUES('ProjectManagerRole',2,'Group Manager Roles',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Guest',2,'usuario no autenticado',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Box.*',1,'Allow users to perform all operations related to boxes',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Launcher.*',1,'Allow users to perform all operations related to group execution process',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.*',1,'Allow users to perform all operations related to nodes',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Operation.*',1,'Allow users to perform all actions related to operations',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.*',1,'Allow users to perform all operations related to groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.*',1,'Allow users to perform all operations related to the site',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.*',1,'Allow users to perform all operations related to users',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.*',1,'Allow users to perform all operations related to virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Box.Delete',0,'Allow users to delete node boxes',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Box.Add',0,'Allow users to add boxes to nodes',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Launcher.Checkidle',0,'* Allow users to check group operation status ',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Launcher.Execute',0,'* Allow users to execute group operations',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.View',0,'Allow users to view node information',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Create',0,'Allow users to create new nodes',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Update',0,'Allow users to update node configuration',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Delete',0,'Allow user to delete nodes',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Index',0,'* Allow users to list nodes and vms',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Admin',0,'Allow users to manage nodes',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Showconfig',0,'Allow users to see node configuration',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Editconfig',0,'Allow users to edit node configuration',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Passwordchange',0,'Allow users to change node password',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Uploadconfig',0,'Allow users to upload a config file to node',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Export',0,'Allow users to export all node information',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Operation.QueryOperations',0,'* Allow users to check operation status',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.View',0,'Allow users to view groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Create',0,'Allow users to create groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Deleteallpending',0,'Allow users to delete group pending operations',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Deletecompleted',0,'Allow users to delete completed project operations',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Update',0,'Allow users to update group configuration',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Delete',0,'Allow users to delete groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Index',0,'Allow users to list groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Updatepriority',0,'Allow users to change priority in group''s virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Addmachine',0,'Allow users to add virtual machines to groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Removemachine',0,'Allow users to delete group virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Batchrun',0,'Allow users to execute bulk startup operations in groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Batchpause',0,'Allow users to execute bulk pause operations in groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Batchstop',0,'Allow users to execute bulk stop operations in groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Deleteoperation',0,'Allow users to delete operations in groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.Logout',0,'* Allow users to logout',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.View',0,'Allow users to view user''s configuration',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.Create',0,'Allow users to create new users',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.Update',0,'Allow users to update user information',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.Delete',0,'Allow users to delete user',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.Index',0,'Allow users to list users',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.Admin',0,'Allow users to manage users',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Command',0,'* Allow users to execute commands in virtual machines ',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Create',0,'Allow users to create new virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Destroy',0,'Allow users to destroy virtual machine',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Delete',0,'Allow users to delete virtual machine from config file and its data',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Batchrun',0,'* Allow users to execute bulk startup operations to virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Batchpause',0,'* Allow users to execute bulk pause operations to virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Batchstop',0,'* Allow users to execute bulk stop operations to virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Batchsnapshot',0,'Allow users to execute bulk snapshot operations to virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.TakeSnapshot',0,'Allow users to take snapshots in virtual machines',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.SnapshotList',0,'Allow users to list virtual machine snapshots',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Deletesnapshot',0,'Allow users to delete virtual machine snapshots ',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Restoresnapshot',0,'Allow users to restore snapshots',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.Genconfig',0,'Allow users to generate new virtual machines configs',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.DeleteRole',0,'Allow users to delete roles',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.AddRole',0,'Allow users to create new roles',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.Index',0,'Allow users to access web index page',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.Error',0,'Allow users to see errors',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.Contact',0,'Allow users to access contact page',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.Login',0,'Allow users to acces login page',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Users',0,'Allow users to list group users',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Manageusers',0,'Allow users to manage users to groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Deleteuser',0,'Allow users to delete users assigned to groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Adduser',0,'Allow users to assign users to groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Operations',0,'Allow users to list node''s operations',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Operation.Clear',0,'Allow users to remove node''s operations',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Operation.Last',0,'Allow users to see last operations performed on a node',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.Controlpanel',0,'Allow users to access the control panel page',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Downloadsinfo',0,'* Allow users to view see node downloads''s information',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Deleteboxdownloads',0,'Allow users to delete node downloads',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Node.Ping',0,'* Allow users to check if nodes are active',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Project.Deleteall',0,'Allow users to delete all pending operations in groups',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Site.Proxy',0,'* Allow users to access the index web page',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.Addvm',0,'Allow users to assign virtual machines to users',NULL,'N;');
INSERT INTO `AuthItem` VALUES('User.Removevm',0,'Allow users to deassign virtual machines from users',NULL,'N;');
INSERT INTO `AuthItem` VALUES('Vm.View',0,'* Allow users to view virtual machines configuration',NULL,'N;');


DROP TABLE IF EXISTS `AuthItemChild`;
CREATE TABLE `AuthItemChild`
(
   parent varchar(64) not null,
   child varchar(64) not null,
   primary key (parent,child),
   foreign key (parent) references AuthItem (name) on delete cascade on update cascade,
   foreign key (child) references AuthItem (name) on delete cascade on update cascade
);

INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Node.Downloadsinfo');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Node.Index');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Node.Operations');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Node.Ping');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Node.Showconfig');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Node.View');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Operation.*');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Operation.Last');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Operation.QueryOperations');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Project.Delete');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Site.Proxy');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Vm.Batchpause');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Vm.Batchrun');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Vm.Batchstop');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Vm.Command');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Vm.SnapshotList');
INSERT INTO `AuthItemChild` VALUES('AuthenticatedRole','Vm.View');
INSERT INTO `AuthItemChild` VALUES('Guest','Site.*');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Launcher.*');
-- INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Launcher.Checkidle');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Launcher.Execute');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Node.Index');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Node.Showconfig');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Operation.*');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Operation.QueryOperations');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Addmachine');
#INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Admin');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Batchpause');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Batchrun');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Batchstop');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Deleteallpending');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Deletecompleted');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Deleteoperation');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Deleteall');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Index');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Removemachine');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.Updatepriority');
INSERT INTO `AuthItemChild` VALUES('ProjectManagerRole','Project.View');

DROP TABLE IF EXISTS `AuthAssignment`;
CREATE TABLE `AuthAssignment`
(
   itemname varchar(64) not null,
   userid varchar(64) not null,
   bizrule text,
   data text,
   primary key (itemname,userid),
   foreign key (itemname) references AuthItem (name) on delete cascade on update cascade
);
INSERT INTO `AuthAssignment` VALUES('AdminRole','4',NULL,'N;');
INSERT INTO `AuthAssignment` VALUES('AuthenticatedRole','4',NULL,'N;');


DROP TABLE IF EXISTS `Rights`;
CREATE TABLE `Rights`
(
    itemname varchar(64) not null,
    type integer not null,
    weight integer not null,
    primary key (itemname),
    foreign key (itemname) references AuthItem (name) on delete cascade on update cascade
);



DROP TABLE IF EXISTS `Users`;
CREATE TABLE `Users` (
    id INTEGER NOT NULL PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(128) NOT NULL,
    password VARCHAR(128) NOT NULL,  
    email VARCHAR(128) NOT NULL    
);

INSERT INTO `Users` VALUES(4,'admin','$2a$10$olF98M6PBmYv0TxLb/sVP.IKZVmki/eOQz0eRFrhSp7HUpfFlLuJ6','');


DROP TABLE IF EXISTS `node_table`;
CREATE TABLE `node_table` (
    node_name VARCHAR(128) PRIMARY KEY NOT NULL,
    node_address VARCHAR(128) NOT NULL,
    node_port INTEGER NOT NULL DEFAULT 3333,
    node_password VARCHAR(64) NOT NULL
);





DROP TABLE IF EXISTS `user_virtual_machine_table`;
CREATE TABLE `user_virtual_machine_table` (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    node_name VARCHAR(128) NOT NULL REFERENCES node_table(node_name) ON UPDATE CASCADE ON DELETE CASCADE,
    machine_name VARCHAR(255) NOT NULL,
    user_id INTEGER NOT NULL,    
    FOREIGN KEY(user_id) REFERENCES Users(id) ON DELETE CASCADE
);

DROP TABLE IF EXISTS `launcher_table`;
CREATE TABLE `launcher_table` (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    project_id INTEGER NOT NULL,
    status INTEGER DEFAULT 0,
    FOREIGN KEY(project_id) REFERENCES project_table(id)
);

DROP TABLE IF EXISTS `project_pending_operations_table`;
CREATE TABLE `project_pending_operations_table` (
    id INTEGER PRIMARY KEY NOT NULL AUTO_INCREMENT,
    pnm_id INTEGER NOT NULL,    
    project_id INTEGER NOT NULL,
    command VARCHAR(255),
    status INTEGER DEFAULT -1,
    status_msg VARCHAR(255),
    operation_id INTEGER,
    operation_timestamp TEXT NOT NULL,
    username VARCHAR(128) NOT NULL,
    FOREIGN KEY(pnm_id) REFERENCES project_node_machine_table(id) ON DELETE CASCADE,
    FOREIGN KEY(project_id) REFERENCES project_table(id) ON DELETE CASCADE   
    
);


DROP TABLE IF EXISTS `project_user_table`;
CREATE TABLE `project_user_table` (
    user_id INTEGER NOT NULL,
    project_id INTEGER NOT NULL,
    FOREIGN KEY(user_id) REFERENCES Users(id) ON DELETE CASCADE,
    FOREIGN KEY(project_id) REFERENCES project_table(id) ON DELETE CASCADE
);
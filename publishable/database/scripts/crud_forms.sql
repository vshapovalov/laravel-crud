/*
SQLyog Ultimate v12.4.1 (32 bit)
MySQL - 5.7.18-log : Database - crud
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
/*Data for the table `crud_field_types` */

insert  into `crud_field_types`(`id`,`code`,`name`) values 
(1,'textbox','textbox'),
(2,'checkbox','checkbox'),
(3,'colorbox','colorbox'),
(4,'textarea','textarea'),
(5,'datepicker','datepicker'),
(6,'richedit','richedit'),
(7,'image','image');

/*Data for the table `crud_fields` */

insert  into `crud_fields`(`id`,`crud_form_id`,`name`,`caption`,`type`,`visibility`,`by_default`,`json`,`readonly`,`description`,`tab`,`validation`,`additional`,`crud_relation_id`,`created_at`,`updated_at`,`order`,`columns`) values 
(1,NULL,'meta->enabled','Статус','checkbox','[ \"browse\", \"edit\", \"add\" ]',NULL,1,0,NULL,'Основные параметры',NULL,NULL,1,'2018-01-13 06:05:24','2018-01-13 06:05:35',0,12),
(2,1,'roles','Роли','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Роли',NULL,NULL,10,'2018-01-13 06:05:44','2018-01-15 10:01:14',0,12),
(3,1,'name','ФИО','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-14 06:05:25','2018-01-15 10:01:14',0,12),
(4,NULL,'userType','тип 2','relation','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,3,'2018-01-14 06:10:56','2018-01-15 09:52:46',0,12),
(5,3,'code','code','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:41:33','2018-01-15 05:47:18',2,6),
(6,3,'name','name','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:41:45','2018-01-15 05:47:18',1,6),
(7,3,'model','model','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:41:56','2018-01-15 05:47:18',3,6),
(8,3,'id','id field','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:42:15','2018-01-15 05:47:18',4,6),
(9,3,'display','display field','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:42:28','2018-01-15 05:47:18',5,6),
(10,3,'type','type','dropdown','[ \"browse\", \"edit\", \"add\" ]','list',0,0,NULL,'Основные параметры','required','{\"mode\":\"single\", \"values\": [ {\"key\":\"list\", \"value\":\"list\"}, {\"key\":\"tree\", \"value\":\"tree\"} ]}',NULL,'2018-01-14 13:43:57','2018-01-15 05:47:19',6,6),
(11,3,'fields','fields','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Поля','required','{ \"buttons\": [ \"add\", \"delete_all\"]}',6,'2018-01-14 13:46:47','2018-01-15 05:47:18',7,12),
(12,3,'scopes','Scopes','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Scopes',NULL,NULL,5,'2018-01-14 13:47:14','2018-01-15 05:47:18',8,12),
(13,4,'name','Param name','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:48:16','2018-01-14 13:48:19',0,12),
(14,5,'name','name','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:49:10','2018-01-14 13:50:06',0,12),
(15,5,'params','Scope params','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,4,'2018-01-14 13:50:00','2018-01-14 13:50:06',0,12),
(16,6,'name','name','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:52:20','2018-01-16 06:24:17',1,6),
(17,6,'caption','caption','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-14 13:52:47','2018-01-16 06:24:18',2,6),
(18,6,'type','type','dropdown','[ \"browse\", \"edit\", \"add\" ]','textbox',0,0,NULL,'Основные параметры','required','{ \"mode\": \"single\", \"values\": [ {\"key\":\"textbox\", \"value\":\"textbox\"}, {\"key\":\"colorbox\", \"value\":\"colorbox\"}, {\"key\":\"checkbox\", \"value\":\"checkbox\"}, {\"key\":\"textarea\", \"value\":\"textarea\"}, {\"key\":\"datepicker\", \"value\":\"datepicker\"}, {\"key\":\"richedit\", \"value\":\"richedit\"}, {\"key\":\"image\", \"value\":\"image\"}, {\"key\":\"dynamic\", \"value\":\"dynamic\"}, {\"key\":\"relation\", \"value\":\"relation\"}, {\"key\":\"dropdown\", \"value\":\"dropdown\"} ]}',NULL,'2018-01-14 13:56:10','2018-01-16 06:24:18',3,6),
(19,7,'type','Тип','dropdown','[ \"browse\", \"edit\", \"add\" ]','belongsTo',0,0,NULL,'Основные параметры','required','{\"mode\":\"single\", \"values\": [ {\"key\":\"belongsTo\", \"value\":\"belongsTo\"}, {\"key\":\"belongsToMany\",\"value\":\"belongsToMany\"}, {\"key\":\"hasOne\",\"value\":\"hasOne\"}, {\"key\":\"hasMany\",\"value\":\"hasMany\"} ]}',NULL,'2018-01-15 03:27:02','2018-01-15 03:29:49',0,12),
(20,7,'crud','crud','relation','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,7,'2018-01-15 03:28:13','2018-01-15 03:29:49',0,12),
(21,7,'pivot','pivot','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Pivot',NULL,NULL,6,'2018-01-15 03:29:42','2018-01-15 03:29:49',0,12),
(22,6,'relation','relation','relation','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,8,'2018-01-15 03:31:04','2018-01-16 06:24:18',4,6),
(23,6,'visibility','Видимость','dropdown','[ \"browse\", \"edit\", \"add\" ]','[ \"browse\", \"edit\", \"add\" ]',0,0,NULL,'Основные параметры','required','{ \"mode\":\"multi\",\"values\": [ {\"key\":\"browse\", \"value\": \"browse\"}, {\"key\":\"add\", \"value\": \"add\"}, {\"key\":\"edit\", \"value\": \"edit\"} ]}',NULL,'2018-01-15 03:32:21','2018-01-16 06:24:18',5,6),
(24,6,'by_default','Значение по умолчанию','textbox','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-15 03:33:04','2018-01-16 06:24:18',6,6),
(25,6,'json','is json','checkbox','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 03:33:24','2018-01-16 06:24:18',11,6),
(26,6,'readonly','is readonly','checkbox','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 03:33:46','2018-01-16 06:24:18',12,6),
(27,6,'description','description','textarea','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-15 03:34:15','2018-01-16 06:24:18',14,12),
(28,6,'tab','tab','textbox','[\"add\",\"edit\"]','Основные параметры',0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 03:34:43','2018-01-16 06:24:19',8,6),
(29,6,'validation','validation','textbox','[\"add\",\"edit\"]','required',0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-15 03:35:16','2018-01-16 06:24:19',7,6),
(30,6,'additional','additional','textarea','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-15 03:35:28','2018-01-16 06:24:19',13,12),
(33,8,'name','name','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 05:57:29','2018-01-15 06:04:22',1,6),
(34,8,'caption','caption','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 05:57:43','2018-01-15 06:04:22',2,6),
(35,8,'action','action','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-15 05:58:10','2018-01-15 06:04:22',3,6),
(36,8,'status','status','dropdown','[\"add\",\"edit\"]','enabled',0,0,NULL,'Основные параметры','required','{\"mode\":\"single\", \"values\": [ {\"key\":\"disabled\", \"value\":\"disabled\"},{\"key\":\"enabled\", \"value\":\"enabled\"} ]}',NULL,'2018-01-15 06:00:48','2018-01-15 06:04:23',4,6),
(37,8,'order','order','textbox','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 06:01:07','2018-01-15 06:04:23',5,6),
(38,8,'default','is default','checkbox','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 06:01:54','2018-01-15 06:04:23',6,6),
(39,8,'parent','parent','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,9,'2018-01-15 06:04:19','2018-01-15 06:04:22',7,6),
(40,9,'code','Код','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 08:49:38','2018-01-15 09:55:28',0,12),
(41,9,'name','Название','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 08:49:48','2018-01-15 09:55:28',0,12),
(42,1,'password','Пароль','textbox','[\"add\",\"edit\"]',NULL,0,0,NULL,'Основные параметры',NULL,'{ \"mode\":\"password\"}',NULL,'2018-01-15 09:51:39','2018-01-15 10:01:14',0,12),
(43,1,'email','E-mail','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-15 09:52:17','2018-01-15 10:01:14',0,12),
(44,9,'users','Пользователи','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Пользователи',NULL,NULL,11,'2018-01-15 09:53:59','2018-01-15 09:55:27',0,12),
(45,9,'menus','Доступ к меню','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Доступ к меню',NULL,NULL,12,'2018-01-15 09:55:24','2018-01-15 09:55:27',0,12),
(46,10,'title','Заголовок','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required|string|max:191','{\"slugify\":\"url\"}',NULL,'2018-01-16 05:58:45','2018-01-16 06:00:40',1,6),
(47,10,'parent','Главное меню','relation','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,13,'2018-01-16 05:59:12','2018-01-16 06:00:39',3,6),
(48,10,'url','Ссылка','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-16 05:59:38','2018-01-16 06:00:40',2,6),
(49,10,'code','Код','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры',NULL,NULL,NULL,'2018-01-16 05:59:52','2018-01-16 06:00:40',4,6),
(50,11,'name','Название','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required|string|max:191',NULL,NULL,'2018-01-16 06:01:31','2018-01-16 06:02:02',0,12),
(51,11,'code','Код','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required|string|max:191',NULL,NULL,'2018-01-16 06:01:48','2018-01-16 06:02:02',0,12),
(52,12,'name','Название','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required|string|max:191',NULL,NULL,'2018-01-16 06:02:40','2018-01-16 06:02:56',0,12),
(53,12,'code','Код','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required|string|max:191',NULL,NULL,'2018-01-16 06:02:53','2018-01-16 06:02:55',0,12),
(54,13,'name','Название','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required|string|max:191',NULL,NULL,'2018-01-16 06:03:37','2018-01-16 06:15:52',0,12),
(55,13,'key','Ключ','textbox','[ \"browse\", \"edit\", \"add\" ]',NULL,0,0,NULL,'Основные параметры','required|string|max:191',NULL,NULL,'2018-01-16 06:03:55','2018-01-16 06:15:52',0,12),
(56,13,'adminSettingGroup','Группа настроек','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Настройки','required',NULL,14,'2018-01-16 06:04:45','2018-01-16 06:15:53',0,12),
(57,13,'crudFieldType','Тип поля','relation','[\"add\",\"edit\"]',NULL,0,0,NULL,'Настройки','required',NULL,15,'2018-01-16 06:05:18','2018-01-16 06:15:53',0,12),
(58,13,'value','Значение','dynamic','[\"browse\",\"edit\"]',NULL,0,0,NULL,'Основные параметры',NULL,'{\"type\":\"related\",\"from\":\"crudFieldType.code\"}',NULL,'2018-01-16 06:07:47','2018-01-16 06:15:52',0,12),
(59,6,'order','order','textbox','[\"add\",\"edit\"]','0',0,0,NULL,'Основные параметры','required',NULL,NULL,'2018-01-16 06:21:58','2018-01-16 06:24:17',9,6),
(60,6,'columns','columns','dropdown','[\"add\",\"edit\"]','12',0,0,NULL,'Основные параметры','required','{\"mode\":\"single\", \"values\": [ {\"key\": 12, \"value\":\"12\"}, {\"key\": 6, \"value\":\"6\"}, {\"key\": 4, \"value\":\"4\"}, {\"key\": 3, \"value\":\"3\"}]}',NULL,'2018-01-16 06:24:05','2018-01-16 06:24:17',10,6);

/*Data for the table `crud_forms` */

insert  into `crud_forms`(`sur_id`,`name`,`code`,`model`,`id`,`display`,`type`,`created_at`,`updated_at`) values 
(1,'Пользователи','users','App\\User','id','name','list','2018-01-12 04:45:33','2018-01-12 04:45:33'),
(3,'CRUD формы','crudforms','Vshapovalov\\Crud\\Models\\CrudForm','sur_id','name','list','2018-01-14 13:47:18','2018-01-14 13:47:18'),
(4,'CRUD Scope params','crudscopeparams','Vshapovalov\\Crud\\Models\\CrudScopeParam','id','name','list','2018-01-14 13:48:19','2018-01-14 13:48:19'),
(5,'CRUD Scopes','crudscopes','Vshapovalov\\Crud\\Models\\CrudScope','id','name','list','2018-01-14 13:50:05','2018-01-14 13:50:05'),
(6,'CRUD поля','crudfields','Vshapovalov\\Crud\\Models\\CrudField','id','name','list','2018-01-14 13:56:13','2018-01-14 13:56:13'),
(7,'CRUD связи','crudrelations','Vshapovalov\\Crud\\Models\\CrudRelation','id','crud.name','list','2018-01-15 03:29:48','2018-01-15 03:29:48'),
(8,'Меню админки','crudmenu','Vshapovalov\\Crud\\Models\\CrudMenu','id','name','list','2018-01-15 06:02:14','2018-01-15 06:02:14'),
(9,'Роли','roles','Vshapovalov\\Crud\\Models\\Role','id','name','list','2018-01-15 09:14:01','2018-01-15 09:14:01'),
(10,'Меню сайта','menuitems','Vshapovalov\\Crud\\Models\\MenuItem','id','title','tree','2018-01-16 05:59:58','2018-01-16 05:59:58'),
(11,'CRUD типы полей','crudfieldtypes','Vshapovalov\\Crud\\Models\\CrudFieldType','id','name','list','2018-01-16 06:02:01','2018-01-16 06:02:01'),
(12,'Группы настроек','adminsettinggroups','Vshapovalov\\Crud\\Models\\AdminSettingGroup','id','name','list','2018-01-16 06:02:55','2018-01-16 06:02:55'),
(13,'Настройки','adminsettings','Vshapovalov\\Crud\\Models\\AdminSetting','id','name','list','2018-01-16 06:07:57','2018-01-16 06:07:57');

/*Data for the table `crud_menus` */

insert  into `crud_menus`(`id`,`name`,`caption`,`action`,`default`,`parent_id`,`order`,`status`,`created_at`,`updated_at`) values 
(1,'media-library','Медиа библиотека','medialibrary:mount',0,NULL,1,'enabled','2018-01-11 12:09:44','2018-01-11 12:09:44'),
(2,'crudmenu','Меню админ.панели','crud:crudmenu:mount',0,3,2,'enabled','2018-01-11 12:10:04','2018-01-15 06:09:45'),
(3,'system-menu','Системные настройки',NULL,0,NULL,3,'enabled','2018-01-11 12:10:37','2018-01-11 12:10:37'),
(4,'menuitems','Меню сайта','crud:menuitems:mount',0,3,1,'enabled','2018-01-11 12:11:04','2018-01-11 12:11:04'),
(5,'crudfieldtypes','Типы полей','crud:crudfieldtypes:mount',0,3,2,'enabled','2018-01-11 12:11:27','2018-01-16 05:56:17'),
(6,'adminsettinggroups','Группы настроек','crud:adminsettinggroups:mount',0,3,3,'enabled','2018-01-11 12:11:50','2018-01-11 12:11:50'),
(7,'adminsettings','Настройки','crud:adminsettings:mount',0,NULL,4,'enabled','2018-01-11 12:12:15','2018-01-11 12:12:15'),
(9,'users','Пользователи','crud:users:mount',0,NULL,6,'enabled','2018-01-11 12:12:48','2018-01-11 12:12:48'),
(10,'roles','Роли','crud:roles:mount',0,NULL,8,'enabled','2018-01-11 12:13:08','2018-01-11 12:13:08'),
(13,'crudforms','CRUD формы','crud:crudforms:mount',0,3,13,'enabled','2018-01-11 12:13:47','2018-01-15 06:09:59');

/*Data for the table `crud_relations` */

insert  into `crud_relations`(`id`,`type`,`crud_form_id`,`created_at`,`updated_at`) values 
(1,'belongsToMany',2,'2018-01-13 06:05:35','2018-01-13 06:05:35'),
(4,'hasMany',4,'2018-01-14 13:49:50','2018-01-14 13:49:50'),
(5,'hasMany',5,'2018-01-14 13:50:48','2018-01-14 13:50:48'),
(6,'hasMany',6,'2018-01-15 03:21:19','2018-01-15 03:21:19'),
(7,'belongsTo',3,'2018-01-15 03:28:03','2018-01-15 03:28:03'),
(8,'belongsTo',7,'2018-01-15 03:30:55','2018-01-15 03:30:55'),
(9,'belongsTo',8,'2018-01-15 06:04:14','2018-01-15 06:04:14'),
(10,'belongsToMany',9,'2018-01-15 09:50:21','2018-01-15 09:50:21'),
(11,'belongsToMany',1,'2018-01-15 09:53:40','2018-01-15 09:53:40'),
(12,'belongsToMany',8,'2018-01-15 09:54:32','2018-01-15 09:54:32'),
(13,'belongsTo',10,'2018-01-16 06:00:34','2018-01-16 06:00:34'),
(14,'belongsTo',12,'2018-01-16 06:04:37','2018-01-16 06:04:37'),
(15,'belongsTo',11,'2018-01-16 06:05:16','2018-01-16 06:05:16');

/*Data for the table `crud_scope_params` */

/*Data for the table `crud_scopes` */

/*Data for the table `role_menu` */

/*Data for the table `roles` */

insert  into `roles`(`id`,`code`,`name`,`created_at`,`updated_at`) values 
(1,'admin','Администратор','2018-01-15 09:56:58','2018-01-15 09:56:58');

/*Data for the table `user_role` */

insert  into `user_role`(`user_id`,`role_id`,`meta`,`created_at`,`updated_at`) values 
(1,1,NULL,NULL,NULL);

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`password`,`remember_token`,`created_at`,`updated_at`) values 
(1,'admin','admin@admin.com','$2y$10$yXXgm2NZ5pEIFRl6U5C4sOiamvuMv6pC/5R/Cadr3Rr.UoYtxM.Q2','BQgTGZrZCrPzRJyIwFIDmaXcJLNy1Dxld2zJe2PoLMbuT8j2n7PMsBasKeRl','2017-11-05 07:24:34','2018-01-16 07:29:07');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

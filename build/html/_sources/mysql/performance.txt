性能测试
========

- 2017-05-13

.. contents:: 目录

建表
----

- 四张不同引擎，不同索引的表

  .. code-block:: sql
     :linenos:

      DROP TABLE IF EXISTS `time1`;
      CREATE TABLE `time1` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `num` int(11) DEFAULT NULL,
        `str` varchar(20) DEFAULT NULL,
        `dt` datetime DEFAULT NULL,
        `ts` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

      DROP TABLE IF EXISTS `time2`;
      CREATE TABLE `time2` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `num` int(11) DEFAULT NULL,
        `str` varchar(20) DEFAULT NULL,
        `dt` datetime DEFAULT NULL,
        `ts` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `num` (`num`),
        KEY `dt` (`dt`),
        KEY `ts` (`ts`)
      ) ENGINE=InnoDB DEFAULT CHARSET=utf8;

      DROP TABLE IF EXISTS `time3`;
      CREATE TABLE `time3` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `num` int(11) DEFAULT NULL,
        `str` varchar(20) DEFAULT NULL,
        `dt` datetime DEFAULT NULL,
        `ts` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

      DROP TABLE IF EXISTS `time4`;
      CREATE TABLE `time4` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `num` int(11) DEFAULT NULL,
        `str` varchar(20) DEFAULT NULL,
        `dt` datetime DEFAULT NULL,
        `ts` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
        PRIMARY KEY (`id`),
        KEY `num` (`num`),
        KEY `dt` (`dt`),
        KEY `ts` (`ts`)
      ) ENGINE=MyISAM DEFAULT CHARSET=utf8;

- 待续。。

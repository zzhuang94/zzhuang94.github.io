关于性能
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

初始化
------

- 导入测试数据，这里使用 ``php`` 脚本来向四张表中导入相同的1000万条相同的数据

  .. code-block:: php
     :linenos:

      <?php

      date_default_timezone_set('Asia/Shanghai');
      $table = $argv[1];

      $pdo = new PDO('mysql:host=localhost;dbname=zzhuang94', 'root', '');
      $unix = time();
      for ($j = 0; $j < 1000; $j ++) {
          $sql = "INSERT INTO $table (num, str, dt, ts) values ";
          for ($i = 0; $i < 10000; $i ++) {
              $unix ++;
              $time = date('Y-m-d H:i:s', $unix);
              $sql .= "($unix, 'zzhuang$unix', '$time', '$time'),";
          }
          $sql = rtrim($sql, ',');
          $pdo->exec($sql);
      }

- 执行数据导入，可以看到：

  - 对于不同引擎使用不同索引的表，插入完全相同的数据，真实执行时间 *(19.85s, 20.28s, 19.01s, 19.69s)* 相差无几
  
  .. code-block:: shell
     :linenos:

      [12:33:49] ~/code/php on git:master x $ time php mysql.php time1
      php mysql.php time1  19.85s user 0.79s system 14% cpu 2:23.38 total
      [12:20:37] ~/code/php on git:master x $ time php mysql.php time2
      php mysql.php time2  20.28s user 0.76s system 11% cpu 3:11.14 total
      [12:24:07] ~/code/php on git:master x $ time php mysql.php time3
      php mysql.php time3  19.01s user 0.73s system 20% cpu 1:37.04 total
      [12:26:26] ~/code/php on git:master x $ time php mysql.php time4
      php mysql.php time4  19.69s user 0.76s system 6% cpu 5:34.05 total

存储结构
--------

- 各表在磁盘上的真实存储，可以看到：

  - 相同的引擎，不使用索引占空间更小 *(time1:576M<time2:1016M;  time3:481M<time4:819M)*
  - 相同的索引，MyISAM引擎占空间更小 *(time3:481M<time1:576M;  time4:819M<time2:1016M)*
  - 都是以二进制文件的方式存储数据，但是不同的引擎数据存储方式不同

    - **Innodb** : ``.frm`` 存储表结构， ``.ibd`` 存储表数据 *（包括索引信息）*
    - **MyISAM** : ``.frm`` 存储表结构， ``.MYD`` 存储表数据， ``.MYI`` 存储索引信息 *（可以看到，time3和time4的MYD文件大小一样，而MYI文件time4确大了很多，是因为time3只有主键索引，而time4还有三个其它字段的索引）*

  .. code-block:: shell
     :linenos:

      [12:32:07] /var/lib/mysql/zzhuang94 $ l
      total 2.9G
      -rw-rw---- 1 mysql mysql    65 May 10 23:12 db.opt
      -rw-rw---- 1 mysql mysql  8.5K May 10 23:13 time1.frm
      -rw-rw---- 1 mysql mysql  576M May 20 12:36 time1.ibd
      -rw-rw---- 1 mysql mysql  8.5K May 10 23:13 time2.frm
      -rw-rw---- 1 mysql mysql 1016M May 20 12:24 time2.ibd
      -rw-rw---- 1 mysql mysql  8.5K May 10 23:13 time3.frm
      -rw-rw---- 1 mysql mysql  382M May 20 12:26 time3.MYD
      -rw-rw---- 1 mysql mysql   99M May 20 12:26 time3.MYI
      -rw-rw---- 1 mysql mysql  8.5K May 10 23:13 time4.frm
      -rw-rw---- 1 mysql mysql  382M May 20 12:32 time4.MYD
      -rw-rw---- 1 mysql mysql  437M May 20 12:32 time4.MYI

查询效率
--------

- 分别对四张表进行同样的查询操作，得到以下结论：

  - **MyISAM** 引擎会记录全表总记录数，不加条件查找 **COUNT(*)** 可以秒出结果，有条件限制也需要计算
  - 相同的索引情况， **MyISAM** 的查询效率要高于 **InnoDB**
  - **时间先后** 比较的场景，使用 **时间戳** 的效率要略高于 **datetime** ，且这两种类型的效率要远远高于 **timestamp**

.. csv-table::
   :header: "SELECT COUNT(*) FROM ${table} WHERE", "time1", "time2", "time3", "time4"
    
   "1", "3.78 sec", "3.20 sec", "0.00 sec", "0.00 sec"
   "`dt` > '2017-06-20' AND `dt` < '2017-08-20'", "5.45 sec", "4.60 sec", "5.37 sec", "3.68 sec"
   "`ts` > '2017-06-20' AND `ts` < '2017-08-20'", "18.30 sec", "12.29 sec", "17.76 sec", "12.45 sec"
   "`num` > 1497888000 AND `num` < 1503158400", "4.22 sec", "2.54 sec", "2.30 sec", "2.50 sec"

.. csv-table::
   :header: "SELECT MAX(num) FROM ${table} WHERE", "time1", "time2", "time3", "time4"
    
   "1", "5.07 sec", "0.07 sec", "2.29 sec", "0.09 sec"
   "`dt` > '2017-06-20' AND `dt` < '2017-08-20'", "6.25 sec", "7.30 sec", "3.51 sec", "3.83 sec"
   "`ts` > '2017-06-20' AND `ts` < '2017-08-20'", "18.97 sec", "26.22 sec", "16.98 sec", "18.48 sec"
   "`num` > 1497888000 AND `num` < 1503158400", "4.77 sec", "0.00 sec", "2.78 sec", "0.00 sec"

关于索引
--------

- 关于上面的测试数据，应该会发现一个明显的问题： **在同样的数据库引擎及同样的查询之下，使用索引并不是总是比不用索引快**

- 但是经过测试会发现：

  - *数据量小，使用索引明显效率提升*
  - *数据量大，看不出来使用索引与否与查询效率的关联*

- 大胆猜测： **如果查询数据量过大，MySQL则不走索引，直接全表查询**
  
- 借助 `EXPLAIN`_ 查看索引使用情况，印证上述猜测

  .. code-block:: sql
     :linenos:

      mysql> explain select * from time2 where dt > '2017-07-01';
      +----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
      | id | select_type | table | type | possible_keys | key  | key_len | ref  | rows    | Extra       |
      +----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
      |  1 | SIMPLE      | time2 | ALL  | dt            | NULL | NULL    | NULL | 9385444 | Using where |
      +----+-------------+-------+------+---------------+------+---------+------+---------+-------------+
      1 row in set (0.00 sec)
      
      mysql> explain select * from time2 where dt = '2017-07-01';
      +----+-------------+-------+------+---------------+------+---------+-------+------+-------+
      | id | select_type | table | type | possible_keys | key  | key_len | ref   | rows | Extra |
      +----+-------------+-------+------+---------------+------+---------+-------+------+-------+
      |  1 | SIMPLE      | time2 | ref  | dt            | dt   | 6       | const |    1 | NULL  |
      +----+-------------+-------+------+---------------+------+---------+-------+------+-------+
      1 row in set (0.00 sec)


- MySQL官方文档， `B-Tree Index Characteristics`_ 有下面这段话 :)

 .. image:: asset/btree-index.png
    :scale: 90%

.. _EXPLAIN: https://dev.mysql.com/doc/refman/5.6/en/explain-output.html
.. _B-Tree Index Characteristics: https://dev.mysql.com/doc/refman/5.6/en/index-btree-hash.html#btree-index-characteristics

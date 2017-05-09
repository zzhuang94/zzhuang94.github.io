时区 datetime timestamp
=======================

+ 2017-05-09

.. contents:: 目录

MySQL时区
---------

- 查看时区

   .. code-block:: sql
      :linenos:

      mysql> show variables like "%time_zone%";
      +------------------+--------+
      | Variable_name    | Value  |
      +------------------+--------+
      | system_time_zone | CST    |
      | time_zone        | SYSTEM |
      +------------------+--------+
      2 rows in set (0.00 sec)
 
  + **system_time_zone** 表示当前机器的时区 `(CST: China Standard Time)`
  + **time_zone** 表示MySQL的时区 `(SYSTEM: 使用系统时区，即CST)`
  + 有时会发现使用 ``FROM_UNIXTIME`` 转换时间戳，得到的结果和预期不一致，那么极有可能是时区不同导致

- 修改时区

  + 临时修改， ``mysqld`` 服务重启即失效

    .. code-block:: sql
       :linenos:

       mysql> -- 设置当前会话，不影响其它会话
       mysql> set time_zone = '+8:00';
       mysql> -- 可在程序中临时使用
       mysql> -- 如： mysql_query("SET time_zone = '+8:00'")

       mysql> -- 设置当前会话及新创建的会话
       mysql> set global time_zone = '+8:00';

  + 永久修改，需要修改 ``my.cnf`` 配置文件
    
    .. code-block:: sh
       :linenos:

       # vim /etc/my.cnf
       # [mysqld]中添加
       default-time_zone = '+8:00'
       # 重启MySQL服务，即永久生效
       service mysqld restart


datetime VS timestamp
---------------------

- **datetime** 和 **timestamp** 都是用于存储时间字符串的类型

  + 范围

    + **datetime** `1000-01-01 00:00:00.000000 ~ 9999-12-31 23:59:59.999999`
    + **timestamp** `1970-01-01 00:00:01.000000 ~ 2038-01-19 03:14:07.999999`

  + 存储格式

    + **datetime**  8字节，存储实际格式，与MySQL时区无关
    + **timestamp** 4字节，原始值根据时区自动转换成UTC存储，读取时再根据时区从UTC转换

  + 自动更新

    + **datetime**  需手动注明需要自动更新 `(NOT NULL DEFAULT CURRENT_TIMESTAMP)`
    + **timestamp** 默认在所在 ``row`` 数据更改时，自动更新为当前时间

- 效率问题

   .. image:: asset/search-speed.png
      :scale: 90%

  + 对于 **MyISAM** 引擎，采用 **UNIX_TIMESTAMP(timestamp)** 比较
  + 对于 **InnoDB** 引擎，建立索引，采用 **int** 或 **datetime** 直接比较时间字符串

- 在以上两种类型之间，个人倾向于使用 **datetime** ，范围大，不受时区影响，
  如果需要考虑到效率问题，可以直接使用 **int**

参考文档
--------

- `飘易博客 <http://www.piaoyi.org/database/MYSQL-INT-TIMESTAMP-DATETIME.html>`_

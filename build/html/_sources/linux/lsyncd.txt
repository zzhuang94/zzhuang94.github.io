使用lsyncd同步文件
##################

- 2017-09-24

.. contents:: 目录

install
-------

- yum install rsync lua lua-devel lsyncd -y

conf
----

- vi /etc/lsyncd.conf

  .. code-block:: shell
     :linenos:

      settings {
          logfile      = "/var/lsyncd.log",
          statusFile   = "/var/lsyncd.status",
          inotifyMode  = "Modify",
          maxProcesses = 7,
          -- nodaemon  = true,
      }
      
      sync {
          default.rsync,
          source          = "/root/code",
          target          = "/root/workspace/desk",
          delay           = 0,
          maxProcesses    = 1,
          rsync           = {
              binary      = "/usr/bin/rsync",
              archive     = true,
              compress    = true,
              verbose     = true
          }
      }

go
--

- lsyncd -log Exec /etc/lsyncd.conf

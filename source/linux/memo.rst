备忘录
======

- 2017-05-20

.. contents:: 目录

时间戳与时间字符串
------------------

- 获取当前时间戳

  .. code-block:: shell
     :linenos:

      [13:32:27] ~ $ date +%s   
      1495258357

- 时间戳转字符串

  .. code-block:: shell
     :linenos:

      [13:32:37] ~ $ date -d @1495258357
      Sat May 20 13:32:37 CST 2017
      [13:44:46] ~ $ date -d "1970-01-01 UTC 1495228800 seconds" "+%F %T"
      2017-05-20 05:20:00
      [13:44:55] ~ $ date -d "1970-01-01 CST 1495228800 seconds" "+%F %T"
      2017-05-19 21:20:00
      [13:45:01] ~ $ date -d "1970-01-01 CST 1495228800 seconds" "+%F"   
      2017-05-19
      [13:45:06] ~ $ date -d "1970-01-01 CST 1495228800 seconds" "+%T"
      21:20:00


- 时间字符串转时间戳

  .. code-block:: shell
     :linenos:

      [13:38:05] ~ $ date +%s -d'2017-05-20 05:20:00'
      1495228800
      [13:40:17] ~ $ date +%s -d '2017-05-20 05:20:00 CST'
      1495228800
      [13:40:21] ~ $ date +%s -d '2017-05-20 05:20:00 UTC'
      1495257600

ARGS 参数传递
-------------

- xargs 多参数传递

  .. code-block:: shell
     :linenos:

      [10:55:18] ~/files $ ls *.json | xargs -I% cp "%" "%".bak

GIT
---

- 本地分支推送到远端

  .. code-block:: shell
     :linenos:

      [11:14:14] ~/docs on git:master x $ git push origin my-branch

jq
--

- JSON格式化

  .. code-block:: shell
     :linenos:

      [19:33:13] ~/code/shell/jq on git:master x $ cat json.json                                          
      {"hzz":{"kobe":[33,8,10,24],"james":[["23","6","23"]]},"src":[{"zzhuang94":100,"kobe.bryant":99,"www.zzhuang94.com":{"github":"github","github1":"github1","github2":"github2"}}]}#                                                            
      [19:33:17] ~/code/shell/jq on git:master x $ cat json.json | jq                                     
      {
          "hzz": {
              "kobe": [
                  33,
                  8,
                  10,
                  24
              ],
              "james": [
                  [
                      "23",
                      "6",
                      "23"
                  ]
              ]
          },
          "src": [
              {
                  "zzhuang94": 100,
                  "kobe.bryant": 99,
                  "www.zzhuang94.com": {
                      "github": "github",
                      "github1": "github1",
                      "github2": "github2"
                  }
              }
          ]
      }
      [19:33:19] ~/code/shell/jq on git:master x $ cat json.json | jq 'keys'                              
      [
          "hzz",
          "src"
      ]
      [19:33:35] ~/code/shell/jq on git:master x $ cat json.json | jq 'keys[]'
      "hzz"
      "src"
      [19:33:41] ~/code/shell/jq on git:master x $ cat json.json | jq '.["hzz"]' | jq 'keys'
      [
          "james",
          "kobe"
      ]
      [19:33:53] ~/code/shell/jq on git:master x $ cat json.json | jq '.["hzz"]["kobe"]'    
      [
          33,
          8,
          10,
          24
      ]
      [19:34:06] ~/code/shell/jq on git:master x $ cat json.json | jq '.["hzz"]["kobe"][]'
      33
      8
      10
      24
      [19:34:08] ~/code/shell/jq on git:master x $ cat json.json | jq '.["hzz"][][]'      
      33
      8
      10
      24
      [
          "23",
          "6",
          "23"
      ]
      [19:34:12] ~/code/shell/jq on git:master x $ cat json.json | jq '.["hzz"][][][]'
      jq: error (at <stdin>:0): Cannot iterate over number (33)
      C:5[19:34:15] ~/code/shell/jq on git:master x $ cat json.json | jq '.["hzz"][][][]?'
      "23"
      "6"
      "23"
      [19:34:21] ~/code/shell/jq on git:master x $ cat json.json | jq '.["hzz"][][][]?'

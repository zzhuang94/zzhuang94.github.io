个人博客搭建
============

+ 2017-05-08

.. contents:: 目录

简介
----

- 基于 `Github Pages`_ 和 `Sphinx`_ 使用 `rst`_ 文档搭建个人博客

- 效果图

.. image:: asset/blog.png
   :scale: 90%

Github Pages 个人博客创建
-------------------------

- Github个人首页， **New repository** *(后文所有提到的username均为自己的GitHub账号)*

.. image:: asset/new-repository.png
   :scale: 90%

- repository name 为 **username.github.io** 

- 本地创建git目录，并推送到GitHub服务器

.. code-block:: sh
   :linenos:

    mkdir username.github.io
    cd username.github.io
    echo "this is my blog index" > index.html
    git init
    git add .
    git commit -m "first commit"
    git remote add origin https://github.com/username/username.github.io.git
    git push -u origin master

- 访问 https://username.github.io 检测个人博客是否搭建成功

Sphinx搭建静态网站
------------------

- 安装 **Sphinx**
  
.. code-block:: sh
   :linenos:

    pip install sphinx

- 进入 ``username.github.io`` , 开始 **Sphinx** 初始化
  
.. code-block:: sh
   :linenos:

    sphinx-quickstart
    # 根据自己需要，一路回车

- 修改 ``source`` 目录下的rst文件，然后编译
  
.. code-block:: sh
   :linenos:

    # 建议使用 sphinx_rtd_theme 主题 
    # vim source/conf.py
    # 修改 html_theme

    make html

- 用浏览器打开 ``build/html/index.html``  检查静态网站是否搭建成功
  *(如果初始化Sphinx时没有选择 Separate source and build directories ， 则是 _build 目录)*

Sphinx对接GitHub Pages
----------------------

- 由于 **GitHub Pages** 默认使用 ``index.html`` 文件，所以需要修改 ``index.html`` 使之自动跳转到 ``build/html/index.html``

.. code-block:: html
    :linenos:

    <script language='javascript'>document.location = 'build/html/index.html'</script>

- 还有一个很坑的地方， **Github Pages** 找不到下划线开头的文件，
  需要添加 ``.nojekyll`` 文件来屏蔽此限制

- 将整个项目push到 ``GitHub`` ，即完成了 ``Github Pages`` + ``Sphinx`` 搭建个人博客


参考文档
--------

- `reStructuredText(rst)快速入门语法说明 <http://www.cnblogs.com/seayxu/p/5603876.html>`_
- `reStructuredText快速入门 <http://www.jianshu.com/p/f60e9be4781d>`_
- `使用sphinx生成美观的文档 <http://blog.csdn.net/handsomekang/article/details/46778895>`_

.. _Github Pages: https://pages.github.com/
.. _Sphinx: http://sphinx-doc.org/
.. _rst: http://zh-sphinx-doc.readthedocs.io/en/latest/rest.html

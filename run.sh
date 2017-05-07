#!/bin/bash

# 由于 github pages 无法识别下划线开头的的文件夹
# 需要转换文件夹名称

rm -rf build

make html

mv build/html/_static build/html/static
sed -i "s/_static/static/g" `grep "<script" -rl build/html`
sed -i "s/_static/static/g" `grep "<link" -rl build/html`

mv build/html/_sources build/html/sources
sed -i "s/_sources/sources/g" `grep "_sources" -rl build/html`

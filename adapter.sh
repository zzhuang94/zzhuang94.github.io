#!/bin/bash

# 由于 github pages 无法识别下划线开头的的文件夹
# 需要转换文件夹名称

rm -rf build

make html

mv build/html/_static build/html/static
reg="^\s*<script.*</script>$"
sed -i "/${reg}/s/_static/static/g" `grep "${reg}" -rl build/html`
sed -i "s/_static/static/g" `grep "^\s*<link.*stylesheet.*>$" -rl build/html`

mv build/html/_sources build/html/sources
sed -i "s/_sources/sources/g" `grep "\s*<a href=\".* View page source</a>" -rl build/html`
sed -i "s/_sources/sources/g" `grep "\s*\$.ajax({url: DOCUMENTATION_OPTIONS.URL_ROOT})" -rl build/html`

mv build/html/_images build/html/images
sed -i "s/_images/images/g" `grep ".*image-reference\" href=\".*\.png.*" -rl build/html`

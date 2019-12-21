#!/bin/bash

read -p "请输入一个字符 : " KEY

case "$KEY" in
[a-z]|[A-Z])
    echo "这是字母!"
;;
[0-9])
    echo "这是数字!"
;;
*)
    echo "你这是在为难我胖虎!"
esac



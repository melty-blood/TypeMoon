#!/bin/bash
ping -c 3 -i 0.2 -W 3 $1 &> /dev/null
# &> /dev/null 作用将命令输出的结果扔进balck_hole File

if [ $? -eq 0 ]
    then echo "HOST $1 is On-Line.$?-$0"
else 
    echo "Host $1 Is Off-Line."
fi

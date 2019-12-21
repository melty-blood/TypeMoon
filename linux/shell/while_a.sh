#!/bin/bash
PRICE=$(expr $RANDOM % 1000)
TIMES=0
GAMETAG="高手高手高手～～～～～"

echo "商品实际价格是0-999之间, 猜猜是多少"

while true
do
    read -p "请输入猜测的价格 : " INT
    let TIMES++
    if [ $INT -eq $PRICE ]
    then
        if [ $TIMES -ge 10 ] && [ $TIMES -le 15 ]
        then
            GAMETAG="继续努力!"
        elif [ $TIMES -ge 16 ] && [ $TIMES -le 20 ]
        then
            GAMETAG="不行啊少年!"
        elif [ $TIMES -ge 21 ] && [ $TIMES -le 25 ]
        then
            GAMETAG= "寀鸡一枚!"
        elif [ $TIMES -ge 26 ]
        then
            GAMETAG="你是蔡许坤吗? 来送你一个篮球!"
        fi

        echo "你竟然猜对了, 实际价格 $PRICE"
	echo "总共猜 $TIMES 次! -- $GAMETAG"
        exit 0

    elif [ $INT -gt $PRICE ]
    then
        echo "太高了"
    else 
        echo "太低了"
    fi
done


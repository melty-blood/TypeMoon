#!/bin/bash
cat /var/log/secure|awk '/Failed/{print $(NF-3)}'|sort|uniq -c|awk '{print $2"="$1;}' > /root/black.txt
# 定义攻击次数,2次密码输错就视为攻击
DEFINE="2"
for i in `cat ./black.txt`
do
    IP=`echo ${i}|awk -F= '{print $1}'`
    NUM=`echo ${i}|awk -F= '{print $2}'`
    if [ ${NUM} -gt ${DEFINE} ];then
        sed -i '/'${IP}'/d' /etc/hosts.deny
        if [ $? > 0 ];then
            echo "sshd:$IP:deny" >> /etc/hosts.deny
        fi
    fi
done
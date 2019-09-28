#!/bin/bash
# Rotate the nginx logs to prevent a single logfile from consuming too much disk space.以防万一用root身份来进行

LOGS_PATH=/usr/local/nginx/logs/history
CUR_LOGS_PATH=/usr/local/nginx/logs
YESTERDAY=$(date -d "yesterday" +%Y-%m-%d-%H-%i)
mv $(CUR_LOGS_PATH)/acg_access.log $(LOGS_PATH)/hime_acg_access_${YESTERDAY}.log
mv $(CUR_LOGS_PATH)/error.log $(LOGS_PATH)/hime_error_${YESTERDAY}.log
# mv $(CUR_LOGS_PATH)/access.log $(LOGS_PATH)/hime_access_${YESTERDAY}.log
# 向nginx主进程发送USR1信号，USR1信号是重新打开日志文件  nginx.pid主进程(master)id
kill -USR1 $(cat /usr/local/nginx/logs/nginx.pid)
#!/bin/bash
read -p "Enter The User Password : " PASSWD

for UNAME in `cat for_example.txt`
do
    id $UNAME &> /dev/null
    if [ $? -eq 0 ]
    then 
        echo "User Has System!"
    else
        echo "Not has! You can useradd $UNAME \n"
        echo "$UNAME - $PASSWD"
    fi
done


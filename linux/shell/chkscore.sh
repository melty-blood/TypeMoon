#!/bin/bash
read -p "Enter your score (0-100) : " GRADE
if [ $GRADE -lt 0 ] || [ $GRADE -gt 100 ]
    then 
        echo "$GRADE is Outrange!"
        exit 1
fi

if [ $GRADE -ge 85 ] && [ $GRADE -le 100 ] ; then
    echo "$GRADE is Excellent"
elif [ $GRADE -ge 70 ] && [ $GRADE -le 84 ] ; then
    echo "$GRADE is Pass"
else
    echo "$GRADE is Bad"
fi

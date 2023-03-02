#!/bin/bash
function mq_bill() {
  COUNT=$(ps -ef | grep 'php artisan mq:bill' | grep -v "grep" | wc -l)

  if [ $COUNT -eq 0 ]; then
    cd ../
    nohup php artisan mq:bill >/dev/null 2>&1 &
  fi
}
mq_bill

function mq_account() {
  COUNT=$(ps -ef | grep 'php artisan mq:account' | grep -v "grep" | wc -l)

  if [ $COUNT -eq 0 ]; then
    cd ../
    nohup php artisan mq:account >/dev/null 2>&1 &
  fi
}
mq_account

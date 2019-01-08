#!/bin/sh

rm -rf /home/ec2-user/was/dapos.dabaesong.co.kr/git*
cd /home/ec2-user/was/dapos.dabaesong.co.kr

if [ $1 = P ]
then
  cp .env.prod .env
else
  cp .env.dev .env
fi

if [ ! -d /home/ec2-user/was/dapos.dabaesong.co.kr/bootstrap/cache ]
then
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/bootstrap/cache
fi

if [ ! -d /home/ec2-user/was/dapos.dabaesong.co.kr/storage ]
then
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/logs
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework/cache
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework/sessions
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework/views
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/app

  chmod -R 777 ./storage/
fi

if [ ! -d /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework ]
then
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework/cache
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework/sessions
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/framework/views
fi

if [ ! -d /home/ec2-user/was/dapos.dabaesong.co.kr/storage/app ]
then
  mkdir /home/ec2-user/was/dapos.dabaesong.co.kr/storage/app
fi

if [ ! -d /home/ec2-user/was/dapos.dabaesong.co.kr/vender ]
then
  composer update
  php artisan optimize --force
  php artisan config:clear
  php artisan route:clear
  php artisan view:clear
else
  composer install
fi

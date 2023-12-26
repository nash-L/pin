# shellcheck disable=SC1113
#/usr/bin/env sh

function installPhp() {
  type php > /dev/null 2>&1
  php_exists=$?
  if [ "$php_exists" == '0' ]; then
    php --ri swoole > /dev/null 2>&1
    sw_exists=$?
    if [ "$sw_exists" != '0' ]; then
      php_exists=1
    fi
  fi
  if [ "$php_exists" != '0' ]; then
    arch_name=$(uname -m)
    if [ "$arch_name" == 'x86_64' ]; then
      wget -O swoole-cli.tar.xz https://wenda-1252906962.file.myqcloud.com/dist/swoole-cli-v5.1.0-linux-x64.tar.xz
    elif [ "$arch_name" == 'aarch64' ]; then
      wget -O swoole-cli.tar.xz https://wenda-1252906962.file.myqcloud.com/dist/swoole-cli-v5.0.3-linux-arm64.tar.xz
    fi
    xz -d swoole-cli.tar.xz
    tar -xvf swoole-cli.tar
    mv swoole-cli /usr/bin/php
    chmod +x /usr/bin/php
    rm -rf swoole-cli.tar LICENSE swoole-cli.tar.xz pack-sfx.php
  fi
}

function installComposer() {
  type composer > /dev/null 2>&1
  composer_exists=$?
  if [ "$composer_exists" != '0' ]; then
    wget -O /usr/bin/composer https://mirrors.tencent.com/composer/composer.phar
    chmod +x /usr/bin/composer
    composer config -g repos.packagist composer https://mirrors.tencent.com/composer/
  fi
}

installPhp

installComposer

php $(cd $(dirname $0); pwd)/boot.php start

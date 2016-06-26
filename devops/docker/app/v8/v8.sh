#!/usr/bin/env bash

# compile v8 and php-v8js extension
# see https://github.com/phpv8/v8js/blob/master/README.Linux.md

set -e
set -o pipefail

# -----------------------------------------------------------------------

# Install `build-essential` if you haven't already:
apt-get install -y build-essential

# Install `libicu-dev` if you haven't already:
apt-get install -y libicu-dev

cd /tmp

# Install depot_tools first (needed for source checkout)
git clone --depth=1 https://chromium.googlesource.com/chromium/tools/depot_tools.git
export PATH=`pwd`/depot_tools:"$PATH"

# Download v8
fetch v8
cd v8

# (optional) If you'd like to build a certain version:
#git checkout 4.9.385.28
#gclient sync

# use libicu of operating system
export GYP_DEFINES="use_system_icu=1"

# Build (with internal snapshots)
export GYPFLAGS="-Dv8_use_external_startup_data=0"

# Force gyp to use system-wide ld.gold
export GYPFLAGS="${GYPFLAGS} -Dlinux_use_bundled_gold=0"

make native library=shared snapshot=on -j8

# Install to /usr
mkdir -p /usr/lib /usr/include
cp out/native/lib.target/lib*.so /usr/lib/
cp -R include/* /usr/include

# Install libv8_libplatform.a (V8 >= 5.2.51)
echo -e "create /usr/lib/libv8_libplatform.a\naddlib out/native/obj.target/src/libv8_libplatform.a\nsave\nend" | sudo ar -M

# ... same for V8 < 5.2.51, libv8_libplatform.a is built in tools/gyp directory
echo -e "create /usr/lib/libv8_libplatform.a\naddlib out/native/obj.target/tools/gyp/libv8_libplatform.a\nsave\nend" | sudo ar -M

# -----------------------------------------------------------------------

cd /tmp
git clone https://github.com/phpv8/v8js.git
cd v8js
phpize
./configure
make
make test
make install

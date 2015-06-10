PACKAGE := famous
INSTALL_PATH := /var/www/famous
CONFIG_PATH := /var/conf/famous
LONG_DESCRIPTION := Famous People Disease
MAINTAINER := Guy Bar-Nahum <guy@barnahum.com>
DEPENDS := php5 (>= 5.3.0)
ARCH := all

# 
# be sure to modify the mkbuild section below to set up the package 
# files correctly
# 

VERSION := "1.0"
DATE := $(shell date -u +%Y%m%d%H%M$S)

CONTROL_FILE := "Package: $(PACKAGE_NAME)\nVersion: $(VERSION)\nSection: php\nPriority: optional\nArchitecture: $(ARCH)\nEssential: no\nDepends: $(DEPENDS)\nMaintainer: $(MAINTAINER)\nProvides: $(PACKAGE_NAME)\nDescription: $(LONG_DESCRIPTION)\n"

test: mkbuild
	@echo Done
	fakeroot dpkg -b build $(PACKAGE).deb
	@rm -rf build
	@echo Done. Package version is $(VERSION)

#main:
#	@echo Building package
#	fakeroot dpkg -b build $(PACKAGE).deb;
#	@rm -rf build
#	@echo Done. Package version is $(VERSION)

mkbuild:
	@echo Creating new build environment
	@rm -rf build

	@mkdir -p build$(INSTALL_PATH)
	@find . |grep -Ev '\.git' | cpio -dumpv ./build$(INSTALL_PATH);# cd -;

	@cp DEBIAN		build/ -r

clean:
	@echo Cleaning up junk
	@rm -rf build
	@rm -f *.deb
	@echo Done

install:
	sudo dpkg -i $(PACKAGE)*.deb;

env:
	mkdir -p DEBIAN
	echo $(CONTROL_FILE) > DEBIAN/control
	touch DEBIAN/conffiles.ex
	touch DEBIAN/crond.d.ex
	touch DEBIAN/postinst.ex
	touch DEBIAN/preinst.ex
	touch DEBIAN/postrm.ex
	touch DEBIAN/prerm.ex

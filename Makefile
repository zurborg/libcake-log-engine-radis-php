phpdoc=vendor/phpdocumentor/phpdocumentor/bin/phpdoc
phpdocmd=vendor/evert/phpdoc-md/bin/phpdocmd
yaml2json=/usr/bin/perl -MJSON -MYAML -eprint -e'encode_json(YAML::Load(join""=><>))'
composer=./composer.phar

all: | composer.json documentation

documentation:
	-git rm -f docs/structure.xml
	$(phpdoc) -d src/ -t docs/ --template=xml
	git add docs/structure.xml
	-git rm -f docs/*.md
	$(phpdocmd) docs/structure.xml docs/
	git add docs/*.md

clean:
	git clean -xdf -e composer.phar -e vendor

prepare:
	-rm $(composer)*
	wget https://getcomposer.org/composer.phar -O $(composer)
	chmod +x $(composer)
	-rm -rf vendor
	$(composer) install

composer.json: composer.yaml
	$(yaml2json) < $< > $@
	git add $@

archive: | clean composer.json
	$(composer) archive

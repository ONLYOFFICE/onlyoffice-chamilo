#!/bin/bash

rm -rf ./deploy
rm ./RELEASE.md

cwd=$(pwd)
git submodule update --init --recursive

mkdir ./deploy
rsync -av --exclude='deploy' ./ ./deploy/
cd ./deploy

rm -rf ./.github
rm -rf ./.git/
rm ./.gitignore
rm ./pack.sh

cd $cwd
awk '/## [0-9]/{p++} p; /## [0-9]/{if (p > 1) exit}' CHANGELOG.md | awk 'NR>2 {print last} {last=$0}' > RELEASE.md
zip onlyoffice.zip -r deploy
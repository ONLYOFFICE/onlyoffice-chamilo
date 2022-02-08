#!/bin/bash

rm -rf ./onlyoffice
rm ./RELEASE.md

cwd=$(pwd)
git submodule update --init --recursive

mkdir ./onlyoffice
rsync -av --exclude='onlyoffice' ./ ./onlyoffice/
cd ./onlyoffice

rm -rf ./.github
rm -rf ./.git/
rm ./.gitignore
rm ./pack.sh
rm ./.gitmodules

cd $cwd
awk '/## [0-9]/{p++} p; /## [0-9]/{if (p > 1) exit}' CHANGELOG.md | awk 'NR>2 {print last} {last=$0}' > RELEASE.md